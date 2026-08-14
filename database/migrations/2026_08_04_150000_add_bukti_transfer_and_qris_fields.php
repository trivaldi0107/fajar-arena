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
        Schema::table('pemesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pemesanan', 'bukti_transfer')) {
                $table->string('bukti_transfer')->nullable()->after('status');
            }
        });

        Schema::table('pengaturan', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaturan', 'qris_image')) {
                $table->string('qris_image')->nullable();
            }
            if (!Schema::hasColumn('pengaturan', 'rekening_bank')) {
                $table->text('rekening_bank')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pemesanan', 'bukti_transfer')) {
                $table->dropColumn('bukti_transfer');
            }
        });

        Schema::table('pengaturan', function (Blueprint $table) {
            if (Schema::hasColumn('pengaturan', 'qris_image')) {
                $table->dropColumn('qris_image');
            }
            if (Schema::hasColumn('pengaturan', 'rekening_bank')) {
                $table->dropColumn('rekening_bank');
            }
        });
    }
};
