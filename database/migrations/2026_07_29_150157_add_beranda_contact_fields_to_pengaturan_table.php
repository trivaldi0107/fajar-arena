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
            if (!Schema::hasColumn('pengaturan', 'beranda_alamat')) $table->text('beranda_alamat')->nullable();
            if (!Schema::hasColumn('pengaturan', 'beranda_kota')) $table->string('beranda_kota')->nullable();
            if (!Schema::hasColumn('pengaturan', 'beranda_no_telp')) $table->string('beranda_no_telp')->nullable();
            if (!Schema::hasColumn('pengaturan', 'beranda_email')) $table->string('beranda_email')->nullable();
            if (!Schema::hasColumn('pengaturan', 'beranda_link_maps')) $table->text('beranda_link_maps')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn([
                'beranda_alamat',
                'beranda_kota',
                'beranda_no_telp',
                'beranda_email',
                'beranda_link_maps'
            ]);
        });
    }
};
