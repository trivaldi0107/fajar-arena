<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservasiController;
use App\Models\Pemesanan;
use App\Http\Controllers\AdminController;
use App\Models\Jadwal;

Route::get('/sitemap.xml', function () {
    $sitemapPath = public_path('sitemap.xml');
    if (!file_exists($sitemapPath)) {
        $sitemapPath = base_path('public/sitemap.xml');
    }
    if (file_exists($sitemapPath)) {
        return response(file_get_contents($sitemapPath), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
    return response('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"><url><loc>https://fajararena.cloud/</loc></url></urlset>', 200, [
        'Content-Type' => 'application/xml',
    ]);
});

Route::get('/pembayaran/{id}', function ($id) {

    $pemesanan = Pemesanan::with('detail')->findOrFail($id);

    if ($pemesanan->status === 'berhasil') {
        return redirect()->route('tiket', $id);
    } elseif ($pemesanan->status === 'batal') {
        return redirect()->route('reservasi');
    } elseif ($pemesanan->status === 'proses') {
        return redirect()->route('pembayaran.menunggu', $id);
    }

    if ($pemesanan->jenis_user == 'member') {
        $hargaPerJam = null;
        $total = active_arena()->member_harga ?? 1000000;
    } else {
        $hargaPerJam = active_arena()->harga_per_jam ?? 80000;
        $jumlahJam = $pemesanan->durasi;
        $total = $jumlahJam * $hargaPerJam;
    }

    $qrisImage = active_arena()->qris_image ?? null;
    $rekeningBank = active_arena()->rekening_bank ?? null;

    return response()->view('pembayaran', [
        'pemesanan' => $pemesanan,
        'harga' => $hargaPerJam,
        'total' => $total,
        'qrisImage' => $qrisImage,
        'rekeningBank' => $rekeningBank
    ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
      ->header('Pragma', 'no-cache')
      ->header('Expires', '0');
});

Route::get('/pembayaran/menunggu/{id}', function ($id) {
    $pemesanan = Pemesanan::with('detail')->findOrFail($id);

    if ($pemesanan->status === 'berhasil') {
        return redirect()->route('tiket', $id);
    } elseif ($pemesanan->status === 'batal') {
        return redirect()->route('reservasi')->with('error', 'Pemesanan Anda telah dibatalkan.');
    } elseif ($pemesanan->status === 'pending') {
        return redirect('/pembayaran/' . $id);
    }

    if ($pemesanan->jenis_user == 'member') {
        $total = active_arena()->member_harga ?? 1000000;
    } else {
        $total = $pemesanan->durasi * (active_arena()->harga_per_jam ?? 80000);
    }

    return response()->view('pembayaran-menunggu', [
        'pemesanan' => $pemesanan,
        'total' => $total
    ])->header('Cache-Control', 'no-cache, no-store, must-revalidate')
      ->header('Pragma', 'no-cache')
      ->header('Expires', '0');
})->name('pembayaran.menunggu');

Route::post('/pembayaran/upload/{id}', function (\Illuminate\Http\Request $request, $id) {
    $pemesanan = Pemesanan::with('detail')->findOrFail($id);

    $request->validate([
        'bukti_transfer' => 'required|image|mimes:jpeg,png,jpg,webp|max:5048'
    ], [
        'bukti_transfer.required' => 'Silakan pilih foto bukti transfer terlebih dahulu.',
        'bukti_transfer.image' => 'File harus berupa gambar.',
        'bukti_transfer.mimes' => 'Format gambar yang diperbolehkan: JPG, PNG, WEBP.',
        'bukti_transfer.max' => 'Ukuran file maksimal 5MB.'
    ]);

    if ($request->hasFile('bukti_transfer')) {
        $file = $request->file('bukti_transfer');
        $filename = 'bukti_' . $pemesanan->kode_reservasi . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('bukti_transfer', $filename, 'public');
        $pemesanan->bukti_transfer = 'storage/' . $path;
    }

    $pemesanan->status = 'proses';
    $pemesanan->save();

    foreach ($pemesanan->detail as $d) {
        if ($d->jadwal_id) {
            \App\Models\Jadwal::where('id', $d->jadwal_id)->update(['status' => 'proses']);
        }
    }

    $firstDetail = $pemesanan->detail->first();
    $arena = ($firstDetail && $firstDetail->lapangan) ? $firstDetail->lapangan->pengaturan : null;
    $namaArena = $arena ? $arena->nama_arena : 'Fajar Arena';
    $caborName = $arena ? ($arena->jenis_olahraga ?: $arena->nama_arena) : '';

    try {
        \App\Helpers\WebPushHelper::sendToAdmins(
            $namaArena . ' 💳',
            'Bukti pembayaran (' . $pemesanan->kode_reservasi . ') cabor ' . $caborName . ' dari ' . ($pemesanan->user->name ?? 'Pelanggan') . ' telah diunggah.',
            route('admin.pemesanan.detail', $pemesanan->id)
        );
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('Push notification trigger error: ' . $e->getMessage());
    }

    return redirect()->route('pembayaran.menunggu', $pemesanan->id);
})->name('pembayaran.upload');

Route::get('/pembayaran/simulasi/{id}', function ($id) {

    $pemesanan = Pemesanan::with('detail')->findOrFail($id);

    if ($pemesanan->jenis_user == 'member') {
        $hargaPerJam = null;
        $total = active_arena()->member_harga ?? 1000000;
    } else {
        $hargaPerJam = active_arena()->harga_per_jam ?? 80000;
        $jumlahJam = $pemesanan->durasi;
        $total = $jumlahJam * $hargaPerJam;
    }

    return view('pembayaran-simulasi', [
        'pemesanan' => $pemesanan,
        'harga' => $hargaPerJam,
        'total' => $total
    ]);

})->name('pembayaran.simulasi');

Route::get('/', function () {
    // Beranda selalu memuat pengaturan utama (cabang pertama) sesuai permintaan "kecuali pengaturan beranda"
    $pengaturan = \App\Models\Pengaturan::first();
    $semuaCabang = \App\Models\Pengaturan::all();
    $sliders = $pengaturan ? \App\Models\Slider::where('pengaturan_id', $pengaturan->id)->orderBy('urutan', 'asc')->get() : collect();
    return view('beranda', compact('pengaturan', 'semuaCabang', 'sliders'));
})->name('beranda');

Route::get('/pilih-cabang', [\App\Http\Controllers\PortalController::class, 'index'])->name('portal');

Route::get('/set-arena/{slug}', [\App\Http\Controllers\PortalController::class, 'setArena'])->name('set-arena');

# DASHBOARD (tidak dipakai)
Route::get('/dashboard', function () {
    return redirect('/beranda');
})->middleware(['auth'])->name('dashboard');

# PROFILE (bisa diakses semua)
Route::get('/profile', function () {
    $pemesanans = collect([]);
    if (auth()->check()) {
        $pemesanans = \App\Models\Pemesanan::with('detail')
            ->where('user_id', auth()->id())
            ->where(function($q) {
                $q->where('tanggal_mulai', '>=', \Carbon\Carbon::today()->toDateString())
                  ->orWhere('created_at', '>=', \Carbon\Carbon::now()->subHours(24));
            })
            ->latest()
            ->get();
    }
    return view('profile.index', ['pemesanans' => $pemesanans]);
});

Route::delete('/pemesanan/{id}', function ($id) {
    if (!auth()->check()) return redirect('/login');
    $pemesanan = \App\Models\Pemesanan::where('user_id', auth()->id())->findOrFail($id);
    if (in_array($pemesanan->status, ['batal', 'dibatalkan'])) {
        $pemesanan->detail()->delete();
        $pemesanan->delete();
    }
    return back()->with('success', 'Riwayat pesanan berhasil dihapus.');
})->name('pemesanan.destroy');

# RESERVASI (dibuka untuk semua)
Route::get('/reservasi', [ReservasiController::class, 'index'])->name('reservasi');

Route::match(['get', 'post'], '/reservasi/filter', [ReservasiController::class, 'filter']);

# YANG BENAR-BENAR BUTUH LOGIN
Route::middleware('auth')->group(function () {

    Route::post('/reservasi/pesan', [ReservasiController::class, 'pesan'])->name('reservasi.pesan');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Rute untuk mengecek status pembayaran QRIS ke Midtrans
    Route::get('/cek-status/{order_id}', function ($orderId) {
        $serverKey = env('MIDTRANS_SERVER_KEY');
        $response = \Illuminate\Support\Facades\Http::withBasicAuth($serverKey, '')
            ->get("https://api.sandbox.midtrans.com/v2/{$orderId}/status");
        
        return response()->json($response->json());
    });
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/photo', [ProfileController::class, 'removePhoto'])->name('profile.photo.destroy');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});


Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    
    Route::get('/set-arena/{slug}', function ($slug) {
        session(['active_arena_slug' => $slug]);
        return back()->with('success', 'Berhasil beralih cabang olahraga.');
    })->name('admin.set_arena');

    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('admin.dashboard');

    Route::get('/pemesanan', [AdminController::class, 'pemesanan'])
        ->name('admin.pemesanan');

    Route::get('/scan', [AdminController::class, 'scan'])
        ->name('admin.scan');
    Route::post('/scan/process', [AdminController::class, 'processScan'])
        ->name('admin.scan.process');
    Route::post('/scan/checkin', [AdminController::class, 'checkIn'])
        ->name('admin.scan.checkin');
    Route::post('/scan/cancel-checkin', [AdminController::class, 'cancelCheckIn'])
        ->name('admin.scan.cancelCheckIn');


    Route::get('/pemesanan/latest-check', function () {
        $arena = active_arena();
        $latest = \App\Models\Pemesanan::with('user')
            ->where('pengaturan_id', $arena->id)
            ->where('status', 'proses')
            ->latest()
            ->first();
        $count = \App\Models\Pemesanan::where('pengaturan_id', $arena->id)
            ->where('status', 'proses')
            ->count();
        return response()->json([
            'count' => $count,
            'latest_id' => $latest ? $latest->id : null,
            'customer_name' => $latest && $latest->user ? $latest->user->name : 'Pelanggan',
            'cabor' => $arena->nama_lapangan ?? 'Badminton',
        ]);
    })->name('admin.pemesanan.latest_check');

    Route::get('/pemesanan/{id}', [AdminController::class, 'detailPemesanan'])
        ->name('admin.pemesanan.detail');
    Route::post('/pemesanan/konfirmasi/{id}', [AdminController::class, 'konfirmasiPemesanan'])
        ->name('admin.pemesanan.konfirmasi');
    Route::post('/pemesanan/tolak/{id}', [AdminController::class, 'tolakPemesanan'])
        ->name('admin.pemesanan.tolak');
    Route::post('/qris/upload', [AdminController::class, 'uploadQrisStatis'])
        ->name('admin.qris.upload');

    Route::get('/jadwal', [AdminController::class, 'jadwal'])
        ->name('admin.jadwal');

    Route::get('/jadwal/data', [AdminController::class, 'jadwalData'])
        ->name('admin.jadwal.data');

    Route::post('/jadwal/update', [AdminController::class, 'jadwalUpdate'])
        ->name('admin.jadwal.update');

    Route::post('/jadwal/update-all', [AdminController::class, 'jadwalUpdateAll'])
        ->name('admin.jadwal.update_all');

    Route::get('/beranda', [\App\Http\Controllers\BerandaAdminController::class, 'index'])
        ->name('admin.beranda.index');
    Route::get('/beranda/edit/{id}', [\App\Http\Controllers\BerandaAdminController::class, 'edit'])
        ->name('admin.beranda.edit');
    Route::post('/beranda/update/{id}', [\App\Http\Controllers\BerandaAdminController::class, 'update'])
        ->name('admin.beranda.update');

    Route::get('/lapangan', [AdminController::class, 'indexLapangan'])
        ->name('admin.lapangan.index');

    Route::get('/lapangan/create', [AdminController::class, 'createLapangan'])
        ->name('admin.lapangan.create');

    Route::get('/lapangan/edit/{id?}', [AdminController::class, 'editLapangan'])
        ->name('admin.lapangan.edit');

    Route::post('/lapangan/store', [AdminController::class, 'storeLapangan'])
        ->name('admin.lapangan.store');

    Route::post('/lapangan/update/{id}', [AdminController::class, 'updateLapangan'])
        ->name('admin.lapangan.update');

    Route::post('/lapangan/hapus-gambar', [AdminController::class, 'hapusGambar'])
        ->name('admin.lapangan.hapus_gambar');

    Route::post('/lapangan/hapus-pengumuman', [AdminController::class, 'hapusPengumuman'])
        ->name('admin.lapangan.hapus_pengumuman');

    Route::delete('/lapangan/destroy/{id}', [AdminController::class, 'destroyLapangan'])
        ->name('admin.lapangan.destroy');

    Route::post('/sliders', [AdminController::class, 'storeSlider'])
        ->name('admin.sliders.store');
    
    Route::post('/sliders/hapus/{id}', [AdminController::class, 'destroySlider'])
        ->name('admin.sliders.destroy');
        
    Route::post('/sliders/update', [AdminController::class, 'updateSlider'])
        ->name('admin.sliders.update');
});

Route::post('/pembayaran/proses/{id}', function ($id) {

    $pemesanan = \App\Models\Pemesanan::with('detail')->findOrFail($id);

    // ubah status pemesanan
    $pemesanan->status = 'berhasil';
    $pemesanan->save();

    // ubah status jadwal yang dipesan
    foreach ($pemesanan->detail as $detail) {

        Jadwal::where('id', $detail->jadwal_id)
            ->update([
                'status' => 'berhasil'
            ]);

    }

    return redirect('/pembayaran/berhasil/' . $id);

})->name('pembayaran.proses');

Route::post('/pembayaran/batal/{id}', function ($id) {

    $pemesanan = \App\Models\Pemesanan::with('detail')->findOrFail($id);

    // kembalikan jadwal menjadi tersedia
    foreach ($pemesanan->detail as $detail) {

        \App\Models\Jadwal::where('id', $detail->jadwal_id)
            ->update([
                'status' => 'tersedia'
            ]);
    }

    // ubah status pemesanan
    $pemesanan->status = 'batal';
    $pemesanan->save();

    return redirect()->route('reservasi');

})->name('pembayaran.batal');

Route::get('/pembayaran/berhasil/{id}', function ($id) {

    $pemesanan = \App\Models\Pemesanan::findOrFail($id);

    return view('pembayaran-berhasil', compact('pemesanan'));

})->name('pembayaran.berhasil');


Route::get('/reservasi/status/{tanggal}', function ($tanggal) {

    return \App\Models\Jadwal::where('tanggal', $tanggal)
        ->select('id', 'status')
        ->get();

})->name('reservasi.status');

Route::get('/tiket/{id}', [ReservasiController::class, 'tiket'])
    ->name('tiket');

Route::get('/pembayaran/status/{id}', function ($id) {

    return [
        'status' => \App\Models\Pemesanan::findOrFail($id)->status
    ];

})->name('pembayaran.status');

Route::get('/reservasi/pending', function () {

    if (!auth()->check()) {
        return ['pending' => false];
    }

    $pending = \App\Models\Pemesanan::where('user_id', auth()->id())
        ->where('status', 'pending')
        ->latest()
        ->first();

    return [
        'pending' => $pending ? true : false,
        'id' => $pending?->id
    ];

});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/check-new-orders', function () {
        $latest = \App\Models\Pemesanan::where('status', 'proses')
            ->with(['user', 'detail.lapangan.pengaturan'])
            ->latest('updated_at')
            ->first();
        $count = \App\Models\Pemesanan::where('status', 'proses')->count();

        $firstDetail = $latest ? $latest->detail->first() : null;
        $arena = ($firstDetail && $firstDetail->lapangan) ? $firstDetail->lapangan->pengaturan : null;
        $arenaName = $arena ? $arena->nama_arena : 'Fajar Arena';
        $caborName = $arena ? ($arena->jenis_olahraga ?: $arena->nama_arena) : '';

        return response()->json([
            'count' => $count,
            'latest_id' => $latest ? $latest->id : null,
            'latest_time' => $latest ? $latest->updated_at->timestamp : 0,
            'arena_name' => $arenaName,
            'cabor_name' => $caborName,
            'kode_reservasi' => $latest ? $latest->kode_reservasi : '',
            'customer_name' => $latest ? ($latest->user->name ?? 'Pelanggan') : 'Pelanggan',
        ]);
    })->name('admin.check_new_orders');

    Route::get('/admin/vapid-public-key', function () {
        $keys = \App\Helpers\WebPushHelper::getVapidKeys();
        return response()->json(['publicKey' => $keys['publicKey']]);
    })->name('admin.vapid_public_key');

    Route::post('/admin/push-subscribe', function (\Illuminate\Http\Request $request) {
        $endpoint = $request->input('endpoint');
        $p256dh = $request->input('keys.p256dh') ?: $request->input('p256dh');
        $auth = $request->input('keys.auth') ?: $request->input('auth');

        if (!$endpoint) {
            return response()->json(['error' => 'Endpoint missing'], 400);
        }

        $hash = md5($endpoint);

        \App\Models\PushSubscription::updateOrCreate(
            ['endpoint_hash' => $hash],
            [
                'user_id' => auth()->id(),
                'endpoint' => $endpoint,
                'public_key' => $p256dh,
                'auth_token' => $auth,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Notifikasi push berhasil diaktifkan!']);
    })->name('admin.push_subscribe');
});Route::get("/test-filter", function(\Illuminate\Http\Request $request) {
    $request->merge([
        "durasi" => 3,
        "tanggal_mulai" => "2026-08-02",
        "tanggal_akhir" => "2026-08-31",
        "jam_mulai" => "08:00",
        "jam_selesai" => "23:00",
    ]);
    return app(\App\Http\Controllers\ReservasiController::class)->filter($request);
});
