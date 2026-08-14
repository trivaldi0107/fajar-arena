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
            if (!Schema::hasColumn('pengaturan', 'fasilitas')) {
                $table->json('fasilitas')->nullable();
            }
            if (!Schema::hasColumn('pengaturan', 'fasilitas_tambahan')) {
                $table->text('fasilitas_tambahan')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn('fasilitas');
            $table->dropColumn('fasilitas_tambahan');
        });
    }
};
