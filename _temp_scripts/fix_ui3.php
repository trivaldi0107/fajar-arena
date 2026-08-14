<?php
$file = 'resources/views/reservasi/index.blade.php';
$content = file_get_contents($file);

// Replace the specific messy time string
$content = preg_replace(
    '/>\s*\{\{\s*substr\(\$slot->jam_mulai,0,5\)\s*\}\}\s*-\s*\{\{\s*substr\(\$slot->jam_selesai,0,5\)\s*\}\}\s*</',
    '><span class="whitespace-nowrap">{{ substr($slot->jam_mulai,0,5) }} - {{ substr($slot->jam_selesai,0,5) }}</span><',
    $content
);
$content = preg_replace(
    '/>\s*\{\{\s*substr\(\$jam,0,5\)\s*\}\}\s*-\s*\{\{\s*substr\(\$items->first\(\)->jam_selesai,0,5\)\s*\}\}\s*</',
    '><span class="whitespace-nowrap">{{ substr($jam,0,5) }} - {{ substr($items->first()->jam_selesai,0,5) }}</span><',
    $content
);

file_put_contents($file, $content);

// For admin app.blade.php, add padding-bottom to the main wrapper instead of changing the animation.
$appFile = 'resources/views/admin/layouts/app.blade.php';
$appContent = file_get_contents($appFile);
// Ensure body doesn't have overflow-y-scroll
$appContent = preg_replace('/<body class="([^"]*)overflow-y-scroll([^"]*)"/', '<body class="$1$2"', $appContent);
$appContent = preg_replace('/<body class="([^"]*)overflow-x-hidden([^"]*)"/', '<body class="$1$2"', $appContent);

// Add padding bottom to main to prevent scrollbar jump
$appContent = str_replace('<main class="p-4 md:p-8 animate-page-fade-up">', '<main class="p-4 md:p-8 pb-12 animate-page-fade-up">', $appContent);

// Ensure translateY is back
if (strpos($appContent, 'transform: translateY(15px)') === false) {
    $appContent = str_replace(
        '@keyframes pageFadeUp { 0% { opacity: 0; transform: translateY(0); }',
        '@keyframes pageFadeUp { 0% { opacity: 0; transform: translateY(15px); }',
        $appContent
    );
    $appContent = str_replace(
        '@keyframes pageFadeUp { 0% { opacity: 0; }',
        '@keyframes pageFadeUp { 0% { opacity: 0; transform: translateY(15px); }',
        $appContent
    );
}

file_put_contents($appFile, $appContent);
echo "Fixed UI\n";
