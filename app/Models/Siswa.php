<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Siswa extends Model
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    /**
     * Mengarahkan pengiriman notifikasi email secara otomatis ke email_asli.
     * Fallback ke email user login jika email_asli kosong.
     */
    public function routeNotificationForMail($notification)
    {
        return $this->email_asli ?? optional($this->user)->email;
    }

    /**
     * Relasi ke Model User (Satu Siswa terhubung ke satu User)
     */
  public function user()
{
    return $this->belongsTo(User::class, 'user_id');
}

    /**
     * Relasi ke Model Tagihan (Satu Siswa punya banyak Tagihan)
     */
    public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'siswa_id');
    }

    /**
     * Relasi ke SppRate
     */
    public function sppRate()
    {
        return $this->belongsTo(SppRate::class, 'spp_rate_id');
    }

    // =========================================================================
    // ACCESSOR (Penyelamat agar $siswa->nama / $siswa->name tidak pernah N/A)
    // =========================================================================

    /**
     * Mengambil nama siswa secara otomatis dari tabel User jika di tabel Siswa kosong
     */
    public function getNamaAttribute()
    {
        return $this->attributes['nama'] 
            ?? $this->user->name 
            ?? $this->attributes['name'] 
            ?? 'N/A';
    }

    /**
     * Mengambil nama jika Blade memanggil $siswa->name
     */
    public function getNameAttribute()
    {
        return $this->user->name 
            ?? $this->attributes['name'] 
            ?? $this->attributes['nama'] 
            ?? 'N/A';
    }

    /**
     * Mengambil NIS dengan fallback aman jika null
     */
    public function getNisAttribute($value)
    {
        return $value ?? '-';
    }
}