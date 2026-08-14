<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add slug to pengaturan
        Schema::table('pengaturan', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('id');
        });

        // Set default slug for existing pengaturan
        DB::table('pengaturan')->update(['slug' => 'badminton']);

        // 2. Add pengaturan_id to lapangan
        Schema::table('lapangan', function (Blueprint $table) {
            $table->unsignedBigInteger('pengaturan_id')->nullable()->after('id');
            // Assuming the first pengaturan is id 1
        });

        // Update existing lapangans to belong to the first pengaturan
        $firstPengaturan = DB::table('pengaturan')->first();
        if ($firstPengaturan) {
            DB::table('lapangan')->update(['pengaturan_id' => $firstPengaturan->id]);
        }

        // Add foreign key
        Schema::table('lapangan', function (Blueprint $table) {
            $table->foreign('pengaturan_id')->references('id')->on('pengaturan')->onDelete('cascade');
        });

        // 3. Add pengaturan_id to sliders
        Schema::table('sliders', function (Blueprint $table) {
            $table->unsignedBigInteger('pengaturan_id')->nullable()->after('id');
        });

        if ($firstPengaturan) {
            DB::table('sliders')->update(['pengaturan_id' => $firstPengaturan->id]);
        }

        Schema::table('sliders', function (Blueprint $table) {
            $table->foreign('pengaturan_id')->references('id')->on('pengaturan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropForeign(['pengaturan_id']);
            $table->dropColumn('pengaturan_id');
        });

        Schema::table('lapangan', function (Blueprint $table) {
            $table->dropForeign(['pengaturan_id']);
            $table->dropColumn('pengaturan_id');
        });

        Schema::table('pengaturan', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
