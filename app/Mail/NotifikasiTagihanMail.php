<?php

namespace App\Mail;

use App\Models\Tagihan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiTagihanMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tagihan;

    public function __construct(Tagihan $tagihan)
    {
        $this->tagihan = $tagihan;
    }

    public function build()
    {
        return $this->subject('Notifikasi Tagihan Pembayaran Baru')
                    ->html("
                        <h2>Halo, {$this->tagihan->siswa->user->name}</h2>
                        <p>Tagihan baru telah diterbitkan untuk Anda dengan rincian berikut:</p>
                        <ul>
                            <li><strong>Bulan/Tahun:</strong> {$this->tagihan->bulan} / {$this->tagihan->tahun}</li>
                            <li><strong>Nominal:</strong> Rp " . number_format($this->tagihan->nominal, 0, ',', '.') . "</li>
                            <li><strong>Deskripsi:</strong> " . e($this->tagihan->description ?? '-') . "</li>
                            <li><strong>Jatuh Tempo:</strong> " . ($this->tagihan->due_date ?? '-') . "</li>
                        </ul>
                        <p>Silakan lakukan pembayaran melalui sistem portal sekolah.</p>
                        <p>Terima kasih.</p>
                    ");
    }
}