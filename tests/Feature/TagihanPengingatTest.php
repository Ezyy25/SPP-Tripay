<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use App\Notifications\TagihanPengingatNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TagihanPengingatTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_it_sends_each_due_day_reminder_once_and_skips_paid_bills(): void
    {
        Carbon::setTestNow('2026-09-30 00:10:00');
        Mail::fake();

        $siswa = $this->createSiswa();
        $this->createTagihan($siswa, '2026-10-05');
        $this->createTagihan($siswa, '2026-10-01');
        $this->createTagihan($siswa, '2026-09-30');
        $dueSoon = $this->createTagihan($siswa, '2026-10-03');
        $overdue = $this->createTagihan($siswa, '2026-09-29');
        $this->createTagihan($siswa, '2026-10-04');
        $this->createTagihan($siswa, '2026-10-01', 'paid');

        $this->artisan('tagihan:kirim-pengingat')->assertExitCode(0);
        $this->artisan('tagihan:kirim-pengingat')->assertExitCode(0);

        $notifications = $siswa->notifications()
            ->where('type', TagihanPengingatNotification::class)
            ->get();

        $this->assertCount(5, $notifications);
        $this->assertEqualsCanonicalizing(
            ['H-5', 'H-3', 'H-1', 'Hari H', 'H+1'],
            $notifications->map(fn ($notification) => $notification->data['reminder_day'])->all()
        );
        $this->assertTrue($notifications->contains(fn ($notification) => $notification->data['tagihan_id'] === $dueSoon->id));
        $this->assertTrue($notifications->contains(fn ($notification) => $notification->data['tagihan_id'] === $overdue->id));
    }

    private function createSiswa(): Siswa
    {
        $user = User::factory()->create();

        return Siswa::create([
            'user_id' => $user->id,
            'nis' => 'NIS-' . uniqid(),
            'nama' => $user->name,
            'email_asli' => $user->email,
        ]);
    }

    private function createTagihan(Siswa $siswa, string $dueDate, string $status = 'unpaid'): Tagihan
    {
        return Tagihan::create([
            'siswa_id' => $siswa->id,
            'bulan' => '09',
            'tahun' => 2026,
            'nominal' => 250000,
            'description' => 'SPP',
            'status' => $status,
            'due_date' => $dueDate,
        ]);
    }
}