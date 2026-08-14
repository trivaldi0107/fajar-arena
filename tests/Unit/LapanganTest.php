<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Lapangan;
use App\Models\Pengaturan;
use App\Models\Jadwal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LapanganTest extends TestCase
{
    use RefreshDatabase;

    public function test_lapangan_belongs_to_pengaturan(): void
    {
        $arena = Pengaturan::create([
            'slug' => 'arena-1',
            'nama_arena' => 'Arena 1',
        ]);

        $lapangan = Lapangan::create([
            'pengaturan_id' => $arena->id,
            'nama_lapangan' => 'Lapangan A',
            'harga_per_jam' => 50000,
            'status' => 'aktif',
        ]);

        $this->assertInstanceOf(Pengaturan::class, $lapangan->pengaturan);
        $this->assertEquals($arena->id, $lapangan->pengaturan->id);
    }

    public function test_lapangan_has_many_jadwal(): void
    {
        $lapangan = Lapangan::create([
            'nama_lapangan' => 'Lapangan B',
            'harga_per_jam' => 60000,
            'status' => 'aktif',
        ]);

        $jadwal1 = Jadwal::create([
            'lapangan_id' => $lapangan->id,
            'tanggal' => '2026-08-20',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
            'status' => 'tersedia',
        ]);

        $jadwal2 = Jadwal::create([
            'lapangan_id' => $lapangan->id,
            'tanggal' => '2026-08-20',
            'jam_mulai' => '09:00',
            'jam_selesai' => '10:00',
            'status' => 'tersedia',
        ]);

        $this->assertCount(2, $lapangan->jadwal);
        $this->assertEquals('08:00', $lapangan->jadwal->first()->jam_mulai);
    }
}
