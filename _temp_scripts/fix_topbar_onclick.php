<?php
$file = 'resources/views/admin/partials/topbar.blade.php';
$content = file_get_contents($file);

$oldStr = 'onclick="confirmDeleteTopbar({{ $arena->id }})"';
$newStr = 'onclick="event.preventDefault(); event.stopPropagation(); confirmDeleteTopbar({{ $arena->id }})"';

$content = str_replace($oldStr, $newStr, $content);
file_put_contents($file, $content);
echo "Fixed onclick handler in topbar.blade.php\n";
