<?php
// 1. UPDATE ROUTES
$routeFile = 'routes/web.php';
$routeContent = file_get_contents($routeFile);

$newRoute = "
    Route::get('/set-arena/{slug}', function (\$slug) {
        session(['active_arena_slug' => \$slug]);
        return back()->with('success', 'Berhasil beralih cabang olahraga.');
    })->name('admin.set_arena');
";

if (strpos($routeContent, 'admin.set_arena') === false) {
    $routeContent = str_replace(
        "Route::get('/dashboard', [AdminController::class, 'dashboard'])",
        $newRoute . "\n    Route::get('/dashboard', [AdminController::class, 'dashboard'])",
        $routeContent
    );
    file_put_contents($routeFile, $routeContent);
    echo "Route updated.\n";
}

// 2. UPDATE TOPBAR
$topbarFile = 'resources/views/admin/partials/topbar.blade.php';
$topbarContent = file_get_contents($topbarFile);

$startStr = '<!-- Cabang Olahraga Selector -->';
$endStr = '<!-- Right Menu -->';

$startPos = strpos($topbarContent, $startStr);
$endPos = strpos($topbarContent, $endStr);

if ($startPos !== false && $endPos !== false) {
    $oldBlock = substr($topbarContent, $startPos, $endPos - $startPos);
    
    // We only want to replace up to the third </div> which closes the left section
    
    $newBlock = '<!-- Cabang Olahraga Selector -->
    <div class="relative group cursor-pointer z-50">
        <div class="flex items-center gap-3 px-4 py-2 bg-slate-50 group-hover:bg-slate-100 rounded-xl border border-slate-200 transition-colors">
            <div class="flex flex-col">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider leading-none mb-1">Pilih Cabang</span>
                <span class="text-sm font-bold text-slate-800 leading-none">{{ active_arena()->nama_arena ?? \'Fajar Arena\' }}</span>
            </div>
            <svg class="w-4 h-4 text-slate-400 ml-4 hidden md:block transition-transform duration-200 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
        
        <!-- Dropdown Menu -->
        <div class="absolute left-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-left group-hover:translate-y-0 translate-y-2">
            <div class="py-2">
                @php
                    $arenas = \App\Models\Pengaturan::all();
                    $activeSlug = session(\'active_arena_slug\') ?: (\App\Models\Pengaturan::first()->slug ?? \'\');
                @endphp
                @foreach($arenas as $arena)
                    <a href="{{ route(\'admin.set_arena\', $arena->slug) }}" class="block px-4 py-3 hover:bg-slate-50 transition-colors {{ $activeSlug === $arena->slug ? \'bg-blue-50\' : \'\' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold {{ $activeSlug === $arena->slug ? \'text-blue-700\' : \'text-slate-800\' }}">{{ $arena->nama_arena }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">{{ $arena->jenis_olahraga }}</p>
                            </div>
                            @if($activeSlug === $arena->slug)
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    </div>

    ';
    
    $topbarContent = str_replace($oldBlock, $newBlock, $topbarContent);
    file_put_contents($topbarFile, $topbarContent);
    echo "Topbar updated.\n";
} else {
    echo "Failed to find markers in topbar.\n";
}
