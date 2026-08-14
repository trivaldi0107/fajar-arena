<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pemesanan;
use App\Models\PemesananDetail;
use App\Models\Pembayaran;
use App\Models\Tiket;
use App\Models\Lapangan;
use App\Models\Jadwal;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PemesananTest extends TestCase
{
    use RefreshDatabase;

    public function test_pemesanan_creation_and_relations(): void
    {
        $user = User::factory()->create();

        $pemesanan = Pemesanan::create([
            'kode_reservasi' => 'RES-20260814-001',
            'user_id' => $user->id,
            'jenis_user' => 'non_member',
            'durasi' => 2,
            'tanggal_mulai' => '2026-08-20',
            'status' => 'menunggu_pembayaran',
            'is_used' => false,
        ]);

        $this->assertInstanceOf(User::class, $pemesanan->user);
        $this->assertEquals('RES-20260814-001', $pemesanan->kode_reservasi);
        $this->assertEquals('menunggu_pembayaran', $pemesanan->status);

        // Add lapangan & jadwal
        $lapangan = Lapangan::create([
            'nama_lapangan' => 'Lapangan 1',
            'harga_per_jam' => 50000,
            'status' => 'aktif',
        ]);

        $jadwal = Jadwal::create([
            'lapangan_id' => $lapangan->id,
            'tanggal' => '2026-08-20',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'status' => 'dibooking',
        ]);

        $detail = PemesananDetail::create([
            'pemesanan_id' => $pemesanan->id,
            'lapangan_id' => $lapangan->id,
            'jadwal_id' => $jadwal->id,
            'tanggal' => '2026-08-20',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
        ]);

        $this->assertCount(1, $pemesanan->detail);
        $this->assertInstanceOf(Jadwal::class, $detail->jadwal);

        // Add pembayaran
        $pembayaran = Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'metode' => 'transfer',
            'nominal' => 100000,
            'status' => 'menunggu_konfirmasi',
        ]);

        $this->assertInstanceOf(Pembayaran::class, $pemesanan->pembayaran);
        $this->assertEquals(100000, $pemesanan->pembayaran->nominal);

        // Add tiket
        $tiket = Tiket::create([
            'pemesanan_id' => $pemesanan->id,
            'kode_tiket' => 'TKT-998877',
            'qr_code' => 'data:image/svg+xml;base64,sample',
            'status' => 'aktif',
            'download_count' => 0,
        ]);

        $this->assertInstanceOf(Tiket::class, $pemesanan->tiket);
        $this->assertEquals('TKT-998877', $pemesanan->tiket->kode_tiket);
    }
}
