<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lapangan;
use App\Models\Jadwal;
use App\Models\Pemesanan;
use App\Models\PemesananDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservasiController extends Controller
{
    // =========================
    // HALAMAN RESERVASI
    // =========================
    public function index(Request $request)
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
                    Jadwal::where('id', $detail->jadwal_id)->update(['status' => 'tersedia']);
                }
            }
        }

        $isMember = $request->member == 1;
        
        $tanggal = $request->tanggal ?? now()->format('Y-m-d');

        $pengaturan = active_arena();

        $lapangan = Lapangan::where('pengaturan_id', $pengaturan->id)->get();
        if (!$pengaturan?->is_member_active) {
            $isMember = false;
        }
        $jamBuka = $pengaturan ? (int) substr($pengaturan->jam_buka, 0, 2) : 8;
        $jamTutup = $pengaturan ? (int) substr($pengaturan->jam_tutup, 0, 2) : 23;

        $effectiveTutup = $jamTutup;
        if ($effectiveTutup <= $jamBuka) {
            $effectiveTutup += 24;
        }

        $hargaPerJamDefault = $pengaturan ? $pengaturan->harga_per_jam : 80000;

        $jamList = [];
        for ($i = $jamBuka; $i < $effectiveTutup; $i++) {
            $startHour = $i % 24;
            $endHour = ($i + 1) % 24;
            $start = sprintf('%02d:00', $startHour);
            $end = sprintf('%02d:00', $endHour);
            $jamList[] = [$start, $end];
        }

        $startDate = Carbon::today()->format('Y-m-d');
        $endDate = Carbon::today()->addDays(59)->format('Y-m-d');
        
        $existingJadwal = Jadwal::whereBetween('tanggal', [$startDate, $endDate])
            ->select('tanggal', 'lapangan_id', 'jam_mulai')
            ->get()
            ->groupBy(function($item) {
                $jamMulai = substr($item->jam_mulai, 0, 5);
                return $item->tanggal . '_' . $item->lapangan_id . '_' . $jamMulai;
            })->toArray();
            
        $newSchedules = [];
        $now = now();
        for ($d = 0; $d < 60; $d++) {
            $tgl = Carbon::today()->addDays($d)->format('Y-m-d');
            foreach ($lapangan as $l) {
                foreach ($jamList as $jam) {
                    $key = $tgl . '_' . $l->id . '_' . $jam[0];
                    if (!isset($existingJadwal[$key])) {
                        $newSchedules[] = [
                            'lapangan_id' => $l->id,
                            'tanggal' => $tgl,
                            'jam_mulai' => $jam[0],
                            'jam_selesai' => $jam[1],
                            'status' => 'tersedia',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }
            }
        }
        
        if (count($newSchedules) > 0) {
            foreach (array_chunk($newSchedules, 500) as $chunk) {
                Jadwal::insert($chunk);
            }
        }

        $jamBukaStr = sprintf('%02d:00:00', $jamBuka);
        $jamTutupStr = sprintf('%02d:00:00', $jamTutup);

        $jadwalQuery = Jadwal::where('tanggal', $tanggal)
            ->whereIn('lapangan_id', $lapangan->pluck('id'));

        if ($jamTutup <= $jamBuka) {
            $jadwalQuery->where(function($q) use ($jamBukaStr, $jamTutupStr) {
                $q->where('jam_mulai', '>=', $jamBukaStr)
                  ->orWhere('jam_mulai', '<', $jamTutupStr);
            });
        } else {
            $jadwalQuery->where('jam_mulai', '>=', $jamBukaStr)
                        ->where('jam_mulai', '<', $jamTutupStr);
        }

        $jadwalRaw = $jadwalQuery->get();

        $jadwalDict = [];
        foreach ($jadwalRaw as $j) {
            $key = $j->jam_mulai . '_' . $j->lapangan_id;
            if (!isset($jadwalDict[$key]) || $jadwalDict[$key]->status == 'tersedia') {
                $jadwalDict[$key] = $j;
            }
        }
        $jadwal = collect(array_values($jadwalDict))->sortBy(function($j) use ($jamBuka) {
            $h = (int) substr($j->jam_mulai, 0, 2);
            return ($h < $jamBuka) ? ($h + 24) : $h;
        });

        $memberSlots = [];

        if ($isMember) {

            $jamMulai = $request->jam_mulai;
            $jamAkhir = $request->jam_akhir;
            
            $tglMulai = $request->tanggal_mulai ? Carbon::parse($request->tanggal_mulai) : Carbon::parse($tanggal);
            $tglAkhir = $request->tanggal_akhir ? Carbon::parse($request->tanggal_akhir) : Carbon::parse($tanggal);

            // Loop untuk setiap hari di rentang tanggal
            $currentDate = $tglMulai->copy();
            while ($currentDate->lte($tglAkhir)) {
                $slots = $this->cariSlotMember(
                    $currentDate->format('Y-m-d'),
                    $jamMulai,
                    $jamAkhir
                );
                
                // Tambahkan tanggal ke setiap slot agar bisa di-group di view
                foreach ($slots as &$slot) {
                    $slot['tanggal_member'] = $currentDate->format('Y-m-d');
                }
                unset($slot);
                
                $memberSlots = array_merge($memberSlots, $slots);
                $currentDate->addDay();
            }

            // Jika pengguna melakukan filter: HANYA tampilkan slot yang statusnya 'tersedia'
            $isFilterMember = $request->filled('tanggal_mulai') || $request->filled('tanggal_akhir') || $request->filled('jam_mulai') || $request->filled('jam_akhir');
            if ($isFilterMember) {
                $memberSlots = array_values(array_filter($memberSlots, function($s) {
                    return isset($s['status']) && $s['status'] === 'tersedia';
                }));
            }
        }

        $tanggalList = [];
        $today = Carbon::today();

        for ($i = 0; $i < 30; $i++) {
            $tanggalList[] = $today->copy()->addDays($i);
        }

        $pembayaranPending = null;

        if (auth()->check()) {

            $pembayaranPending = Pemesanan::where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'proses'])
                ->latest()
                ->first();

        }

        return view('reservasi.index', compact(
            'tanggal',
            'lapangan',
            'jadwal',
            'tanggalList',
            'isMember',
            'memberSlots',
            'pembayaranPending',
            'jamBuka',
            'jamTutup',
            'hargaPerJamDefault'
        ));
    }

    // =========================
    // PESAN (ANTI DOUBLE BOOKING)
    // =========================
    public function pesan(Request $request)
    {
        if (auth()->check()) {
            $existingPending = Pemesanan::where('user_id', auth()->id())
                ->whereIn('status', ['pending', 'proses'])
                ->latest()
                ->first();

            if ($existingPending) {
                if ($existingPending->status === 'pending') {
                    return redirect('/pembayaran/' . $existingPending->id);
                } else {
                    return redirect()->route('pembayaran.menunggu', $existingPending->id);
                }
            }
        }

        $jadwalDipilih = $request->jadwal;

        if (!$jadwalDipilih) {
            return back()->with('error', 'Pilih jadwal terlebih dahulu');
        }

        DB::beginTransaction();

        try {

            foreach ($jadwalDipilih as $idJadwal) {

                $jadwal = Jadwal::lockForUpdate()->find($idJadwal);

                if (!$jadwal || $jadwal->status != 'tersedia') {
                    DB::rollBack();
                    return back()->with('error', 'Jadwal tidak tersedia!');
                }
            }

            $kode = 'RSV' . time();

            // Ambil tanggal dari jadwal pertama
            $jadwalPertama = Jadwal::find($jadwalDipilih[0]);

            $durasi = count($jadwalDipilih);

            $pengaturan = active_arena();

            if ($request->has('is_member')) {
                // Harga member (contoh: di override di hasil saja)
            } else {
                $hargaPerJam = $pengaturan ? $pengaturan->harga_per_jam : 80000;
                $total = $durasi * $hargaPerJam;
            }

            $pemesanan = \App\Models\Pemesanan::create([
                'kode_reservasi' => $kode,
                'user_id' => auth()->id(),
                'tanggal_mulai' => $jadwalPertama->tanggal,
                'jenis_user' => $request->has('is_member') ? 'member' : 'non_member',
                'durasi' => $durasi,
                'total_harga' => $total ?? ($pengaturan->member_harga ?? 1000000),
                'status' => 'pending'
            ]);

            foreach ($jadwalDipilih as $idJadwal) {

                $jadwal = Jadwal::lockForUpdate()->find($idJadwal);

                PemesananDetail::create([
                    'pemesanan_id' => $pemesanan->id,
                    'lapangan_id' => $jadwal->lapangan_id,
                    'jadwal_id' => $jadwal->id,
                    'minggu_ke' => null,
                    'tanggal' => $jadwal->tanggal,
                    'jam_mulai' => $jadwal->jam_mulai,
                    'jam_selesai' => $jadwal->jam_selesai
                ]);

                $jadwal->update([
                    'status' => 'proses'
                ]);
            }

            DB::commit();

            // Langsung ke 1 halaman pembayaran
            return redirect('/pembayaran/' . $pemesanan->id);

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan');
        }
    }

    // =========================
    // HALAMAN HASIL (GABUNGAN)
    // =========================
    public function hasil($id)
    {
        $pemesanan = Pemesanan::with('detail.jadwal.lapangan')->find($id);

        $jadwal = $pemesanan->detail;

        $pengaturan = active_arena();
        $hargaDefault = $pengaturan ? $pengaturan->harga_per_jam : 80000;

        $tanggal = $jadwal->first()->tanggal;
        $durasi = $pemesanan->durasi;
        $harga = $pemesanan->jenis_user == 'member' ? ($pengaturan->member_harga ?? 1000000) : $hargaDefault;
        $total = $pemesanan->jenis_user == 'member' ? ($pengaturan->member_harga ?? 1000000) : $durasi * $harga;

        return view('reservasi.hasil', [
            'pemesanan' => $pemesanan,
            'jadwal' => $jadwal,
            'tanggal' => $tanggal,
            'durasi' => $durasi,
            'harga' => $harga,
            'total' => $total,
            'status' => $pemesanan->status
        ]);
    }

    // =========================
    // BAYAR (TIDAK PINDAH HALAMAN)
    // =========================
    public function bayar($id)
    {
        $pemesanan = Pemesanan::with('detail')->find($id);

        $pemesanan->update([
            'status' => 'berhasil'
        ]);

        foreach ($pemesanan->detail as $d) {

            $jadwal = Jadwal::find($d->jadwal_id);

            $jadwal->update([
                'status' => 'telah dipesan'
            ]);
        }

        // Balik ke halaman yang sama
        return redirect('/reservasi/hasil/' . $pemesanan->id);
    }

    // ============================================================
    // FILTER ALGORITMA GREEDY
    // ============================================================
    public function filter(Request $request)
    {

        $isMember = false;
        $memberSlots = [];
        $pengaturan = active_arena();
        $jamBuka = $pengaturan ? (int) substr($pengaturan->jam_buka, 0, 2) : 8;
        $jamTutup = $pengaturan ? (int) substr($pengaturan->jam_tutup, 0, 2) : 23;
        if ($jamTutup == 0) $jamTutup = 24;
        $hargaPerJamDefault = $pengaturan ? $pengaturan->harga_per_jam : 80000;

        if (!$request->durasi) {
            return back()->with('error', 'Durasi wajib diisi');
        }

        $durasi = (int)$request->durasi;

        $today = Carbon::today();

        $tanggalMulai = $request->tanggal_mulai ?? $today->format('Y-m-d');
        $tanggalAkhir = $request->tanggal_akhir ?? $today->copy()->addDays(29)->format('Y-m-d');

        $jamMulai = $request->jam_mulai 
            ? date('H:i:s', strtotime($request->jam_mulai)) 
            : null;

        $jamSelesai = $request->jam_selesai 
            ? date('H:i:s', strtotime($request->jam_selesai)) 
            : null;

        // =========================================================================
        // 1. QUERY DATABASE: Mengambil daftar lapangan & jadwal dari MySQL
        // =========================================================================
        $lapangan = Lapangan::where('pengaturan_id', $pengaturan->id)->get(); // Mengambil semua data lapangan yang terdaftar 

        $jamBuka = $pengaturan ? (int) substr($pengaturan->jam_buka, 0, 2) : 8;
        $jamTutup = $pengaturan ? (int) substr($pengaturan->jam_tutup, 0, 2) : 23;
        if ($jamTutup == 0) $jamTutup = 24; // Mengbah angkanya jadi 24 agar logika perbandingan batas jam tetap valid dari jam 08.00 sampai 24.00
        $jamBukaStr = sprintf('%02d:00:00', $jamBuka);
        $jamTutupStr = sprintf('%02d:00:00', $jamTutup); //Mengubah angka jam buka/tutup kembali ke string format standar MySQL 

        $jadwal = Jadwal::whereIn('lapangan_id', $lapangan->pluck('id')) // Cari jadwal yang lapangan id 
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir]) 
            ->where('jam_mulai', '>=', $jamBukaStr)
            ->where('jam_mulai', '<', $jamTutupStr)
            ->orderBy('tanggal') // mengurutkan dari awal
            ->orderBy('jam_mulai') 
            ->get(); 

        // Wadah kosong untuk menampung hasil akhir algoritma greedy
        $hasil = [];

        // Membuat daftar slot yang sudah dipesan (booked)
        // Kunci formatnya: "2026-08-06_1_08:00:00" (tanggal_lapanganId_jamMulai)
        $bookedKeys = [];
        foreach ($jadwal as $j) {
            if ($j->status != 'tersedia') {
                $bookedKeys[$j->tanggal . '_' . $j->lapangan_id . '_' . $j->jam_mulai] = true;
            }
        }

        // Menyaring hanya slot yang BENAR-BENAR tersedia
        // Syarat lolos filter:
        //   1. Status = 'tersedia'
        //   2. Tidak ada record duplikat yang statusnya sudah 'booked'
        //   3. Waktu jadwal belum lewat (belum masa lalu)
        $jadwalTersedia = $jadwal->where('status', 'tersedia')->filter(function ($item) use ($bookedKeys) {
            $key = $item->tanggal . '_' . $item->lapangan_id . '_' . $item->jam_mulai;
            if (isset($bookedKeys[$key])) return false;
            return !\Carbon\Carbon::parse($item->tanggal . ' ' . $item->jam_mulai)->isPast();
        });

        // =========================================================================
        // 2. ALGORITMA GREEDY: PROSES SELEKSI & REKOMENDASI SLOT BERURUTAN
        // =========================================================================
        foreach ($jadwalTersedia->groupBy('tanggal') as $tgl => $items) { // mengelompokkan slot pertanggal

            // mengambil semua mulai dari jam mulai 
            $jamList = $items->pluck('jam_mulai')->unique()->sort()->values();

            for ($i = 0; $i <= count($jamList) - $durasi; $i++) {

                $paths = [[]]; // menyiapkan tempat awal rute
                $validSequence = true; // menandai status keberurutan jam

                for ($j = 0; $j < $durasi; $j++) { // Memastikan jam-jam yang dirangkai benar-benar berurutan tanpa jeda kosong

                    $jamTarget = $jamList[$i + $j];
                    $currentJadwal = $items->where('jam_mulai', $jamTarget);

                    // SYARAT 1: Jam Harus Berurutan (Tanpa Jeda / Gap Waktu)
                    if ($j > 0) {
                        $jamPrev = $jamList[$i + $j - 1];
                        if ($jamTarget != date('H:i:s', strtotime($jamPrev) + 3600)) {
                            $validSequence = false;
                            break;
                        }
                    }

                    // SYARAT 2: Filter Rentang Jam (Mulai)
                    if ($jamMulai && $jamTarget < $jamMulai) {
                        $validSequence = false;
                        break;
                    }

                    // SYARAT 3: Filter Rentang Jam (Selesai)
                    $jamAkhirTarget = date('H:i:s', strtotime($jamTarget) + 3600);
                    if ($jamSelesai && $jamAkhirTarget > $jamSelesai) {
                        $validSequence = false;
                        break;
                    }

                    if ($currentJadwal->isEmpty()) { // jika pada jam target tersebut ternyata semua lapangan penuh maka batal
                        $validSequence = false;
                        break;
                    }

                    // =========================================================================
                    // PROSES GREEDY (GREEDY CHOICE): 
                    // Memilih jalur lapangan yang meminimalkan perpindahan lapangan (switches)
                    // =========================================================================
                    $newPaths = []; // untuk menyimpan jalur kombinasi jam bermain 
                    $bestPerCourt = []; // untuk mencatat kombinasi jalur terbaik 
                    
                    foreach ($paths as $path) { // mengambil kombinasi jam bermain yang sudah terbentuk di jam jam sebelumnya
                        foreach ($currentJadwal as $slotAvailable) {
                            $newPath = $path;
                            $newPath[] = $slotAvailable;
                            
                            // Hitung berapa kali user berpindah lapangan (switches)
                            $switches = 0; // hitungan awal perpindahan lapangan
                            for ($k = 1; $k < count($newPath); $k++) {
                                if ($newPath[$k]->lapangan_id != $newPath[$k-1]->lapangan_id) { // apakah ID Lapangan di jam sekarang berbedaa dengan ID Lapangan di jam sebelumya
                                    $switches++; // jika berbeda maka bertambah 1
                                }
                            }
                            
                            $lapId = $slotAvailable->lapangan_id; // jika perpindahan lapangannya 0 maka menjadi terbaik
                            if (!isset($bestPerCourt[$lapId])) {
                                $bestPerCourt[$lapId] = [
                                    'min_switches' => $switches,
                                    'paths' => [$newPath]
                                ];
                            } else {
                                // KEPUTUSAN GREEDY: jika perpindahan lapangan lebih kecil maka akan di update 
                                if ($switches < $bestPerCourt[$lapId]['min_switches']) {
                                    $bestPerCourt[$lapId]['min_switches'] = $switches;
                                    $bestPerCourt[$lapId]['paths'] = [$newPath];
                                }
                            }
                        }
                    }
                    
                    foreach ($bestPerCourt as $courtData) { // mengumpulkan rute-rute pemenang 
                        foreach ($courtData['paths'] as $p) {
                            $newPaths[] = $p;
                        }
                    }
                    $paths = $newPaths;
                }

                // =========================================================================
                // EVALUASI AKHIR GREEDY (OPTIMAL SUBSTRUCTURE):
                // Mengambil hasil urutan jam yang memiliki total perpindahan paling minimal
                // Sistem melihat semua rute yang terbentuk. Misal ada Rute A (switches = 2), Rute B (switches = 0), dan Rute C (switches = 1).
                // =========================================================================
                if ($validSequence && count($paths) > 0) {
                    
                    $minSwitches = 999;
                    
                    foreach ($paths as $path) {
                        $switches = 0;
                        for ($k = 1; $k < count($path); $k++) {
                            if ($path[$k]->lapangan_id != $path[$k-1]->lapangan_id) {
                                $switches++;
                            }
                        }
                        if ($switches < $minSwitches) {
                            $minSwitches = $switches;
                        }
                    }
                    
                    // Simpan path yang memiliki nilai switches terendah/terbaik dan hitung
                    foreach ($paths as $path) {
                        $switches = 0;
                        for ($k = 1; $k < count($path); $k++) {
                            if ($path[$k]->lapangan_id != $path[$k-1]->lapangan_id) {
                                $switches++;
                            }
                        }
                        if ($switches == $minSwitches) {
                            $hasil[$tgl][] = $path;
                        }
                    }
                }
            }
        }

        $tanggal = $tanggalMulai; // membuat daftar tanggal selama 30 hari ke depan

        $tanggalList = [];
        for ($i = 0; $i < 30; $i++) {
            $tanggalList[] = $today->copy()->addDays($i);
        }

        // =========================================================================
        // 3. PENANDAAN HASIL GREEDY (REKOMENDASI)
        // =========================================================================
        // Default: semua slot diset recommended = false
        foreach ($jadwal as $j) { // Perulangan untuk semua slot jadwal yang ditarik dari database
            $j->recommended = false; // Mengatur status awal rekomendasi menjadi false (tidak disorot)
        }

        // Slot yang lolos seleksi Algoritma Greedy diset recommended = true
        foreach ($hasil as $tgl => $blocks) { // Perulangan per tanggal dari hasil akhir Algoritma Greedy
            foreach ($blocks as $block) { // Perulangan per blok kombinasi jam
                foreach ($block as $slot) { // Perulangan per slot jam dalam kombinasi pemenang
                    $slot->recommended = true; // Mengubah status rekomendasi menjadi true agar disorot biru di antarmuka
                }
            }
        }

        $pembayaranPending = null; // Menyiapkan variabel penampung status pembayaran pending

        if (auth()->check()) { // Cek apakah pengguna saat ini sedang login

            $pembayaranPending = Pemesanan::where('user_id', auth()->id()) // Cari transaksi pemesanan milik user
                ->where('status', 'pending') // Filter hanya pemesanan yang statusnya pending
                ->latest() // Urutkan dari transaksi yang paling terbaru
                ->first(); // Ambil transaksi pending terbaru

        }

        return view('reservasi.index', compact( // Mengirimkan seluruh variabel data ke file tampilan/view Blade
            'tanggal', // Variabel tanggal yang sedang dipilih
            'lapangan', // Daftar seluruh lapangan yang aktif
            'tanggalList', // Daftar 30 tanggal ke depan untuk kalender
            'hasil', // Hasil kombinasi rekomendasi Algoritma Greedy
            'jadwal', // Seluruh data slot jadwal beserta status rekomendasinya
            'isMember', // Status apakah memilih mode member atau non-member
            'memberSlots', // Data slot rekomendasi khusus member
            'pembayaranPending', // Data transaksi pending pengguna jika ada
            'jamBuka', // Jam operasional buka arena
            'jamTutup', // Jam operasional tutup arena
            'hargaPerJamDefault' // Harga sewa standar per jam
        ));
    }

    private function cariSlotMember( // Fungsi khusus pencarian rekomendasi slot paket rutin bagi pelanggan Member
        $tanggal, // Tanggal awal mulai berlangganan member
        $jamMulaiFilter = null, // Filter jam mulai opsional dari pengguna
        $jamAkhirFilter = null // Filter jam akhir opsional dari pengguna
    )
    {
        $hasilMember = []; // Menyiapkan array penampung hasil akhir paket member
        $pengaturan = active_arena(); // Mengambil data konfigurasi arena/cabang yang sedang aktif
        $jamBuka = $pengaturan ? (int) substr($pengaturan->jam_buka, 0, 2) : 8; // Mengambil angka jam buka operasional
        $jamTutup = $pengaturan ? (int) substr($pengaturan->jam_tutup, 0, 2) : 23; // Mengambil angka jam tutup operasional
        if ($jamTutup == 0) $jamTutup = 24; // Mengubah jam 00:00 menjadi 24 agar perbandingan rentang jam tetap valid
        $jumlahPekan = $pengaturan->member_jumlah_pekan ?? 4; // Mengambil durasi pekan paket (default 4 minggu)
        $jamPerPertemuan = $pengaturan->member_jam_per_pertemuan ?? 2; // Mengambil durasi main per pertemuan (default 2 jam)
        $pertemuanPerMinggu = $pengaturan->member_pertemuan_per_pekan ?? 1; // Mengambil frekuensi main per pekan (default 1 kali)

        // Eager load semua jadwal yang dibutuhkan untuk pengecekan hari ini
        $tanggalCekList = []; // Menyiapkan daftar tanggal untuk 4 pekan berturut-turut
        for ($minggu = 0; $minggu < $jumlahPekan; $minggu++) { // Perulangan sebanyak jumlah pekan paket (4 minggu)
            $tanggalCekList[] = \Carbon\Carbon::parse($tanggal)->addWeeks($minggu)->format('Y-m-d'); // Tambah 7 hari tiap pekan
        }
        $jamBukaStr = sprintf('%02d:00:00', $jamBuka); // Format string jam buka untuk query database
        $jamTutupStr = sprintf('%02d:00:00', $jamTutup); // Format string jam tutup untuk query database

        $lapanganIds = Lapangan::where('pengaturan_id', $pengaturan->id)->pluck('id'); // Ambil hanya ID lapangan milik cabang yang sedang aktif

        $preloadedJadwals = Jadwal::whereIn('lapangan_id', $lapanganIds) // Filter hanya jadwal lapangan milik cabang aktif
            ->whereIn('tanggal', $tanggalCekList) // Query data jadwal untuk 4 tanggal sekaligus (Eager Loading)
            ->where('status', 'tersedia') // Ambil hanya slot jadwal yang statusnya tersedia
            ->where('jam_mulai', '>=', $jamBukaStr) // Batasi jam mulai sama atau setelah jam buka
            ->where('jam_mulai', '<', $jamTutupStr) // Batasi jam mulai kurang dari jam tutup
            ->get(); // Eksekusi query tarik data dari MySQL

        // looping jam mulai member (maksimum jam mulai = jam tutup - jam per pertemuan)
        for ($jam = $jamBuka; $jam <= $jamTutup - $jamPerPertemuan; $jam++) { // Perulangan tiap kemungkinan jam mulai main
            if ($jamMulaiFilter && $jam < (int)substr($jamMulaiFilter,0,2)) { // Lewati jika jam kurang dari filter jam mulai pengguna
                continue; // Lanjut ke iterasi jam berikutnya
            }

            if ($jamAkhirFilter && ($jam + $jamPerPertemuan) > (int)substr($jamAkhirFilter,0,2)) { // Lewati jika jam melebih filter jam akhir
                continue; // Lanjut ke iterasi jam berikutnya
            }

            // Cari kombinasi terbaik dari semua lapangan
            $allValidPackages = []; // Menyiapkan tempat menampung seluruh paket member yang valid

            $lapangans = $lapanganIds; // Menguji hanya lapangan milik cabang aktif sebagai Lapangan Utama pilihan member
            foreach ($lapangans as $lapanganUtama) { // Menguji setiap lapangan sebagai Lapangan Utama pilihan member

                $validSemuaMinggu = true; // Penanda apakah paket 4 pekan ini sepenuhnya valid tanpa terputus
                $paketMingguan = []; // Tempat menyimpan rincian slot per pekan

                // cek $jumlahPekan minggu
                for ($minggu = 0; $minggu < $jumlahPekan; $minggu++) { // Perulangan mengecek dari Pekan 1 hingga Pekan 4

                    $tanggalCek = \Carbon\Carbon::parse($tanggal) // Ambil tanggal spesifik untuk pekan saat ini
                        ->addWeeks($minggu) // Tambah offset minggu
                        ->format('Y-m-d'); // Format menjadi YYYY-MM-DD

                    $slotJamMingguan = []; // Tempat menyimpan slot jam untuk pekan saat ini

                    // cari $jamPerPertemuan jam berurutan
                    for ($i = 0; $i < $jamPerPertemuan; $i++) { // Perulangan tiap jam main (misal 2 jam per pertemuan)

                        $jamMulai = sprintf('%02d:00:00', $jam + $i); // Format jam mulai target

                        $jadwalList = $preloadedJadwals // Mengambil kandidat slot dari data preloaded
                            ->where('tanggal', $tanggalCek) // Filter sesuai tanggal pekan saat ini
                            ->where('jam_mulai', $jamMulai) // Filter sesuai jam mulai target
                            ->sortBy(function ($item) use ($lapanganUtama) { // GREEDY SELECTION: Utamakan lapangan utama terlebih dahulu
                                return $item->lapangan_id == $lapanganUtama ? 0 : 1; // Lapangan utama diberi bobot 0 agar terpilih paling depan
                            })
                            ->filter(function ($item) { // Filter tambahan memastikan waktu slot belum berlalu
                                return !\Carbon\Carbon::parse($item->tanggal . ' ' . $item->jam_mulai)->isPast(); // Lewati jika waktu sudah lewat
                            })->values(); // Reset indeks array hasil filter

                        if ($jadwalList->count() == 0) { // Jika tidak ada satupun lapangan kosong pada jam ini
                            $validSemuaMinggu = false; // Tandai bahwa paket 4 pekan ini gagal/tidak valid
                            break; // Hentikan perulangan jam
                        }

                        // PRIORITAS GREEDY MEMBER:
                        // 1. gunakan lapangan yang sama dengan slot/pekan sebelumnya jika bisa
                        // 2. jika tidak ada, baru pakai lapangan alternatif
                        $dipilih = null; // Menyiapkan variabel slot yang akan dipilih

                        if (count($slotJamMingguan) > 0) { // Jika sudah ada slot jam sebelumnya dalam pekan ini
                            $lapSebelumnya = end($slotJamMingguan)->lapangan_id; // Ambil ID lapangan dari jam sebelumnya
                            $dipilih = $jadwalList // Cari slot pada jam sekarang yang lapangannya sama dengan jam sebelumnya
                                ->where('lapangan_id', $lapSebelumnya) // Filter ID lapangan yang sama
                                ->first(); // Ambil slot pertama yang cocok
                        }

                        // jika tidak ada lapangan yang sama
                        if (!$dipilih) { // Jika lapangan yang sama penuh/tidak tersedia
                            $dipilih = $jadwalList->first(); // Ambil slot lapangan alternatif pertama yang tersedia (Fallback Greedy)
                        }

                        $slotJamMingguan[] = $dipilih; // Tambahkan slot terpilih ke dalam daftar slot pekan ini
                    }

                    if (!$validSemuaMinggu) { // Jika salah satu jam di pekan ini gagal
                        break; // Batalkan pengecekan pekan selanjutnya
                    }

                    $paketMingguan[] = [ // Simpan rincian data pekan ini ke dalam paket mingguan
                        'minggu' => $minggu + 1, // Nomor urut pekan (1 sampai 4)
                        'tanggal' => $tanggalCek, // Tanggal spesifik pekan ini
                        'slots' => $slotJamMingguan // Daftar slot jam terpilih di pekan ini
                    ];
                }

                // kalau 4 minggu valid, hitung TOTAL perpindahan lapangan (baik di dalam 1 pekan MAUPUN antar pekan)
                if ($validSemuaMinggu) { // Jika seluruh 4 pekan berhasil terbentuk tanpa terputus
                    $totalSwitches = 0; // Inisialisasi total hitungan perpindahan lapangan
                    $lastLapId = null; // Menyiapkan penampung ID lapangan sebelumnya

                    foreach ($paketMingguan as $pm) { // Perulangan tiap pekan
                        foreach ($pm['slots'] as $slot) { // Perulangan tiap slot jam dalam pekan tersebut
                            if ($lastLapId !== null && $slot->lapangan_id != $lastLapId) { // Cek jika terjadi perubahan ID lapangan
                                $totalSwitches++; // Tambah total perpindahan lapangan
                            }
                            $lastLapId = $slot->lapangan_id; // Simpan ID lapangan saat ini untuk perbandingan berikutnya
                        }
                    }

                    $allValidPackages[] = [ // Simpan paket valid ke dalam daftar paket kandidat
                        'switches' => $totalSwitches, // Jumlah total perpindahan lapangan dalam paket ini
                        'paket' => $paketMingguan // Rincian detail paket 4 pekan
                    ];
                }
            }

            // Tambahkan ke hasil (tersedia ATAU tidak tersedia)
            if (count($allValidPackages) > 0) { // Jika ada paket valid yang terbentuk
                // Deduplikasi paket berdasarkan kombinasi slot ID
                $uniquePackages = []; // Tempat menampung paket unik tanpa duplikasi
                foreach ($allValidPackages as $item) { // Perulangan tiap paket kandidat
                    $pathIds = []; // Array penampung ID seluruh slot dalam paket
                    foreach ($item['paket'] as $minggu) { // Perulangan per pekan
                        foreach ($minggu['slots'] as $slot) { // Perulangan per slot jam
                            $pathIds[] = $slot->id; // Ambil ID slot jadwal
                        }
                    }
                    $pathKey = implode('_', $pathIds); // Gabungkan ID slot menjadi kunci unik string (contoh: "101_105_109_113")
                    if (!isset($uniquePackages[$pathKey]) || $item['switches'] < $uniquePackages[$pathKey]['switches']) { // Simpan hanya paket dengan switches terendah jika ada duplikat
                        $uniquePackages[$pathKey] = $item; // Simpan ke array unik
                    }
                }

                // URUTKAN BERDASARKAN TOTAL PERPINDAHAN LAPANGAN (SWITCHES) ASCENDING (GREEDY OPTIMAL FIRST)
                $sortedPackages = collect(array_values($uniquePackages))->sortBy('switches')->values(); // Urutkan paket: total switches terkecil menjadi Opsi 1

                foreach ($sortedPackages as $idx => $item) { // Perulangan paket yang sudah terurut
                    $hasilMember[] = [ // Masukkan ke dalam array hasil akhir member
                        'jam_awal' => sprintf('%02d:00', $jam), // Jam mulai pertemuan
                        'jam_akhir' => sprintf('%02d:00', $jam + $jamPerPertemuan), // Jam selesai pertemuan
                        'status' => 'tersedia', // Status paket member tersedia
                        'paket' => $item['paket'], // Rincian detail paket per pekan
                        'opsi' => $idx + 1 // Nomor urut opsi (Opsi 1 = paling optimal/tanpa berpindah)
                    ];
                }
            } else { // Jika tidak ada satupun paket valid yang terbentuk untuk jam ini
                $hasilMember[] = [ // Catat status jam ini tidak tersedia untuk paket member
                    'jam_awal' => sprintf('%02d:00', $jam), // Jam mulai
                    'jam_akhir' => sprintf('%02d:00', $jam + $jamPerPertemuan), // Jam selesai
                    'status' => 'tidak_tersedia', // Status tidak tersedia
                    'paket' => [] // Paket kosong
                ];
            }
        }

        return $hasilMember; // Kembalikan seluruh array hasil rekomendasi paket member
    }

    public function tiket($id) // Controller method untuk menampilkan halaman e-tiket digital
    {
        $pemesanan = Pemesanan::with('detail')->findOrFail($id); // Cari data pemesanan berdasarkan ID beserta detail slotnya

        return view('reservasi.tiket', compact('pemesanan')); // Tampilkan halaman view e-tiket dengan membawa data pemesanan
    }
    
}