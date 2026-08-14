<?php
$file = 'resources/views/reservasi/index.blade.php';
$content = file_get_contents($file);

$content = str_replace('"" style', '" style', $content);

file_put_contents($file, $content);
echo "Fixed double quotes.\n";
