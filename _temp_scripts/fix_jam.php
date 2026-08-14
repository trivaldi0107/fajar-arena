<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

foreach(\App\Models\Pengaturan::all() as $p) {
    $b = (int)substr($p->jam_buka, 0, 2);
    $t = (int)substr($p->jam_tutup, 0, 2);
    if($t == 0 && $p->jam_tutup == '00:00:00') $t = 24;
    if($t <= $b) {
        $p->update(['jam_tutup' => '23:00:00']);
        echo "Updated Pengaturan ID {$p->id} (set jam_tutup to 23:00:00)\n";
    }
}
