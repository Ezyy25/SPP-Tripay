<?php

namespace App\Notifications;

use App\Models\Tagihan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TagihanBaruNotification extends Notification
{
    use Queueable;

    public $tagihan;

    public function __construct(Tagihan $tagihan)
    {
        $this->tagihan = $tagihan;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $namaSiswa = $notifiable->name ?? 'Siswa';
        $nominalFormatted = 'Rp ' . number_format($this->tagihan->nominal, 0, ',', '.');

        return (new MailMessage)
                    ->subject('Notifikasi Tagihan Pembayaran Baru')
                    ->greeting("Halo, {$namaSiswa}!")
                    ->line("Tagihan pembayaran baru telah diterbitkan dengan rincian berikut:")
                    ->line("• Periode: Bulan {$this->tagihan->bulan} / Tahun {$this->tagihan->tahun}")
                    ->line("• Nominal: {$nominalFormatted}")
                    ->line("• Deskripsi: " . ($this->tagihan->description ?? '-'))
                    ->line("• Jatuh Tempo: " . ($this->tagihan->due_date ?? '-'))
                    ->line("Silakan melakukan pembayaran tepat waktu melalui sistem portal sekolah.")
                    ->salutation('Terima Kasih, Pengurus Sekolah');
    }

    public function toArray($notifiable)
    {
        $namaSiswa = $notifiable->name ?? $notifiable->nama ?? 'Siswa';
        $nominalFormatted = 'Rp ' . number_format($this->tagihan->nominal, 0, ',', '.');

        return [
            'notification_type' => 'tagihan_baru',
            'title' => 'Tagihan pembayaran baru',
            'message' => "Tagihan bulan {$this->tagihan->bulan} {$this->tagihan->tahun} sebesar {$nominalFormatted} telah diterbitkan. Jatuh tempo: " . ($this->tagihan->due_date ?? '-'),
            'tagihan_id' => $this->tagihan->id,
            'recipient_name' => $namaSiswa,
            'recipient_email' => $notifiable->routeNotificationFor('mail'),
            'sent_at' => now()->toDateTimeString(),
        ];
    }
}