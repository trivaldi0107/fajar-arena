<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiket extends Model
{
    use HasFactory;

    protected $table = 'tiket';

    protected $fillable = [
        'pemesanan_id',
        'kode_tiket',
        'qr_code',
        'status',
        'download_count'
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class);
    }
}