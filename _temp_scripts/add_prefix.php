<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('pengaturan', 'prefix_lapangan')) {
    Schema::table('pengaturan', function (Blueprint $table) {
        $table->string('prefix_lapangan')->nullable()->default('Lapangan')->after('jumlah_lapangan');
    });
    echo "Added prefix_lapangan column.\n";
} else {
    echo "prefix_lapangan column already exists.\n";
}
