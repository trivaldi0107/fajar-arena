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
        Schema::table('pemesanan_detail', function (Blueprint $table) {
            if (!Schema::hasColumn('pemesanan_detail', 'is_used')) {
                $table->boolean('is_used')->default(false)->after('jam_selesai');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan_detail', function (Blueprint $table) {
            $table->dropColumn('is_used');
        });
    }
};
