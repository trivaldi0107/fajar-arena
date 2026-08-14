<?php
// 1. Fix reservasi/index.blade.php spacing
$reservasiFile = 'resources/views/reservasi/index.blade.php';
$content = file_get_contents($reservasiFile);

// Use regex to remove multiple spaces around the time variables
$content = preg_replace('/\s+\{\{\s*substr\(\$slot->jam_mulai,0,5\)\s*\}\}\s+-\s+\{\{\s*substr\(\$slot->jam_selesai,0,5\)\s*\}\}\s+/', '{{ substr($slot->jam_mulai,0,5) }} - {{ substr($slot->jam_selesai,0,5) }}', $content);
$content = preg_replace('/\s+\{\{\s*substr\(\$jam,0,5\)\s*\}\}\s+-\s+\{\{\s*substr\(\$items->first\(\)->jam_selesai,0,5\)\s*\}\}\s+/', '{{ substr($jam,0,5) }} - {{ substr($items->first()->jam_selesai,0,5) }}', $content);

file_put_contents($reservasiFile, $content);

// 2. Fix admin layout scrollbar jump
$appFile = 'resources/views/admin/layouts/app.blade.php';
$appContent = file_get_contents($appFile);
$appContent = str_replace('transform: translateY(15px);', '', $appContent);
$appContent = str_replace('transform: translateY(0);', '', $appContent);
file_put_contents($appFile, $appContent);

echo "UI Bugs Fixed.\n";
