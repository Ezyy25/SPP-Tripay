<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tagihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tagihans'; // Sesuaikan jika nama tabel beda

    // Pastikan 'nominal' tercantum di sini
    protected $fillable = [
        'siswa_id',
        'bulan',
        'tahun',
        'nominal', // <-- Tambahkan ini jika belum ada!
        'description',
        'status',
        'due_date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id'); // atau User::class
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_id');
    }
}