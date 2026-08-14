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
            $table->string('promo_label')->nullable()->default('Promo Terbatas');
            $table->string('promo_judul')->nullable()->default('Jangan Lewatkan Kesempatan Ini!');
            $table->string('promo_teks_tombol')->nullable()->default('Ambil Promo Sekarang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn(['promo_label', 'promo_judul', 'promo_teks_tombol']);
        });
    }
};
