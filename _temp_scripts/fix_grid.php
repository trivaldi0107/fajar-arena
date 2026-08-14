<?php
$file = 'resources/views/reservasi/index.blade.php';
$content = file_get_contents($file);

// Replace grid-cols-{{ count($lapangan) + 1 }}
$oldClass = 'grid-cols-{{ count($lapangan) + 1 }}';
$newStyle = 'grid" style="grid-template-columns: 100px repeat({{ count($lapangan) }}, minmax(140px, 1fr));"';
$content = str_replace($oldClass, $newStyle, $content);

// Wrap the tables in overflow-x-auto so they can scroll
// First block (Search Results)
// We need to find: <div class="grid" style="grid-template-columns... <div>Waktu</div> ... @endforeach
// And close it after @foreach($block as $slot) ... @endforeach
// A simpler way is to find the parent container that holds these and just add `overflow-x-auto`.
// The parent container is already bg-white rounded-3xl etc.
$parentStr = '<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50/50 p-4 sm:p-6 md:p-8 mb-8">';
$newParentStr = '<div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-50/50 p-4 sm:p-6 md:p-8 mb-8 overflow-x-auto">';

// There are multiple occurrences of this wrapper. We can just add overflow-x-auto to all of them because it doesn't hurt.
$content = str_replace($parentStr, $newParentStr, $content);

file_put_contents($file, $content);
echo "reservasi/index.blade.php fixed.\n";
