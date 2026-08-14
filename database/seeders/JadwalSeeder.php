<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    public function run()
    {

        $tanggalMulai = Carbon::today();

        for ($h = 0; $h < 30; $h++) {

            $tanggal = $tanggalMulai->copy()->addDays($h);

            for ($jam = 8; $jam < 23; $jam++) {

                $jamMulai = sprintf('%02d:00:00', $jam);
                $jamSelesai = sprintf('%02d:00:00', $jam + 1);

                for ($lapangan = 1; $lapangan <= 2; $lapangan++) {

                    Jadwal::create([
                        'lapangan_id' => $lapangan,
                        'tanggal' => $tanggal->format('Y-m-d'),
                        'jam_mulai' => $jamMulai,
                        'jam_selesai' => $jamSelesai,
                        'status' => 'tersedia'
                    ]);

                }

            }

        }
    }
}