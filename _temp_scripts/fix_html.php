<?php
$file = 'resources/views/reservasi/index.blade.php';
$content = file_get_contents($file);

// The bad replacement string was:
// grid" style="grid-template-columns: 100px repeat({{ count($lapangan) }}, minmax(140px, 1fr));"

// We need to fix it. We want to find:
// <div class="grid grid" style="grid-template-columns: 100px repeat({{ count($lapangan) }}, minmax(140px, 1fr));" text-sm font-semibold border-b border-gray-100 pb-3 mb-5 text-gray-600">
// And turn it into:
// <div class="grid text-sm font-semibold border-b border-gray-100 pb-3 mb-5 text-gray-600 min-w-[max-content]" style="grid-template-columns: 100px repeat({{ count($lapangan) }}, minmax(140px, 1fr));">

// Since there are 4 of these, with slightly different dangling classes, let's use regex to fix them.

$pattern = '/<div class="grid grid" style="grid-template-columns: 100px repeat\(\{\{ count\(\$lapangan\) \}\}, minmax\(140px, 1fr\)\);" ([^>]+)>/s';
$replacement = '<div class="grid min-w-[max-content] $1" style="grid-template-columns: 100px repeat({{ count($lapangan) }}, minmax(140px, 1fr));">';

$content = preg_replace($pattern, $replacement, $content);

file_put_contents($file, $content);
echo "Fixed malformed HTML in reservasi/index.blade.php\n";
