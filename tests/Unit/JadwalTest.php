<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Lapangan;
use App\Models\Jadwal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JadwalTest extends TestCase
{
    use RefreshDatabase;

    public function test_jadwal_belongs_to_lapangan(): void
    {
        $lapangan = Lapangan::create([
            'nama_lapangan' => 'Lapangan 1',
            'harga_per_jam' => 45000,
            'status' => 'aktif',
        ]);

        $jadwal = Jadwal::create([
            'lapangan_id' => $lapangan->id,
            'tanggal' => '2026-08-21',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'status' => 'tersedia',
        ]);

        $this->assertInstanceOf(Lapangan::class, $jadwal->lapangan);
        $this->assertEquals('Lapangan 1', $jadwal->lapangan->nama_lapangan);
        $this->assertEquals('tersedia', $jadwal->status);
    }
}
