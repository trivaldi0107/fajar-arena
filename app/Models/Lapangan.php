<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    use HasFactory;

    protected $table = 'lapangan';

    protected $fillable = [
        'pengaturan_id',
        'nama_lapangan',
        'harga_per_jam',
        'status'
    ];

    public function pengaturan()
    {
        return $this->belongsTo(Pengaturan::class);
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function pemesananDetail()
    {
        return $this->hasMany(PemesananDetail::class);
    }
}