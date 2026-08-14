<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pemesanan;
use App\Models\Tiket;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TiketTest extends TestCase
{
    use RefreshDatabase;

    public function test_tiket_belongs_to_pemesanan(): void
    {
        $user = User::factory()->create();
        $pemesanan = Pemesanan::create([
            'kode_reservasi' => 'RES-TIKET-001',
            'user_id' => $user->id,
            'jenis_user' => 'member',
            'durasi' => 4,
            'tanggal_mulai' => '2026-09-01',
            'status' => 'disetujui',
        ]);

        $tiket = Tiket::create([
            'pemesanan_id' => $pemesanan->id,
            'kode_tiket' => 'TKT-12345678',
            'qr_code' => 'data:image/svg+xml;base64,...',
            'status' => 'aktif',
            'download_count' => 1,
        ]);

        $this->assertInstanceOf(Pemesanan::class, $tiket->pemesanan);
        $this->assertEquals('RES-TIKET-001', $tiket->pemesanan->kode_reservasi);
        $this->assertEquals('aktif', $tiket->status);
        $this->assertEquals(1, $tiket->download_count);
    }
}
