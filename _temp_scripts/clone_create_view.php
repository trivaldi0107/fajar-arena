<?php
$source = 'resources/views/admin/lapangan/edit.blade.php';
$dest = 'resources/views/admin/lapangan/create.blade.php';

$content = file_get_contents($source);

// Replace Title
$content = str_replace(
    "@section('title', 'Pengaturan Arena - Fajar Arena')",
    "@section('title', 'Tambah Lapangan - Fajar Arena')",
    $content
);

// Replace H2
$content = str_replace(
    '<h2 class="text-3xl font-bold text-gray-800">Pengaturan Arena</h2>',
    '<h2 class="text-3xl font-bold text-gray-800">Tambah Lapangan</h2>',
    $content
);

// Replace form action
$content = str_replace(
    'action="{{ route(\'admin.lapangan.update\', $pengaturan->id) }}"',
    'action="{{ route(\'admin.lapangan.store\') }}"',
    $content
);

// Remove any remaining @method('PUT') if they exist in edit (there isn't one because it uses POST with no PUT, but let's be sure)
$content = str_replace("@method('PUT')", '', $content);

file_put_contents($dest, $content);
echo "Cloned edit.blade.php to create.blade.php successfully.";
