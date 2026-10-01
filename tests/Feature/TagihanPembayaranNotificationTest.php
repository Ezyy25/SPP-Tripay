<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use App\Notifications\TagihanPembayaranNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagihanPembayaranNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_success_page_records_one_thank_you_notification(): void
    {
        $user = User::factory()->create(['role' => 'siswa']);
        $siswa = $this->createSiswa($user);
        $tagihan = $this->createTagihan($siswa);

        $this->actingAs($user)->get(route('siswa.pembayaran.success', $tagihan->id))->assertOk();
        $this->actingAs($user)->get(route('siswa.pembayaran.success', $tagihan->id))->assertOk();

        $notifications = $siswa->notifications()
            ->where('type', TagihanPembayaranNotification::class)
            ->get();

        $this->assertCount(1, $notifications);
        $this->assertStringContainsString('Terima kasih', $notifications->first()->data['message']);
    }

    public function test_admin_manual_payment_records_thank_you_notification(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $siswa = $this->createSiswa(User::factory()->create());
        $tagihan = $this->createTagihan($siswa);

        $this->actingAs($admin)
            ->patch(route('admin.tagihan.toggle-status', $tagihan->id))
            ->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $siswa->id,
            'notifiable_type' => Siswa::class,
            'type' => TagihanPembayaranNotification::class,
        ]);
    }

    public function test_repeated_tripay_callback_records_one_thank_you_notification(): void
    {
        config(['tripay.private_key' => 'test-secret']);
        $user = User::factory()->create(['role' => 'siswa']);
        $siswa = $this->createSiswa($user);
        $tagihan = $this->createTagihan($siswa);
        $tagihan->update(['reference' => 'TRX-THANK-YOU']);

        $payload = ['reference' => 'TRX-THANK-YOU', 'status' => 'PAID'];
        $json = json_encode($payload);
        $signature = hash_hmac('sha256', $json, 'test-secret');

        foreach (range(1, 2) as $_) {
            $this->call('POST', '/api/tripay/callback', [], [], [], [
                'HTTP_X_CALLBACK_SIGNATURE' => $signature,
                'HTTP_X_CALLBACK_EVENT' => 'payment_status',
                'CONTENT_TYPE' => 'application/json',
            ], $json)->assertOk();
        }

        $this->assertSame(1, $siswa->notifications()
            ->where('type', TagihanPembayaranNotification::class)
            ->count());
    }

    private function createSiswa(User $user): Siswa
    {
        return Siswa::create([
            'user_id' => $user->id,
            'nis' => 'NIS-' . uniqid(),
            'nama' => $user->name,
            'email_asli' => $user->email,
        ]);
    }

    private function createTagihan(Siswa $siswa): Tagihan
    {
        return Tagihan::create([
            'siswa_id' => $siswa->id,
            'bulan' => '09',
            'tahun' => 2026,
            'nominal' => 250000,
            'description' => 'SPP',
            'status' => 'unpaid',
            'due_date' => '2026-10-05',
        ]);
    }
}