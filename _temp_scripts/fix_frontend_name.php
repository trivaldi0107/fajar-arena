<?php

// 1. Update Navigation
$navFile = 'resources/views/layouts/navigation.blade.php';
$navContent = file_get_contents($navFile);
$navContent = str_replace(
    "{{ active_arena()->nama_arena ?? 'Fajar Arena' }}",
    "{{ (request()->is('reservasi*') || request()->is('pilih-cabang*')) ? (active_arena()->nama_arena ?? 'Fajar Arena') : 'Fajar Arena' }}",
    $navContent
);
file_put_contents($navFile, $navContent);

// 2. Update Beranda
$berandaFile = 'resources/views/beranda.blade.php';
$berandaContent = file_get_contents($berandaFile);

// Footer
$berandaContent = str_replace(
    "{{ \$pengaturan->nama_arena ?? 'Fajar Arena' }}",
    "Fajar Arena",
    $berandaContent
);

// Hero title fallback (PHP)
$berandaContent = str_replace(
    "(\$pengaturan->nama_arena ?? 'Fajar Arena')",
    "'Fajar Arena'",
    $berandaContent
);

// JS Sliders 
$berandaContent = str_replace(
    "(isset(\$pengaturan) && !empty(\$pengaturan->nama_arena) ? addslashes(e(\$pengaturan->nama_arena)) : 'Fajar Arena')",
    "'Fajar Arena'",
    $berandaContent
);

file_put_contents($berandaFile, $berandaContent);

echo "Updated nama_arena to Fajar Arena globally except for reservations.\n";
