<?php

$navFile = 'resources/views/layouts/navigation.blade.php';
$navContent = file_get_contents($navFile);
$navContent = str_replace(
    "{{ (request()->is('reservasi*') || request()->is('pilih-cabang*')) ? (active_arena()->nama_arena ?? 'Fajar Arena') : 'Fajar Arena' }}",
    "{{ (auth()->check() && request()->is('reservasi*')) ? (active_arena()->nama_arena ?? 'Fajar Arena') : 'Fajar Arena' }}",
    $navContent
);
file_put_contents($navFile, $navContent);

echo "Updated navigation logic for Fajar Arena title.\n";
