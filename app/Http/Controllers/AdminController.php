<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Pemesanan;
use App\Models\User;
use App\Models\Pengaturan;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $activeArenaId = active_arena()->id;

        // 1. Auto-Cancel Reservasi Pending yang sudah lewat 10 menit
        $expiredPemesanans = Pemesanan::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(10))
            ->with('detail')
            ->get();

        foreach ($expiredPemesanans as $p) {
            $p->update(['status' => 'batal']);
            foreach ($p->detail as $detail) {
                \App\Models\Jadwal::where('id', $detail->jadwal_id)->update(['status' => 'tersedia']);
            }
        }

        // 2. Data Statistik tersaring berdasarkan Cabang Aktif dan Jadwal Hari Ini
        $lapanganAktif = Lapangan::where('pengaturan_id', $activeArenaId)
            ->where('status', 'aktif')
            ->where(function ($query) {
                // Lapangan dianggap aktif hari ini JIKA:
                // 1. Belum ada jadwal yang digenerate untuk hari ini
                $query->whereDoesntHave('jadwal', function($q2) {
                    $q2->where('tanggal', today()->toDateString());
                })
                // 2. ATAU ada jadwal hari ini, tapi ada minimal satu jam yang berstatus 'tersedia' atau 'proses'
                ->orWhereHas('jadwal', function($q3) {
                    $q3->where('tanggal', today()->toDateString())
                       ->whereIn('status', ['tersedia', 'proses']);
                });
            })
            ->count();

        $reservasiHariIni = Pemesanan::whereHas('detail.lapangan', function($q) use ($activeArenaId) {
                $q->where('pengaturan_id', $activeArenaId);
            })
            ->whereDate('created_at', today())
            ->where('status', '!=', 'batal')
            ->count();

        $pending = Pemesanan::whereHas('detail.lapangan', function($q) use ($activeArenaId) {
                $q->where('pengaturan_id', $activeArenaId);
            })
            ->whereIn('status', ['proses', 'pending'])
            ->count();

        $pemesananTerbaru = Pemesanan::whereHas('detail', function ($q) use ($activeArenaId) {
                $q->where('tanggal', today()->toDateString())
                  ->where('jam_selesai', '>', now()->format('H:i:s'))
                  ->whereHas('lapangan', function($q2) use ($activeArenaId) {
                      $q2->where('pengaturan_id', $activeArenaId);
                  });
            })
            ->where('status', '!=', 'batal')
            ->orderBy('created_at', 'desc')
            ->get();

        $chartLabels = [];
        $chartData = [];

        for ($i = -7; $i <= 30; $i++) {
            $date = \Carbon\Carbon::today()->addDays($i);
            $chartLabels[] = $date->translatedFormat('d M'); 
            $chartData[] = Pemesanan::whereHas('detail.lapangan', function($q) use ($activeArenaId) {
                $q->where('pengaturan_id', $activeArenaId);
            })
            ->whereDate('tanggal_mulai', $date)
            ->where('status', 'berhasil')
            ->count();
        }

        return view('admin.dashboard', compact(
            'lapanganAktif',
            'reservasiHariIni',
            'pending',
            'pemesananTerbaru',
            'chartLabels',
            'chartData'
        ));
    }

    public function pemesanan(Request $request)
    {
        // Auto-Cancel Reservasi Pending yang lewat 10 menit
        $expiredPemesanans = Pemesanan::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes(10))
            ->with('detail')
            ->get();

        foreach ($expiredPemesanans as $p) {
            $p->update(['status' => 'batal']);
            foreach ($p->detail as $detail) {
                if ($detail->jadwal_id) {
                    \App\Models\Jadwal::where('id', $detail->jadwal_id)->update(['status' => 'tersedia']);
                }
            }
        }

        $activeArenaId = active_arena()->id;
        $status = $request->query('status', 'semua');
        $search = trim($request->query('search'));

        $query = Pemesanan::with([
            'user',
            'detail.lapangan'
        ])
        ->whereHas('detail.lapangan', function($q) use ($activeArenaId) {
            $q->where('pengaturan_id', $activeArenaId);
        });

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        if ($search !== '' && $search !== null) {
            $query->where(function($q) use ($search) {
                $q->where('kode_reservasi', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $pemesanan = $query->latest()->paginate(15)->withQueryString();

        return view('admin.pemesanan.index', compact('pemesanan', 'status', 'search'));
    }

    public function konfirmasiPemesanan($id)
    {
        \Illuminate\Support\Facades\DB::transaction(function() use ($id) {
            $pemesanan = Pemesanan::with('detail.lapangan.pengaturan')->findOrFail($id);

            /* // KODE PHP: Otomatis alihkan cabang/arena aktif ke arena milik pemesanan ini */
            $firstDetail = $pemesanan->detail->first();
            if ($firstDetail && $firstDetail->lapangan && $firstDetail->lapangan->pengaturan) {
                session(['active_arena_slug' => $firstDetail->lapangan->pengaturan->slug]);
            }

            $pemesanan->status = 'berhasil';
            $pemesanan->save();

            foreach ($pemesanan->detail as $detail) {
                if ($detail->jadwal_id) {
                    \App\Models\Jadwal::where('id', $detail->jadwal_id)->update(['status' => 'berhasil']);
                }
            }

            $harga = $pemesanan->jenis_user == 'member' 
                ? (active_arena()->member_harga ?? 1000000) 
                : ($pemesanan->durasi * (active_arena()->harga_per_jam ?? 80000));

            \App\Models\Pembayaran::firstOrCreate(
                ['pemesanan_id' => $pemesanan->id],
                [
                    'metode' => 'QRIS/Transfer',
                    'nominal' => $harga,
                    'status' => 'berhasil',
                    'waktu_bayar' => now()
                ]
            );

            $kodeTiket = 'TKT-' . strtoupper(\Illuminate\Support\Str::random(8));
            \App\Models\Tiket::firstOrCreate(
                ['pemesanan_id' => $pemesanan->id],
                [
                    'kode_tiket' => $kodeTiket,
                    'qr_code' => $kodeTiket,
                    'status' => 'valid'
                ]
            );
        });

        return back()->with('success', 'Pemesanan berhasil dikonfirmasi (Lunas).');
    }

    public function tolakPemesanan(Request $request, $id)
    {
        $alasan = $request->input('alasan_penolakan') ?: 'Bukti transfer tidak valid atau pembayaran tidak sesuai.';

        \Illuminate\Support\Facades\DB::transaction(function() use ($id, $alasan) {
            $pemesanan = Pemesanan::with('detail.lapangan.pengaturan')->findOrFail($id);

            /* // KODE PHP: Otomatis alihkan cabang/arena aktif ke arena milik pemesanan ini */
            $firstDetail = $pemesanan->detail->first();
            if ($firstDetail && $firstDetail->lapangan && $firstDetail->lapangan->pengaturan) {
                session(['active_arena_slug' => $firstDetail->lapangan->pengaturan->slug]);
            }

            $pemesanan->status = 'batal';
            $pemesanan->alasan_penolakan = $alasan;
            $pemesanan->save();

            foreach ($pemesanan->detail as $detail) {
                if ($detail->jadwal_id) {
                    \App\Models\Jadwal::where('id', $detail->jadwal_id)->update(['status' => 'tersedia']);
                }
            }
        });

        return back()->with('success', 'Pemesanan telah ditolak dengan alasan yang tercatat.');
    }

    public function uploadQrisStatis(Request $request)
    {
        $request->validate([
            'qris_image' => 'required|image|mimes:jpeg,png,jpg,webp|max:5048'
        ], [
            'qris_image.required' => 'Pilih foto/gambar QRIS terlebih dahulu.',
            'qris_image.image' => 'File harus berupa foto/gambar.',
            'qris_image.mimes' => 'Format gambar yang diperbolehkan: JPG, PNG, WEBP.',
            'qris_image.max' => 'Ukuran file maksimal 5MB.'
        ]);

        $arena = active_arena();

        if ($request->hasFile('qris_image')) {
            $file = $request->file('qris_image');
            $filename = 'qris_' . $arena->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('pengaturan', $filename, 'public');
            $arena->qris_image = 'storage/' . $path;
            $arena->save();
        }

        return back()->with('success', 'Gambar QRIS Statis berhasil diperbarui!');
    }

    public function detailPemesanan($id)
    {
        $pemesanan = Pemesanan::with([
            'user',
            'detail.lapangan.pengaturan'
        ])->findOrFail($id);

        /* // KODE PHP: Otomatis berpindah cabor/arena ke cabang milik pesanan ini */
        $firstDetail = $pemesanan->detail->first();
        if ($firstDetail && $firstDetail->lapangan && $firstDetail->lapangan->pengaturan) {
            session(['active_arena_slug' => $firstDetail->lapangan->pengaturan->slug]);
        }

        $detailTanggal = $pemesanan->detail
            ->sortBy('tanggal')
            ->groupBy(function ($item) {
                return $item->tanggal;
            });

        return view('admin.pemesanan.detail', compact(
            'pemesanan',
            'detailTanggal'
        ));
    }

    public function jadwal()
    {
        return view('admin.jadwal');
    }

    public function jadwalData(\Illuminate\Http\Request $request)
    {
        $tanggal = $request->tanggal;
        $lapangan = Lapangan::where('pengaturan_id', active_arena()->id)->get();
        
        $jamBuka = (int) (active_arena()->jam_buka ?? 8);
        $jamTutup = (int) (active_arena()->jam_tutup ?? 22);
        $jamList = [];
        for ($i = $jamBuka; $i < $jamTutup; $i++) {
            $jamList[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
        }

        $jadwalData = [];
        
        foreach ($jamList as $jam) {
            $jamMulai = $jam;
            $jamSelesai = str_pad((int)substr($jam, 0, 2) + 1, 2, '0', STR_PAD_LEFT) . ':00';
            
            $row = [
                'waktu' => $jamMulai . '-' . $jamSelesai,
                'slots' => []
            ];

            foreach ($lapangan as $l) {
                $jadwal = \App\Models\Jadwal::firstOrCreate([
                    'tanggal' => $tanggal,
                    'lapangan_id' => $l->id,
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai
                ], [
                    'status' => 'tersedia'
                ]);

                $isPast = \Carbon\Carbon::parse($tanggal . ' ' . $jadwal->jam_mulai)->isPast();
                
                $isBooked = in_array($jadwal->status, ['berhasil', 'telah dipesan', 'proses']);
                $displayStatus = $jadwal->status;
                if ($isBooked) {
                    $displayStatus = ($jadwal->status === 'proses') ? 'proses' : 'sudah dipesan';
                } elseif ($displayStatus === 'tersedia' && $isPast) {
                    $displayStatus = 'waktu habis';
                }

                $row['slots'][] = [
                    'id' => $jadwal->id,
                    'lapangan_nama' => $l->nama_lapangan,
                    'status' => $jadwal->status,
                    'display_status' => $displayStatus,
                    'is_booked' => $isBooked
                ];
            }
            $jadwalData[] = $row;
        }

        return response()->json([
            'lapangan' => $lapangan->pluck('nama_lapangan'),
            'jadwal' => $jadwalData
        ]);
    }

    public function jadwalUpdate(\Illuminate\Http\Request $request)
    {
        $ids = $request->ids;
        if (!$ids || !is_array($ids)) {
            // fallback if it's a single id (for backward compatibility if needed)
            if ($request->id) {
                $ids = [$request->id];
            } else {
                return response()->json(['success' => false, 'message' => 'Invalid ids'], 400);
            }
        }
        
        $status = strtolower($request->status);

        $jadwals = \App\Models\Jadwal::whereIn('id', $ids)->get();
        
        foreach ($jadwals as $jadwal) {
            $jadwal->update([
                'status' => $status
            ]);
        }

        // Jika admin mengubah status menjadi tersedia (membatalkan), batalkan juga pemesanannya
        if ($status === 'tersedia') {
            $details = \App\Models\PemesananDetail::whereIn('jadwal_id', $ids)->get();
            $pemesananIds = $details->pluck('pemesanan_id')->unique();
            
            if ($pemesananIds->count() > 0) {
                \App\Models\Pemesanan::whereIn('id', $pemesananIds)->update(['status' => 'batal']);
                
                // Kosongkan semua jadwal lain yang terkait dengan pemesanan ini
                $allDetails = \App\Models\PemesananDetail::whereIn('pemesanan_id', $pemesananIds)->get();
                $allJadwalIds = $allDetails->pluck('jadwal_id');
                \App\Models\Jadwal::whereIn('id', $allJadwalIds)->update(['status' => 'tersedia']);
            }
        }

        return response()->json(['success' => true]);
    }

    public function jadwalUpdateAll(\Illuminate\Http\Request $request)
    {
        $tanggalList = $request->tanggalList;
        if (!$tanggalList || !is_array($tanggalList)) {
            return response()->json(['success' => false, 'message' => 'Invalid dates'], 400);
        }

        $status = strtolower($request->status);
        $lapangan = Lapangan::where('pengaturan_id', active_arena()->id)->get();

        $jamBuka = (int) (active_arena()->jam_buka ?? 8);
        $jamTutup = (int) (active_arena()->jam_tutup ?? 22);
        $jamList = [];
        for ($i = $jamBuka; $i < $jamTutup; $i++) {
            $jamList[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
        }

        foreach ($tanggalList as $tanggal) {
            // Pastikan semua record dibuat
            foreach ($jamList as $jam) {
                $jamMulai = $jam;
                $jamSelesai = str_pad((int)substr($jam, 0, 2) + 1, 2, '0', STR_PAD_LEFT) . ':00';
                
                foreach ($lapangan as $l) {
                    \App\Models\Jadwal::firstOrCreate([
                        'tanggal' => $tanggal,
                        'lapangan_id' => $l->id,
                        'jam_mulai' => $jamMulai,
                        'jam_selesai' => $jamSelesai
                    ], [
                        'status' => 'tersedia'
                    ]);
                }
            }

            $lapanganIds = $lapangan->pluck('id');
            $jadwals = \App\Models\Jadwal::where('tanggal', $tanggal)->whereIn('lapangan_id', $lapanganIds)->get();
            
            foreach ($jadwals as $jadwal) {
                $jadwal->update(['status' => $status]);
            }
        }
        
        // Jika admin mengubah status menjadi tersedia (membatalkan), batalkan juga pemesanannya
        if ($status === 'tersedia') {
            // Ambil semua jadwal_id dari rentang tanggal yang dibatalkan
            $jadwalIds = \App\Models\Jadwal::whereIn('tanggal', $tanggalList)->whereIn('lapangan_id', Lapangan::where('pengaturan_id', active_arena()->id)->pluck('id'))->pluck('id');
            
            $details = \App\Models\PemesananDetail::whereIn('jadwal_id', $jadwalIds)->get();
            $pemesananIds = $details->pluck('pemesanan_id')->unique();
            
            if ($pemesananIds->count() > 0) {
                \App\Models\Pemesanan::whereIn('id', $pemesananIds)->update(['status' => 'batal']);
                
                // Kosongkan semua jadwal lain yang terkait dengan pemesanan ini (in case ada yang beda tanggal)
                $allDetails = \App\Models\PemesananDetail::whereIn('pemesanan_id', $pemesananIds)->get();
                $allJadwalIds = $allDetails->pluck('jadwal_id');
                \App\Models\Jadwal::whereIn('id', $allJadwalIds)->update(['status' => 'tersedia']);
            }
        }
        
        return response()->json(['success' => true]);
    }

    public function indexLapangan()
    {
        return redirect()->route('admin.lapangan.edit', active_arena()->id);
    }

    public function createLapangan()
    {
        $pengaturan = new Pengaturan();
        
        // Cek apakah ada data arena utama (pusat)
        $pusat = Pengaturan::first();
        
        $sliders = collect();
        return view('admin.lapangan.create', compact('pengaturan', 'pusat', 'sliders'));
    }

    public function editLapangan($id = null)
    {
        if ($id != active_arena()->id) {
            return redirect()->route('admin.lapangan.edit', active_arena()->id);
        }
        $pengaturan = Pengaturan::findOrFail($id);
        $sliders = \App\Models\Slider::where('pengaturan_id', $id)->orderBy('urutan', 'asc')->get();
        $pusat = Pengaturan::first();
        
        return view('admin.lapangan.edit', compact('pengaturan', 'sliders', 'pusat'));
    }

    public function storeLapangan(Request $request)
    {
        $pengaturan = new Pengaturan();
        return $this->savePengaturanData($request, $pengaturan);
    }

    public function updateLapangan(Request $request, $id)
    {
        $pengaturan = Pengaturan::findOrFail($id);
        return $this->savePengaturanData($request, $pengaturan);
    }

    public function destroyLapangan($id)
    {
        if (Pengaturan::count() <= 1) {
            return redirect()->route('admin.lapangan.index')->with('error', 'Tidak dapat menghapus arena/cabang utama. Fajar Arena setidaknya harus memiliki 1 cabang olahraga!');
        }

        $pengaturan = Pengaturan::findOrFail($id);
        
        // Hapus gambar jika ada
        if ($pengaturan->gambar_utama) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pengaturan->gambar_utama);
        }
        if ($pengaturan->gambar_pengumuman) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pengaturan->gambar_pengumuman);
        }

        $pengaturan->delete();

        // Jika arena yang dihapus sedang aktif di session, reset sessionnya
        if (session('active_arena_slug') === $pengaturan->slug) {
            $firstArena = Pengaturan::first();
            session(['active_arena_slug' => $firstArena->slug]);
        }

        return redirect()->route('admin.lapangan.index')->with('success', 'Cabang olahraga berhasil dihapus secara permanen.');
    }

    private function savePengaturanData(Request $request, Pengaturan $pengaturan)
    {
        $isCreate = !$pengaturan->exists;

        $rules = [
            'nama_arena' => 'required|string|max:255',
            'jenis_olahraga' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'alamat' => 'required|string',
            'link_maps' => 'required|string',
            'kota' => 'required|string|max:255',
            'provinsi' => 'required|string|max:255',
            'kodepos' => 'required|string|max:20',
            'no_telp' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'prefix_lapangan' => 'required|string|max:100',
            'jumlah_lapangan' => 'required|integer|min:1|max:100',
            'jam_buka' => 'required',
            'jam_tutup' => 'required',
            'harga_per_jam' => 'required',
            'member_jumlah_pekan' => $request->has('is_member_active') && $request->is_member_active ? 'required|integer|min:1|max:52' : 'nullable|integer|min:1|max:52',
            'member_jam_per_pertemuan' => $request->has('is_member_active') && $request->is_member_active ? 'required|integer|min:1|max:24' : 'nullable|integer|min:1|max:24',
            'member_harga' => $request->has('is_member_active') && $request->is_member_active ? 'required' : 'nullable',
            'gambar_utama' => $isCreate ? 'required|image|mimes:jpeg,png,jpg,webp|max:3072' : 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'gambar_pengumuman' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
        ];

        $messages = [
            'nama_arena.required' => 'Nama Lapangan / Cabang wajib diisi.',
            'jenis_olahraga.required' => 'Cabang Olahraga (Cabor) wajib diisi.',
            'deskripsi.required' => 'Deskripsi singkat wajib diisi.',
            'gambar_utama.required' => 'Gambar Arena wajib diunggah.',
            'alamat.required' => 'Alamat lengkap wajib diisi.',
            'link_maps.required' => 'Kode HTML Iframe Peta wajib diisi.',
            'kota.required' => 'Kota wajib diisi.',
            'provinsi.required' => 'Provinsi wajib diisi.',
            'kodepos.required' => 'Kode Pos wajib diisi.',
            'no_telp.required' => 'No Telepon wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format Email tidak valid.',
            'prefix_lapangan.required' => 'Awalan Nama (Prefix Lapangan) wajib diisi.',
            'jumlah_lapangan.required' => 'Jumlah lapangan wajib diisi.',
            'jumlah_lapangan.min' => 'Jumlah lapangan minimal 1.',
            'jam_buka.required' => 'Jam Buka wajib dipilih.',
            'jam_tutup.required' => 'Jam Tutup wajib dipilih.',
            'harga_per_jam.required' => 'Harga per jam (Non Member) wajib diisi.',
            'member_jumlah_pekan.required' => 'Jumlah pekan paket member wajib diisi.',
            'member_jam_per_pertemuan.required' => 'Jam per pertemuan member wajib diisi.',
            'member_harga.required' => 'Harga paket member wajib diisi.',
        ];

        $request->validate($rules, $messages);

        // Validasi Jam Tutup harus lebih akhir daripada Jam Buka
        if ($request->has('jam_buka') && $request->has('jam_tutup')) {
            $bukaHour = (int) substr($request->jam_buka, 0, 2);
            $tutupHour = (int) substr($request->jam_tutup, 0, 2);
            if ($tutupHour == 0) {
                $tutupHour = 24; // 00:00 adalah Tengah Malam (24:00)
            }
            if ($tutupHour <= $bukaHour) {
                return back()->withInput()->with('error', 'Jam Tutup harus lebih akhir daripada Jam Buka!');
            }
        }

        // Generate slug from nama_arena if not exists
        if (!$pengaturan->slug && $request->has('nama_arena')) {
            $request->merge(['slug' => \Illuminate\Support\Str::slug($request->nama_arena)]);
        }

        // Bersihkan format harga
        if ($request->has('harga_per_jam')) {
            $harga = preg_replace('/[^0-9]/', '', $request->harga_per_jam);
            $request->merge(['harga_per_jam' => $harga]);
        }

        if ($request->has('member_harga')) {
            $memberHarga = preg_replace('/[^0-9]/', '', $request->member_harga);
            $request->merge(['member_harga' => $memberHarga]);
        }

        $request->merge([
            'is_member_active' => $request->has('is_member_active') ? true : false
        ]);

        $pengaturan->fill($request->only([
            'slug', 'nama_arena', 'jenis_olahraga', 'prefix_lapangan',
            'deskripsi',
            'alamat', 'kota', 'provinsi', 'kodepos',
            'no_telp', 'email', 'link_maps',
            'jumlah_lapangan', 'jam_buka', 'jam_tutup', 'harga_per_jam',
            'is_member_active', 'member_jumlah_pekan', 'member_pertemuan_per_pekan',
            'member_jam_per_pertemuan', 'member_harga', 'fasilitas_tambahan'
        ]));

        if ($request->has('fasilitas') && is_array($request->fasilitas)) {
            $pengaturan->fasilitas = json_encode($request->fasilitas);
        } else {
            $pengaturan->fasilitas = null;
        }

        // Fallback default values jika kolom kosong saat pembuatan arena baru
        if (empty($pengaturan->nama_arena)) {
            $pengaturan->nama_arena = 'Arena Baru';
        }
        if (empty($pengaturan->slug)) {
            $pengaturan->slug = \Illuminate\Support\Str::slug($pengaturan->nama_arena);
        }
        if (empty($pengaturan->jenis_olahraga)) {
            $pengaturan->jenis_olahraga = 'Olahraga';
        }
        if (empty($pengaturan->prefix_lapangan)) {
            $pengaturan->prefix_lapangan = 'Lapangan';
        }
        if (empty($pengaturan->jumlah_lapangan) || (int)$pengaturan->jumlah_lapangan <= 0) {
            $pengaturan->jumlah_lapangan = 1;
        }
        if (empty($pengaturan->jam_buka)) {
            $pengaturan->jam_buka = '08:00:00';
        }
        if (empty($pengaturan->jam_tutup)) {
            $pengaturan->jam_tutup = '23:00:00';
        }
        if (empty($pengaturan->harga_per_jam) || (int)$pengaturan->harga_per_jam <= 0) {
            $pengaturan->harga_per_jam = 50000;
        }

        // Pengaturan media gambar utama dari step 1
        if ($request->hasFile('gambar_utama')) {
            if ($pengaturan->gambar_utama) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pengaturan->gambar_utama);
            }
            $pengaturan->gambar_utama = $request->file('gambar_utama')->store('pengaturan', 'public');
        }

        $pengaturan->save();

        // Sync jumlah lapangan
        if ($pengaturan->jumlah_lapangan > 0) {
            $existingLapangans = \App\Models\Lapangan::where('pengaturan_id', $pengaturan->id)->orderBy('id')->get();
            $existingCount = $existingLapangans->count();
            
            $prefixLapangan = $request->input('prefix_lapangan', 'Lapangan');

            if ($pengaturan->jumlah_lapangan > $existingCount) {
                // Tambah lapangan baru
                for ($i = $existingCount + 1; $i <= $pengaturan->jumlah_lapangan; $i++) {
                    \App\Models\Lapangan::create([
                        'pengaturan_id' => $pengaturan->id,
                        'nama_lapangan' => $prefixLapangan . ' ' . $i,
                        'harga_per_jam' => $pengaturan->harga_per_jam,
                        'status' => 'aktif'
                    ]);
                }
            } elseif ($pengaturan->jumlah_lapangan < $existingCount) {
                // Nonaktifkan lapangan yang berlebih (jika ada pemesanan, lebih baik dinonaktifkan daripada dihapus)
                // Namun untuk sekarang kita hapus saja yang terakhir jika tidak ada masalah referensi
                $toRemove = $existingCount - $pengaturan->jumlah_lapangan;
                $lapangansToRemove = \App\Models\Lapangan::where('pengaturan_id', $pengaturan->id)->orderBy('id', 'desc')->limit($toRemove)->get();
                foreach ($lapangansToRemove as $lap) {
                    $lap->delete();
                }
            }

                        // Update harga semua lapangan (dan mungkin prefix jika diperlukan di masa depan)
            \App\Models\Lapangan::where('pengaturan_id', $pengaturan->id)->update(['harga_per_jam' => $pengaturan->harga_per_jam]);

            // Update nama lapangan sesuai prefix baru jika ada
            if ($pengaturan->prefix_lapangan) {
                $semuaLapangan = \App\Models\Lapangan::where('pengaturan_id', $pengaturan->id)->orderBy('id')->get();
                foreach ($semuaLapangan as $index => $lap) {
                    $lap->nama_lapangan = $pengaturan->prefix_lapangan . ' ' . ($index + 1);
                    $lap->save();
                }
            }
        }

        // Delete out of bounds available schedules for this arena
        $lapanganIds = \App\Models\Lapangan::where('pengaturan_id', $pengaturan->id)->pluck('id');
        
        \App\Models\Jadwal::whereIn('lapangan_id', $lapanganIds)
            ->where('status', 'tersedia')
            ->where(function($query) use ($pengaturan) {
                $query->where('jam_mulai', '<', $pengaturan->jam_buka)
                      ->orWhere('jam_mulai', '>=', $pengaturan->jam_tutup);
            })
            ->delete();

        return redirect()->route('admin.lapangan.edit', $pengaturan->id)->with('success', 'Pengaturan berhasil diperbarui.')->with('step', $request->step ?? 1);
    }

    public function hapusGambar()
    {
        $pengaturan = Pengaturan::first();
        if ($pengaturan && $pengaturan->gambar_utama) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($pengaturan->gambar_utama);
            $pengaturan->gambar_utama = null;
            $pengaturan->save();
        }
        return redirect()->back()->with('success', 'Gambar utama berhasil dihapus.')->with('step', 2);
    }

    public function hapusPengumuman()
    {
        $pengaturan = Pengaturan::first();
        if ($pengaturan) {
            if ($pengaturan->gambar_pengumuman) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($pengaturan->gambar_pengumuman);
                $pengaturan->gambar_pengumuman = null;
            }
            $pengaturan->pengumuman = null;
            $pengaturan->save();
        }
        return redirect()->back()->with('success', 'Promo/Event berhasil dihapus.')->with('step', 1);
    }

    public function storeSlider(Request $request)
    {
        $request->validate([
            'pengaturan_id' => 'required|exists:pengaturan,id',
            'slider_gambar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'slider_judul' => 'nullable|string|max:255',
            'slider_tagline' => 'nullable|string|max:255',
            'slider_deskripsi' => 'nullable|string'
        ]);

        $path = $request->file('slider_gambar')->store('sliders', 'public');
        
        $urutan = \App\Models\Slider::max('urutan') + 1;

        \App\Models\Slider::create([
            'pengaturan_id' => $request->pengaturan_id,
            'judul' => $request->slider_judul,
            'tagline' => $request->slider_tagline,
            'deskripsi' => $request->slider_deskripsi,
            'gambar' => $path,
            'urutan' => $urutan
        ]);

        return redirect()->back()->with('success', 'Slide berhasil ditambahkan.')->with('step', 2);
    }

    public function destroySlider($id)
    {
        $slider = \App\Models\Slider::findOrFail($id);
        if ($slider->gambar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($slider->gambar);
        }
        $slider->delete();

        return redirect()->back()->with('success', 'Slide berhasil dihapus.')->with('step', 2);
    }

    public function updateSlider(Request $request)
    {
        $request->validate([
            'slider_id' => 'required|exists:sliders,id',
            'slider_gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'slider_judul' => 'nullable|string|max:255',
            'slider_tagline' => 'nullable|string|max:255',
            'slider_deskripsi' => 'nullable|string'
        ]);

        $slider = \App\Models\Slider::findOrFail($request->slider_id);
        $slider->judul = $request->slider_judul;
        $slider->tagline = $request->slider_tagline;
        $slider->deskripsi = $request->slider_deskripsi;

        if ($request->hasFile('slider_gambar')) {
            if ($slider->gambar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($slider->gambar);
            }
            $slider->gambar = $request->file('slider_gambar')->store('sliders', 'public');
        }

        $slider->save();

        return redirect()->back()->with('success', 'Slide berhasil diperbarui.')->with('step', 2);
    }

    public function scan()
    {
        return view('admin.scan.index');
    }

    public function processScan(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'kode_reservasi' => 'required|string'
        ]);

        $pemesanan = \App\Models\Pemesanan::with(['user', 'detail.lapangan'])
            ->where('kode_reservasi', $request->kode_reservasi)
            ->first();

        if (!$pemesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket tidak ditemukan.'
            ], 404);
        }

        $groupedDetails = collect($pemesanan->detail)->groupBy(function($d) {
            return $d->tanggal . '|' . $d->lapangan_id;
        })->map(function($group) {
            $sorted = $group->sortBy('jam_mulai');
            return (object)[
                'tanggal' => $sorted->first()->tanggal,
                'lapangan_id' => $sorted->first()->lapangan_id,
                'lapangan_nama' => $sorted->first()->lapangan->nama_lapangan ?? 'Lapangan ' . $sorted->first()->lapangan_id,
                'jam_mulai' => $sorted->first()->jam_mulai,
                'jam_selesai' => $sorted->last()->jam_selesai,
                'is_used' => $sorted->first()->is_used,
            ];
        })->sortBy('tanggal')->values();

        $jadwalHtml = '';
        $allUsed = true;
        foreach ($groupedDetails as $d) {
            $tgl = \Carbon\Carbon::parse($d->tanggal)->translatedFormat('d F Y');
            $waktu = substr($d->jam_mulai, 0, 5) . ' - ' . substr($d->jam_selesai, 0, 5);
            $jadwalHtml .= '<div class="mb-3 last:mb-0 bg-white border border-gray-200 p-3 rounded-xl shadow-sm">';
            $jadwalHtml .= '<p class="font-bold text-sm text-gray-800">' . $tgl . '</p>';
            $jadwalHtml .= '<div class="flex justify-between items-center mt-2">';
            $jadwalHtml .= '<div class="flex flex-col gap-1">';
            $jadwalHtml .= '<p class="text-xs font-semibold text-blue-600">' . $waktu . ' WITA</p>';
            $jadwalHtml .= '<span class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600 inline-block w-max">' . $d->lapangan_nama . '</span>';
            $jadwalHtml .= '</div>';
            
            $isMember = $pemesanan->jenis_user === 'member';

            if ($isMember) {
                if ($d->is_used) {
                    $jadwalHtml .= '<button type="button" class="btn-cancel-checkin text-[10px] font-bold bg-green-100 hover:bg-red-100 text-green-700 hover:text-red-700 px-3 py-1.5 rounded-lg border border-green-200 hover:border-red-200 shadow-sm transition-all" data-kode="' . $pemesanan->kode_reservasi . '" data-tanggal="' . $d->tanggal . '" data-lapangan="' . $d->lapangan_id . '" title="Klik untuk membatalkan check-in">Telah Digunakan (Batal)</button>';
                } else {
                    $allUsed = false;
                    $jadwalHtml .= '<button type="button" class="btn-checkin-item text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm transition-all" data-kode="' . $pemesanan->kode_reservasi . '" data-tanggal="' . $d->tanggal . '" data-lapangan="' . $d->lapangan_id . '">Check-in</button>';
                }
            } else {
                if ($d->is_used) {
                    $jadwalHtml .= '<span class="text-[10px] font-bold bg-green-100 text-green-700 px-3 py-1.5 rounded-lg border border-green-200 shadow-sm">Telah Digunakan</span>';
                } else {
                    $allUsed = false;
                }
            }

            $jadwalHtml .= '</div></div>';
        }

        if ($pemesanan->jenis_user !== 'member') {
            if ($allUsed) {
                $jadwalHtml .= '<button type="button" class="btn-cancel-all w-full mt-4 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 py-3 rounded-xl font-bold text-sm transition-all shadow-sm" data-kode="' . $pemesanan->kode_reservasi . '">Batalkan Check-in</button>';
            } else {
                $jadwalHtml .= '<button type="button" class="btn-checkin-all w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-bold text-sm transition-all shadow-md" data-kode="' . $pemesanan->kode_reservasi . '">Proses Check-in Tiket</button>';
            }
        }

        // If ticket was not fully used before, but now all details are used, mark it.
        if ($allUsed && !$pemesanan->is_used) {
            $pemesanan->is_used = true;
            $pemesanan->save();
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'kode_reservasi' => $pemesanan->kode_reservasi,
                'nama_pemesan' => $pemesanan->user->name,
                'jenis_user' => $pemesanan->jenis_user,
                'status_pembayaran' => $pemesanan->status,
                'jadwal_html' => $jadwalHtml,
                'is_used' => $allUsed
            ]
        ]);
    }

    public function checkIn(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'kode_reservasi' => 'required|string',
            'tanggal' => 'nullable|date',
            'lapangan_id' => 'nullable'
        ]);

        $pemesanan = \App\Models\Pemesanan::where('kode_reservasi', $request->kode_reservasi)->first();

        if (!$pemesanan) {
            return response()->json(['status' => 'error', 'message' => 'Tiket tidak ditemukan.'], 404);
        }

        if ($pemesanan->status !== 'berhasil') {
            return response()->json(['status' => 'error', 'message' => 'Tiket belum lunas atau sudah dibatalkan.'], 400);
        }

        if ($request->filled('tanggal') && $request->filled('lapangan_id')) {
            $details = \App\Models\PemesananDetail::where('pemesanan_id', $pemesanan->id)
                ->where('tanggal', $request->tanggal)
                ->where('lapangan_id', $request->lapangan_id)
                ->get();
                
            if ($details->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Jadwal tidak ditemukan.'], 404);
            }
            
            if ($details->first()->is_used) {
                return response()->json(['status' => 'error', 'message' => 'Jadwal ini sudah digunakan.'], 400);
            }

            foreach ($details as $detail) {
                $detail->is_used = true;
                $detail->save();
            }
        } else {
            // Check in ALL schedules (for non-member usually)
            $details = \App\Models\PemesananDetail::where('pemesanan_id', $pemesanan->id)->get();
            if ($pemesanan->is_used || $details->every->is_used) {
                return response()->json(['status' => 'error', 'message' => 'Tiket ini sudah digunakan seluruhnya.'], 400);
            }
            
            foreach ($details as $detail) {
                $detail->is_used = true;
                $detail->save();
            }
            
            $pemesanan->is_used = true;
            $pemesanan->save();
        }
        
        // Cek jika seluruh jadwal sudah digunakan
        $allUsed = !\App\Models\PemesananDetail::where('pemesanan_id', $pemesanan->id)->where('is_used', false)->exists();
        if ($allUsed) {
            $pemesanan->is_used = true;
            $pemesanan->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal berhasil di-Checkin!'
        ]);
    }

    public function cancelCheckIn(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'kode_reservasi' => 'required|string',
            'tanggal' => 'nullable|date',
            'lapangan_id' => 'nullable'
        ]);

        $pemesanan = \App\Models\Pemesanan::where('kode_reservasi', $request->kode_reservasi)->first();

        if (!$pemesanan) {
            return response()->json(['status' => 'error', 'message' => 'Tiket tidak ditemukan.'], 404);
        }

        if ($request->filled('tanggal') && $request->filled('lapangan_id')) {
            $details = \App\Models\PemesananDetail::where('pemesanan_id', $pemesanan->id)
                ->where('tanggal', $request->tanggal)
                ->where('lapangan_id', $request->lapangan_id)
                ->get();
                
            if ($details->isEmpty()) {
                return response()->json(['status' => 'error', 'message' => 'Jadwal tidak ditemukan.'], 404);
            }

            foreach ($details as $detail) {
                $detail->is_used = false;
                $detail->save();
            }
        } else {
            // Cancel ALL check-ins
            $details = \App\Models\PemesananDetail::where('pemesanan_id', $pemesanan->id)->get();
            foreach ($details as $detail) {
                $detail->is_used = false;
                $detail->save();
            }
        }
        
        $pemesanan->is_used = false;
        $pemesanan->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in berhasil dibatalkan!'
        ]);
    }
}