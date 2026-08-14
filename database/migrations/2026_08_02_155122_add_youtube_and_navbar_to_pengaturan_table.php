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
            if (!Schema::hasColumn('pengaturan', 'navbar_name')) $table->string('navbar_name')->nullable()->default('Fajar Arena');
            if (!Schema::hasColumn('pengaturan', 'youtube_link')) $table->string('youtube_link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn(['navbar_name', 'youtube_link']);
        });
    }
};
