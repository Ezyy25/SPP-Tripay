<?php

namespace App\Notifications;

use App\Models\Tagihan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TagihanPengingatNotification extends Notification
{
    use Queueable;

    public function __construct(
        private Tagihan $tagihan,
        private int $daysFromDue
    ) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $details = $this->details();

        return (new MailMessage)
            ->subject($details['title'])
            ->greeting('Halo, ' . ($notifiable->name ?? $notifiable->nama ?? 'Siswa') . '!')
            ->line($details['message'])
            ->line("Periode: {$this->tagihan->bulan} / {$this->tagihan->tahun}")
            ->line('Jatuh tempo: ' . ($this->tagihan->due_date ?? '-'))
            ->line('Nominal: Rp ' . number_format($this->tagihan->nominal, 0, ',', '.'))
            ->line('Silakan segera melakukan pembayaran melalui portal sekolah.')
            ->salutation('Terima Kasih, Pengurus Sekolah');
    }

    public function toArray($notifiable)
    {
        return array_merge($this->details(), [
            'notification_type' => 'pengingat_tagihan',
            'tagihan_id' => $this->tagihan->id,
            'reminder_day' => $this->reminderDay(),
            'days_from_due' => $this->daysFromDue,
            'recipient_name' => $notifiable->name ?? $notifiable->nama ?? 'Siswa',
            'recipient_email' => $notifiable->routeNotificationFor('mail'),
            'sent_at' => now()->toDateTimeString(),
        ]);
    }

    private function details(): array
    {
        $label = $this->reminderDay();
        $message = match (true) {
            $this->daysFromDue > 0 => "Tagihan Anda jatuh tempo dalam {$this->daysFromDue} hari ({$label}) dan belum dibayar.",
            $this->daysFromDue === 0 => 'Tagihan Anda jatuh tempo hari ini (Hari H) dan belum dibayar.',
            default => 'Tagihan Anda terlambat ' . abs($this->daysFromDue) . ' hari (' . $label . ') dan belum dibayar.',
        };

        return [
            'title' => "Pengingat tagihan SPP - {$label}",
            'message' => $message,
        ];
    }

    private function reminderDay(): string
    {
        return match (true) {
            $this->daysFromDue > 0 => 'H-' . $this->daysFromDue,
            $this->daysFromDue === 0 => 'Hari H',
            default => 'H+' . abs($this->daysFromDue),
        };
    }
}