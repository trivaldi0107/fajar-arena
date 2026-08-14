<?php
$file = 'routes/web.php';
$content = file_get_contents($file);

$oldRoute = <<<EOF
Route::get('/', function () {
    \$pengaturan = active_arena();
    \$sliders = \App\Models\Slider::where('pengaturan_id', \$pengaturan->id)->orderBy('urutan', 'asc')->get();
    return view('beranda', compact('pengaturan', 'sliders'));
})->name('beranda');
EOF;

$newRoute = <<<EOF
Route::get('/', function () {
    // Beranda selalu memuat pengaturan utama (cabang pertama) sesuai permintaan "kecuali pengaturan beranda"
    \$pengaturan = \App\Models\Pengaturan::first();
    \$sliders = \App\Models\Slider::where('pengaturan_id', \$pengaturan->id)->orderBy('urutan', 'asc')->get();
    return view('beranda', compact('pengaturan', 'sliders'));
})->name('beranda');
EOF;

$content = str_replace($oldRoute, $newRoute, $content);
file_put_contents($file, $content);
echo "Fixed routes/web.php to use Pengaturan::first() for Beranda\n";
