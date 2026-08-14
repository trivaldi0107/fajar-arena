<?php
$file = 'resources/views/admin/partials/topbar.blade.php';
$content = file_get_contents($file);

$content = preg_replace(
    '/<div\s+class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">([\s\S]*?)<\/div>/i',
    '<a href="{{ route(\'profile.edit\') }}" class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg cursor-pointer hover:bg-blue-700 hover:scale-105 transition-all shadow-sm">$1</a>',
    $content
);

file_put_contents($file, $content);
echo "Topbar avatar successfully updated with regex.\n";
