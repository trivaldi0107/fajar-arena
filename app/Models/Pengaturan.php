<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    use HasFactory;

    protected $table = 'pengaturan';
    
    protected $fillable = [
        'slug',
        'prefix_lapangan',
        'nama_arena',
        'jenis_olahraga',
        'tagline',
        'deskripsi',
        'pengumuman',
        'promo_label',
        'promo_judul',
        'promo_teks_tombol',
        'gambar_pengumuman',
        'fitur_judul',
        'fitur_deskripsi',
        'fitur_cards',
        'alamat',
        'kota',
        'provinsi',
        'kodepos',
        'no_telp',
        'email',
        'gambar_utama',
        'link_maps',
        'jumlah_lapangan',
        'jam_buka',
        'jam_tutup',
        'harga_per_jam',
        'is_member_active',
        'member_jumlah_pekan',
        'member_pertemuan_per_pekan',
        'member_jam_per_pertemuan',
        'member_harga',
        'fasilitas',
        'fasilitas_tambahan',
        'beranda_alamat',
        'beranda_kota',
        'beranda_no_telp',
        'beranda_email',
        'beranda_link_maps',
        'navbar_name',
        'youtube_link',
        'qris_image',
        'rekening_bank',
        'catatan_member',
        'berita_list',
        'auth_bg_image'
    ];

    protected $casts = [
        'is_member_active' => 'boolean',
        'fitur_cards' => 'array',
        'berita_list' => 'array',
    ];

    public function lapangan()
    {
        return $this->hasMany(Lapangan::class);
    }

    public function sliders()
    {
        return $this->hasMany(Slider::class);
    }
}
