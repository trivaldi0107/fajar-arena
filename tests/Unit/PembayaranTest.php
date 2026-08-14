<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PembayaranTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembayaran_belongs_to_pemesanan(): void
    {
        $user = User::factory()->create();
        $pemesanan = Pemesanan::create([
            'kode_reservasi' => 'RES-TEST-002',
            'user_id' => $user->id,
            'jenis_user' => 'non_member',
            'durasi' => 1,
            'tanggal_mulai' => '2026-08-22',
            'status' => 'disetujui',
        ]);

        $pembayaran = Pembayaran::create([
            'pemesanan_id' => $pemesanan->id,
            'metode' => 'qris',
            'nominal' => 75000,
            'status' => 'lunas',
            'waktu_bayar' => now(),
        ]);

        $this->assertInstanceOf(Pemesanan::class, $pembayaran->pemesanan);
        $this->assertEquals('RES-TEST-002', $pembayaran->pemesanan->kode_reservasi);
        $this->assertEquals('qris', $pembayaran->metode);
        $this->assertEquals('lunas', $pembayaran->status);
    }
}
