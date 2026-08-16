<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemesanan extends Model
{
    use HasFactory;

    protected $table = 'pemesanan';

    protected $fillable = [
        'kode_reservasi',
        'user_id',
        'jenis_user',
        'durasi',
        'tanggal_mulai',
        'status',
        'bukti_transfer',
        'alasan_penolakan',
        'is_used'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function detail()
    {
        return $this->hasMany(PemesananDetail::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function tiket()
    {
        return $this->hasOne(Tiket::class);
    }
}