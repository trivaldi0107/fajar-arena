<?php
$file = 'resources/views/admin/beranda/edit.blade.php';
$c = file_get_contents($file);

$search = '<div class="space-y-4">';
$replace = '<div class="space-y-4">
                            <input type="hidden" name="pengaturan_id" value="{{ $pengaturan->id }}">';

if (strpos($c, 'name="pengaturan_id"') === false) {
    $c = str_replace($search, $replace, $c);
    file_put_contents($file, $c);
    echo "Added pengaturan_id to blade.\n";
} else {
    echo "Already has pengaturan_id in blade.\n";
}

$controller = 'app/Http/Controllers/AdminController.php';
$c2 = file_get_contents($controller);

// For storeSlider
$searchStore1 = "'slider_gambar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',";
$replaceStore1 = "'pengaturan_id' => 'required|exists:pengaturan,id',
            'slider_gambar' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',";

$searchStore2 = "'judul' => \$request->slider_judul,";
$replaceStore2 = "'pengaturan_id' => \$request->pengaturan_id,
            'judul' => \$request->slider_judul,";

if (strpos($c2, "'pengaturan_id' => 'required|exists:pengaturan,id'") === false) {
    $c2 = str_replace($searchStore1, $replaceStore1, $c2);
    $c2 = str_replace($searchStore2, $replaceStore2, $c2);
    file_put_contents($controller, $c2);
    echo "Updated storeSlider in AdminController.\n";
} else {
    echo "storeSlider already updated.\n";
}
