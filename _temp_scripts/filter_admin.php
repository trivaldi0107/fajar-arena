<?php
$file = 'app/Http/Controllers/AdminController.php';
$content = file_get_contents($file);

// 1. Dashboard
$dashboardOld = <<<'EOD'
    public function dashboard()
    {
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

        // 2. Data Statistik
        $lapanganAktif = Lapangan::where('status', 'aktif')->count();

        $reservasiHariIni = Pemesanan::whereDate('created_at', today())
            ->where('status', '!=', 'batal')
            ->count();

        // Karena yang expired sudah dibatalkan di atas, kita tinggal hitung sisa yang pending
        $pending = Pemesanan::where('status', 'pending')->count();

        // Ambil data pesanan yang akan main HARI INI dan JAM MAINNYA BELUM LEWAT, selain yang batal
        $pemesananTerbaru = Pemesanan::whereHas('detail', function ($q) {
                $q->where('tanggal', today()->toDateString())
                  ->where('jam_selesai', '>', now()->format('H:i:s'));
            })
            ->where('status', '!=', 'batal')
            ->orderBy('created_at', 'desc')
            ->get();

        $chartLabels = [];
        $chartData = [];

        for ($i = -7; $i <= 30; $i++) {
            $date = \Carbon\Carbon::today()->addDays($i);
            // Dapatkan format tanggal dan singkatan bulan (misal: 16 Jul)
            $chartLabels[] = $date->translatedFormat('d M'); 
            $chartData[] = Pemesanan::whereDate('tanggal_mulai', $date)->count();
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
EOD;

$dashboardNew = <<<'EOD'
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

        // 2. Data Statistik tersaring berdasarkan Cabang Aktif
        $lapanganAktif = Lapangan::where('pengaturan_id', $activeArenaId)->where('status', 'aktif')->count();

        $reservasiHariIni = Pemesanan::whereHas('detail.lapangan', function($q) use ($activeArenaId) {
                $q->where('pengaturan_id', $activeArenaId);
            })
            ->whereDate('created_at', today())
            ->where('status', '!=', 'batal')
            ->count();

        $pending = Pemesanan::whereHas('detail.lapangan', function($q) use ($activeArenaId) {
                $q->where('pengaturan_id', $activeArenaId);
            })
            ->where('status', 'pending')
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
            })->whereDate('tanggal_mulai', $date)->count();
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
EOD;

$content = str_replace($dashboardOld, $dashboardNew, $content);

// 2. Pemesanan
$pemesananOld = <<<'EOD'
    public function pemesanan()
    {
        $pemesanan = Pemesanan::with([
            'user',
            'detail.lapangan'
        ])
        ->whereHas('detail')
        ->where('status', 'berhasil')
        ->where('created_at', '>=', now()->subDays(30))
        ->latest()
        ->paginate(10);

        return view('admin.pemesanan.index', compact('pemesanan'));
    }
EOD;

$pemesananNew = <<<'EOD'
    public function pemesanan()
    {
        $activeArenaId = active_arena()->id;
        $pemesanan = Pemesanan::with([
            'user',
            'detail.lapangan'
        ])
        ->whereHas('detail.lapangan', function($q) use ($activeArenaId) {
            $q->where('pengaturan_id', $activeArenaId);
        })
        ->where('status', 'berhasil')
        ->where('created_at', '>=', now()->subDays(30))
        ->latest()
        ->paginate(10);

        return view('admin.pemesanan.index', compact('pemesanan'));
    }
EOD;

$content = str_replace($pemesananOld, $pemesananNew, $content);

// 3. Jadwal Data
$jadwalDataOld = <<<'EOD'
    public function jadwalData(\Illuminate\Http\Request $request)
    {
        $tanggal = $request->tanggal;
        $lapangan = Lapangan::all();
EOD;

$jadwalDataNew = <<<'EOD'
    public function jadwalData(\Illuminate\Http\Request $request)
    {
        $tanggal = $request->tanggal;
        $lapangan = Lapangan::where('pengaturan_id', active_arena()->id)->get();
EOD;

$content = str_replace($jadwalDataOld, $jadwalDataNew, $content);

// 4. Jadwal Update All
$jadwalUpdateAllOld = <<<'EOD'
    public function jadwalUpdateAll(\Illuminate\Http\Request $request)
    {
        $tanggalList = $request->tanggalList;
        if (!$tanggalList || !is_array($tanggalList)) {
            return response()->json(['success' => false, 'message' => 'Invalid dates'], 400);
        }

        $status = strtolower($request->status);
        $lapangan = Lapangan::all();
EOD;

$jadwalUpdateAllNew = <<<'EOD'
    public function jadwalUpdateAll(\Illuminate\Http\Request $request)
    {
        $tanggalList = $request->tanggalList;
        if (!$tanggalList || !is_array($tanggalList)) {
            return response()->json(['success' => false, 'message' => 'Invalid dates'], 400);
        }

        $status = strtolower($request->status);
        $lapangan = Lapangan::where('pengaturan_id', active_arena()->id)->get();
EOD;

$content = str_replace($jadwalUpdateAllOld, $jadwalUpdateAllNew, $content);

// 5. Index Lapangan (Redirect directly to active arena edit page)
$indexLapanganOld = <<<'EOD'
    public function indexLapangan()
    {
        $pengaturans = Pengaturan::withCount('lapangan')->get();
        return view('admin.lapangan.index', compact('pengaturans'));
    }
EOD;

$indexLapanganNew = <<<'EOD'
    public function indexLapangan()
    {
        return redirect()->route('admin.lapangan.edit', active_arena()->id);
    }
EOD;

$content = str_replace($indexLapanganOld, $indexLapanganNew, $content);

file_put_contents($file, $content);

// 6. Sidebar Tambah Lapangan
$sidebarFile = 'resources/views/admin/partials/sidebar.blade.php';
$sidebarContent = file_get_contents($sidebarFile);

$kelolaLapanganStr = <<<'EOD'
              <a href="{{ route('admin.lapangan.index') }}" 
                 class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.lapangan.*') ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }}">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ request()->routeIs('admin.lapangan.*') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  Kelola Lapangan
              </a>
EOD;

$kelolaLapanganNew = <<<'EOD'
              <a href="{{ route('admin.lapangan.index') }}" 
                 class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.lapangan.index') || request()->routeIs('admin.lapangan.edit') ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }}">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ request()->routeIs('admin.lapangan.index') || request()->routeIs('admin.lapangan.edit') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  Kelola Lapangan
              </a>

              <a href="{{ route('admin.lapangan.create') }}" 
                 class="flex items-center gap-3 px-4 py-3.5 rounded-xl font-medium transition-all duration-200 {{ request()->routeIs('admin.lapangan.create') ? 'bg-blue-50 text-blue-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }}">
                  <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 {{ request()->routeIs('admin.lapangan.create') ? 'text-blue-600' : 'text-gray-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                  </svg>
                  Tambah Lapangan
              </a>
EOD;

$sidebarContent = str_replace($kelolaLapanganStr, $kelolaLapanganNew, $sidebarContent);
file_put_contents($sidebarFile, $sidebarContent);

echo "Update complete";
