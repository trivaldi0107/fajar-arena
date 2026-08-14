<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
foreach ($tables as $t) {
    foreach ($t as $k => $v) {
        echo "Table: " . $v . "\n";
    }
}
