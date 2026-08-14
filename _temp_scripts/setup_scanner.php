<?php
// 1. Update routes/web.php
$routesFile = 'routes/web.php';
$routesContent = file_get_contents($routesFile);
if (strpos($routesContent, "admin.scan") === false) {
    $scanRoutes = <<<EOF
    Route::get('/scan', [AdminController::class, 'scan'])
        ->name('admin.scan');
    Route::post('/scan/process', [AdminController::class, 'processScan'])
        ->name('admin.scan.process');

EOF;
    $routesContent = str_replace(
        "Route::get('/pemesanan', [AdminController::class, 'pemesanan'])\n        ->name('admin.pemesanan');",
        "Route::get('/pemesanan', [AdminController::class, 'pemesanan'])\n        ->name('admin.pemesanan');\n\n" . $scanRoutes,
        $routesContent
    );
    file_put_contents($routesFile, $routesContent);
}

// 2. Update AdminController.php
$controllerFile = 'app/Http/Controllers/AdminController.php';
$controllerContent = file_get_contents($controllerFile);
if (strpos($controllerContent, "public function scan()") === false) {
    $scanMethods = <<<EOF

    public function scan()
    {
        return view('admin.scan.index');
    }

    public function processScan(\Illuminate\Http\Request \$request)
    {
        \$request->validate([
            'kode_reservasi' => 'required|string'
        ]);

        \$pemesanan = \App\Models\Pemesanan::with(['user', 'jadwals.lapangan'])
            ->where('kode_reservasi', \$request->kode_reservasi)
            ->first();

        if (!\$pemesanan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tiket tidak ditemukan.'
            ], 404);
        }

        \$jadwalPertama = \$pemesanan->jadwals->first();
        \$tanggal = \$jadwalPertama ? \Carbon\Carbon::parse(\$jadwalPertama->tanggal)->translatedFormat('d F Y') : '-';
        \$jamMulai = \$jadwalPertama ? substr(\$jadwalPertama->jam_mulai, 0, 5) : '-';
        \$jamSelesai = \$jadwalPertama ? substr(\$jadwalPertama->jam_selesai, 0, 5) : '-';
        
        \$namaLapangan = \$pemesanan->jadwals->map(function(\$j) {
            return \$j->lapangan->nama_lapangan;
        })->unique()->implode(', ');

        return response()->json([
            'status' => 'success',
            'data' => [
                'kode_reservasi' => \$pemesanan->kode_reservasi,
                'nama_pemesan' => \$pemesanan->user->name,
                'status_pembayaran' => \$pemesanan->status,
                'tanggal' => \$tanggal,
                'waktu' => \$jamMulai . ' - ' . \$jamSelesai,
                'lapangan' => \$namaLapangan
            ]
        ]);
    }
EOF;
    // Insert before the last closing brace
    $controllerContent = preg_replace('/}([\s\n]*)$/', $scanMethods . "\n}$1", $controllerContent);
    file_put_contents($controllerFile, $controllerContent);
}

// 3. Update sidebar.blade.php
$sidebarFile = 'resources/views/admin/partials/sidebar.blade.php';
$sidebarContent = file_get_contents($sidebarFile);
if (strpos($sidebarContent, "admin.scan") === false) {
    $scanMenu = <<<EOF

            <a href="{{ route('admin.scan') }}"
            class="flex items-center gap-4 px-5 py-4 rounded-xl mt-1 transition
            {{ request()->routeIs('admin.scan')
                    ? 'bg-blue-50 text-blue-600 font-semibold shadow-sm border border-blue-100/50'
                    : 'text-gray-500 font-medium hover:bg-slate-50 hover:text-blue-600' }}">

                <svg class="w-5 h-5 {{ request()->routeIs('admin.scan') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }} transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5Zm10.5 0c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 14.25 9.375v-4.5Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75Zm10.5 0h.75v.75h-.75v-.75Zm-10.5 10.5h.75v.75h-.75v-.75Zm10.5 0h.75v.75h-.75v-.75Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 19.125c0 .621.504 1.125 1.125 1.125h4.5c.621 0 1.125-.504 1.125-1.125v-4.5c0-.621-.504-1.125-1.125-1.125h-4.5c-.621 0-1.125.504-1.125 1.125v4.5Zm10.5-4.5c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5c-.621 0-1.125-.504-1.125-1.125v-4.5Z" />
                </svg>

                Scan Tiket

            </a>
EOF;
    $sidebarContent = str_replace(
        "Data Pemesanan\n\n            </a>",
        "Data Pemesanan\n\n            </a>\n" . $scanMenu,
        $sidebarContent
    );
    file_put_contents($sidebarFile, $sidebarContent);
}

echo "Routes, controller, and sidebar updated.\n";
