<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

protected $fillable = [
        'tagihan_id',
        'kode_transaksi',
        'siswa_id',
        'nominal',
        'bulan',
        'tahun',
        'metode_pembayaran',
        'reference',
        'paid_at',
    ];
    
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }
public function up()
{
    Schema::create('pembayarans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
        $table->foreignId('tagihan_id')->nullable()->constrained('tagihans')->onDelete('set null');
        $table->decimal('nominal', 12, 2);
        $table->integer('bulan');
        $table->integer('tahun');
        $table->string('metode_pembayaran')->nullable(); // TriPay, Manual, dll
        $table->string('reference')->nullable(); // Kode transaksi
        $table->timestamp('paid_at');
        $table->timestamps();
    });
}
public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
    
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
