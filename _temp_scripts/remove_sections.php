<?php
$c = file_get_contents('resources/views/admin/beranda/edit.blade.php');
$startMarker = '<!-- STEP 3: LOKASI & KONTAK -->';
$endMarker = '<!-- ACTION BUTTONS -->';

$startPos = strpos($c, $startMarker);
$endPos = strpos($c, $endMarker);

if ($startPos !== false && $endPos !== false) {
    $c = substr($c, 0, $startPos) . substr($c, $endPos);
    file_put_contents('resources/views/admin/beranda/edit.blade.php', $c);
    echo "Removed step 3 and step 4 from beranda edit.";
} else {
    echo "Markers not found.";
}
