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
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_arena')->default('Fajar Arena Badminton');
            $table->string('jenis_olahraga')->default('badminton');
            $table->string('tagline')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('pengumuman')->nullable();
            
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kodepos')->nullable();
            $table->string('no_telp')->nullable();
            $table->string('email')->nullable();
            
            $table->string('gambar_utama')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
