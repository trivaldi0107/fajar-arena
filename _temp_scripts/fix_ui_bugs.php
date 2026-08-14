<?php

// 1. Fix admin scrollbar
$appBlade = 'resources/views/admin/layouts/app.blade.php';
$appContent = file_get_contents($appBlade);
$appContent = str_replace('overflow-y-scroll', 'overflow-x-hidden', $appContent);
file_put_contents($appBlade, $appContent);

// 2. Fix literal \n in reservasi
$reservasiBlade = 'resources/views/reservasi/index.blade.php';
$reservasiContent = file_get_contents($reservasiBlade);
// Replace literal backslash-n with actual space or newline
$reservasiContent = str_replace('\n', ' ', $reservasiContent);
file_put_contents($reservasiBlade, $reservasiContent);

echo "Fixed scrollbar and literal newlines.\n";
