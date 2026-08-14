<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->integer('jumlah_lapangan')->default(4)->after('link_maps');
            $table->time('jam_buka')->default('08:00:00')->after('jumlah_lapangan');
            $table->time('jam_tutup')->default('22:00:00')->after('jam_buka');
            $table->integer('harga_per_jam')->default(80000)->after('jam_tutup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn(['jumlah_lapangan', 'jam_buka', 'jam_tutup', 'harga_per_jam']);
        });
    }
};
