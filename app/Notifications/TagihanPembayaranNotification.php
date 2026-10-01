<?php

namespace App\Notifications;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class TagihanPembayaranNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Tagihan $tagihan,
        private ?Pembayaran $pembayaran = null
    ) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Terima kasih, pembayaran SPP telah kami terima')
            ->greeting('Halo, ' . ($notifiable->name ?? $notifiable->nama ?? 'Siswa') . '!')
            ->line($this->details()['message'])
            ->line("Periode: {$this->tagihan->bulan} / {$this->tagihan->tahun}")
            ->line('Nominal: Rp ' . number_format($this->tagihan->nominal, 0, ',', '.'))
            ->line('Tanggal pembayaran: ' . $this->paidAt()->format('d/m/Y H:i'))
            ->line('Terima kasih telah memenuhi kewajiban pembayaran. Semoga kegiatan belajar berjalan lancar.')
            ->salutation('Hormat kami, Pihak Sekolah');
    }

    public function toArray($notifiable)
    {
        return array_merge($this->details(), [
            'notification_type' => 'pembayaran_diterima',
            'tagihan_id' => $this->tagihan->id,
            'recipient_name' => $notifiable->name ?? $notifiable->nama ?? 'Siswa',
            'recipient_email' => $notifiable->routeNotificationFor('mail'),
            'amount' => $this->tagihan->nominal,
            'paid_at' => $this->paidAt()->toDateTimeString(),
            'sent_at' => now()->toDateTimeString(),
        ]);
    }

    private function details(): array
    {
        return [
            'title' => 'Pembayaran tagihan telah diterima',
            'message' => "Pembayaran SPP periode {$this->tagihan->bulan} {$this->tagihan->tahun} telah kami terima. Terima kasih.",
        ];
    }

    private function paidAt(): Carbon
    {
        return $this->pembayaran?->paid_at
            ? Carbon::parse($this->pembayaran->paid_at)
            : now();
    }
}