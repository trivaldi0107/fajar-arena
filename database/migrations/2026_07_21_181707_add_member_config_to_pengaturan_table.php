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
            $table->boolean('is_member_active')->default(true)->after('harga_per_jam');
            $table->integer('member_jumlah_pekan')->default(4)->after('is_member_active');
            $table->integer('member_pertemuan_per_pekan')->default(1)->after('member_jumlah_pekan');
            $table->integer('member_jam_per_pertemuan')->default(2)->after('member_pertemuan_per_pekan');
            $table->integer('member_harga')->default(1000000)->after('member_jam_per_pertemuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn([
                'is_member_active',
                'member_jumlah_pekan',
                'member_pertemuan_per_pekan',
                'member_jam_per_pertemuan',
                'member_harga'
            ]);
        });
    }
};
