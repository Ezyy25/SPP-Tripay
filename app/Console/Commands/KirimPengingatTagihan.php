<?php

namespace App\Console\Commands;

use App\Models\Tagihan;
use App\Notifications\TagihanPengingatNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class KirimPengingatTagihan extends Command
{
    protected $signature = 'tagihan:kirim-pengingat';

    protected $description = 'Kirim pengingat tagihan yang belum dibayar sesuai tanggal jatuh tempo';

    public function handle(): int
    {
        $today = Carbon::today();
        $sentCount = 0;

        Tagihan::with(['siswa.user'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<=', $today->copy()->addDays(5))
            ->whereDoesntHave('pembayarans')
            ->chunkById(100, function ($tagihans) use ($today, &$sentCount) {
                foreach ($tagihans as $tagihan) {
                    if (in_array(strtolower(trim((string) $tagihan->status)), ['paid', 'lunas', 'success', '1'])) {
                        continue;
                    }

                    $siswa = $tagihan->siswa;
                    if (!$siswa || !($siswa->email_asli ?? $siswa->user?->email)) {
                        continue;
                    }

                    $daysFromDue = (int) $today->diffInDays(Carbon::parse($tagihan->due_date)->startOfDay(), false);
                    if (!in_array($daysFromDue, [5, 3, 1, 0]) && $daysFromDue >= 0) {
                        continue;
                    }

                    $reminderDay = $daysFromDue > 0 ? 'H-' . $daysFromDue : ($daysFromDue === 0 ? 'Hari H' : 'H+' . abs($daysFromDue));
                    $alreadySent = $siswa->notifications()
                        ->where('type', TagihanPengingatNotification::class)
                        ->where('data->tagihan_id', $tagihan->id)
                        ->where('data->reminder_day', $reminderDay)
                        ->exists();

                    if ($alreadySent) {
                        continue;
                    }

                    $siswa->notify(new TagihanPengingatNotification($tagihan, $daysFromDue));
                    $sentCount++;
                }
            });

        $this->info("Berhasil mengirim {$sentCount} pengingat tagihan.");

        return self::SUCCESS;
    }
}