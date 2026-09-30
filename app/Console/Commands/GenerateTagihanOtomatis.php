<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TagihanSchedule;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Notifications\TagihanBaruNotification;
use Carbon\Carbon;

class GenerateTagihanOtomatis extends Command
{
    protected $signature = 'tagihan:generate-otomatis {--force : Jalankan generator tanpa mengecek tanggal}';
    protected $description = 'Generate tagihan SPP otomatis berdasarkan jadwal tetap dan kirim notifikasi ke siswa';

    public function handle()
    {
        $today = Carbon::now();
        $currentDay = $today->day;
        
        // Sesuaikan angka bulan (01, 02, dst atau 1, 2)
        $currentMonth = $today->format('m'); // Angka bulan 2 digit (contoh: '09')
        $currentMonthAlt = $today->month;    // Angka bulan 1 digit (contoh: 9)
        $currentYear = $today->year;

        // Jika opsi --force aktif, abaikan tanggal terbit
        $query = TagihanSchedule::where('is_active', true);
        if (!$this->option('force')) {
            $query->where('generate_day', $currentDay);
        }

        $schedules = $query->get();

        if ($schedules->isEmpty()) {
            $this->info('Tidak ada jadwal tagihan aktif yang diproses.');
            return;
        }

        $siswas = Siswa::all();
        $count = 0;

        foreach ($schedules as $schedule) {
            // Tentukan tanggal jatuh tempo
            $dueDay = min($schedule->due_day, $today->daysInMonth);
            $dueDate = Carbon::create($currentYear, $today->month, $dueDay)->format('Y-m-d');

            foreach ($siswas as $siswa) {
                // Cek apakah tagihan bulan & tahun ini sudah pernah dibuat
                $exists = Tagihan::where('siswa_id', $siswa->id)
                    ->where(function($q) use ($currentMonth, $currentMonthAlt) {
                        $q->where('bulan', $currentMonth)
                          ->orWhere('bulan', $currentMonthAlt)
                          ->orWhere('periode', $currentMonth)
                          ->orWhere('periode', $currentMonthAlt);
                    })
                    ->where('tahun', $currentYear)
                    ->exists();

                if (!$exists) {
                    $tagihan = Tagihan::create([
                        'siswa_id' => $siswa->id,
                        'bulan'    => $currentMonth,
                        'periode'  => $currentMonth,
                        'tahun'    => $currentYear,
                        'nominal'  => $schedule->nominal,
                        'description' => $schedule->title,
                        'due_date' => $dueDate,
                        'status'   => 'Belum Bayar',
                    ]);

                    // Kirim notifikasi ke siswa
                    if ($siswa->user) {
                        $siswa->user->notify(new TagihanBaruNotification($tagihan));
                    }

                    $count++;
                }
            }
        }

        $this->info("Berhasil menerbitkan {$count} tagihan baru untuk siswa!");
    }
}