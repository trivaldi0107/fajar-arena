<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LapanganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lapangan')->insert([
            [
                'nama_lapangan' => 'Lapangan 1',
                'harga_per_jam' => 50000,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_lapangan' => 'Lapangan 2',
                'harga_per_jam' => 50000,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}