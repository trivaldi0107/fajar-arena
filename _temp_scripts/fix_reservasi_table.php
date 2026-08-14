<?php
$file = 'resources/views/reservasi/index.blade.php';
$content = file_get_contents($file);

// Replace <div>Waktu</div> with sticky class
$content = str_replace('<div>Waktu</div>', '<div class="sticky left-0 bg-white z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] px-2">Waktu</div>', $content);

// In the row loop, find the time div
// E.g., <div>{{ substr($slot->jam_mulai,0,5) }}...</div>
// It's a bit harder. Let's just use regex.

$content = preg_replace('/<div>\s*\{\{ substr\(\$slot->jam_mulai,0,5\) \}\}\s*-\s*\{\{ substr\(\$slot->jam_selesai,0,5\) \}\}\s*<\/div>/', '<div class="sticky left-0 bg-white z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] px-2 font-bold">\n                              {{ substr($slot->jam_mulai,0,5) }}\n                              -\n                              {{ substr($slot->jam_selesai,0,5) }}\n                          </div>', $content);

$content = preg_replace('/<div>\s*\{\{ substr\(\$jam,0,5\) \}\}\s*-\s*\{\{ substr\(\$items->first\(\)->jam_selesai,0,5\) \}\}\s*<\/div>/', '<div class="sticky left-0 bg-white z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] px-2 font-bold">\n                      {{ substr($jam,0,5) }} -\n                      {{ substr($items->first()->jam_selesai,0,5) }}\n                  </div>', $content);

file_put_contents($file, $content);
echo "reservasi/index.blade.php fixed for mobile.\n";
