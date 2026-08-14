<?php
$file = 'resources/views/admin/jadwal.blade.php';
$content = file_get_contents($file);

// Replace table class to remove table-fixed and add min-w-[600px]
$content = str_replace('<table class="w-full text-sm text-left table-fixed">', '<table class="w-full text-sm text-left min-w-[700px]">', $content);

// Make the Waktu column sticky in the header
$oldTh = '<th class="pb-4 font-normal w-32">Waktu</th>';
$newTh = '<th class="pb-4 font-normal w-32 sticky left-0 bg-white z-20 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)]">Waktu</th>';
$content = str_replace($oldTh, $newTh, $content);

// Make the Waktu column sticky in the body
$oldTd = '<td class="py-3 font-semibold">${row.waktu}</td>';
$newTd = '<td class="py-3 font-semibold sticky left-0 bg-white z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">${row.waktu}</td>';
$content = str_replace($oldTd, $newTd, $content);

file_put_contents($file, $content);
echo "admin/jadwal.blade.php fixed for mobile.\n";
