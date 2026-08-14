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
            if (!Schema::hasColumn('pengaturan', 'berita_list')) {
                $table->longText('berita_list')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            if (Schema::hasColumn('pengaturan', 'berita_list')) {
                $table->dropColumn('berita_list');
            }
        });
    }
};
