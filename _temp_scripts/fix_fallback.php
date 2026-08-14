<?php
$berandaFile = 'resources/views/beranda.blade.php';
$berandaContent = file_get_contents($berandaFile);

$berandaContent = str_replace(
    "isset(\$pengaturan) && !empty(\$pengaturan->nama_arena) ? addslashes(e(\$pengaturan->nama_arena)) : \n'Fajar Arena'",
    "'Fajar Arena'",
    $berandaContent
);

$berandaContent = preg_replace(
    "/isset\(\\\$pengaturan\)\s*&&\s*!empty\(\\\$pengaturan->nama_arena\)\s*\?\s*addslashes\(e\(\\\$pengaturan->nama_arena\)\)\s*:\s*'Fajar Arena'/",
    "'Fajar Arena'",
    $berandaContent
);

file_put_contents($berandaFile, $berandaContent);
echo "Fixed fallback title in beranda js\n";
