<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('pengaturan', function (Blueprint $table) {
    if (!Schema::hasColumn('pengaturan', 'fasilitas')) {
        $table->json('fasilitas')->nullable();
    }
    if (!Schema::hasColumn('pengaturan', 'fasilitas_tambahan')) {
        $table->text('fasilitas_tambahan')->nullable();
    }
});

echo "Columns added successfully";
