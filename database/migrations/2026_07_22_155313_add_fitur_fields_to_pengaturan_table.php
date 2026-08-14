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
            $table->string('fitur_judul')->nullable()->default('Mengapa memilih kami?');
            $table->string('fitur_deskripsi')->nullable()->default('Sistem reservasi cepat, aman, dan modern.');
            $table->json('fitur_cards')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn(['fitur_judul', 'fitur_deskripsi', 'fitur_cards']);
        });
    }
};
