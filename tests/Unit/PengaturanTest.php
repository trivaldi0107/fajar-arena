<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Pengaturan;
use App\Models\Lapangan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PengaturanTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengaturan_casts_and_relations(): void
    {
        $cards = [
            ['title' => 'Karpet Standar BWF', 'desc' => 'Lantai vinyl kualitas internasional'],
            ['title' => 'Lampu LED Anti Silau', 'desc' => 'Penerangan optimal untuk malam hari'],
        ];

        $berita = [
            ['judul' => 'Turnamen Merdeka Cup', 'konten' => 'Pendaftaran dibuka'],
        ];

        $pengaturan = Pengaturan::create([
            'slug' => 'fajar-arena-pusat',
            'prefix_lapangan' => 'FA-PST',
            'nama_arena' => 'Fajar Arena Badminton',
            'jenis_olahraga' => 'Badminton',
            'is_member_active' => true,
            'fitur_cards' => $cards,
            'berita_list' => $berita,
            'jumlah_lapangan' => 4,
            'jam_buka' => '07:00',
            'jam_tutup' => '23:00',
        ]);

        $this->assertIsArray($pengaturan->fitur_cards);
        $this->assertEquals('Karpet Standar BWF', $pengaturan->fitur_cards[0]['title']);
        $this->assertIsArray($pengaturan->berita_list);
        $this->assertEquals('Turnamen Merdeka Cup', $pengaturan->berita_list[0]['judul']);
        $this->assertTrue($pengaturan->is_member_active);

        // Add lapangan
        Lapangan::create([
            'pengaturan_id' => $pengaturan->id,
            'nama_lapangan' => 'Court 1',
            'harga_per_jam' => 60000,
            'status' => 'aktif',
        ]);

        $this->assertCount(1, $pengaturan->lapangan);
    }
}
