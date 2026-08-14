<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwal';

    protected $fillable = [
        'lapangan_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'status'
    ];

    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class);
    }

    public function pemesananDetail()
    {
        return $this->hasMany(PemesananDetail::class);
    }
}