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

    public function getWaktuSelesaiMainAttribute()
    {
        $details = $this->relationLoaded('detail') ? $this->detail : $this->detail()->get();
        if ($details->isNotEmpty()) {
            $latestDetail = $details->sortByDesc(function ($d) {
                return ($d->tanggal ?? '') . ' ' . ($d->jam_selesai ?? '');
            })->first();

            if ($latestDetail && $latestDetail->tanggal && $latestDetail->jam_selesai) {
                try {
                    return \Carbon\Carbon::parse($latestDetail->tanggal . ' ' . $latestDetail->jam_selesai);
                } catch (\Exception $e) {}
            }
        }

        if ($this->tanggal_mulai) {
            try {
                return \Carbon\Carbon::parse($this->tanggal_mulai)->endOfDay();
            } catch (\Exception $e) {}
        }

        return $this->created_at ? \Carbon\Carbon::parse($this->created_at) : now();
    }

    public function canBeDeleted()
    {
        $waktuSelesai = $this->waktu_selesai_main;
        if (!$waktuSelesai) {
            return true;
        }
        return $waktuSelesai->copy()->addHours(24)->isPast();
    }
}