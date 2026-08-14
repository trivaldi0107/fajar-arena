<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemesananDetail extends Model
{
    use HasFactory;

    protected $table = 'pemesanan_detail';

    protected $fillable = [
        'pemesanan_id',
        'lapangan_id',
        'jadwal_id',
        'minggu_ke',
        'tanggal',
        'jam_mulai',
        'jam_selesai'
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class);
    }
}