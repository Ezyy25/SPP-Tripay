<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagihanVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_unpaid_tagihan_is_visible_in_rekap_and_siswa_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'siswa']);
        $siswa = $this->createSiswa($user);
        $tagihan = $this->createTagihan($siswa);

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.tagihan.rekap', ['bulan' => 10, 'tahun' => 2026]))
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee('Rp 100.000');

        $this->actingAs($user)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Belum Bayar')
            ->assertSee('Rp 100.000');
    }

    public function test_soft_deleted_unpaid_tagihan_is_not_counted_as_tunggakan(): void
    {
        $user = User::factory()->create(['role' => 'siswa']);
        $siswa = $this->createSiswa($user);
        $tagihan = $this->createTagihan($siswa);
        $tagihan->delete();

        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.tagihan.rekap', ['bulan' => 10, 'tahun' => 2026]))
            ->assertOk()
            ->assertSee('Rp 0')
            ->assertSee('Tidak ada tunggakan');

        $this->actingAs($user)
            ->get(route('siswa.dashboard'))
            ->assertOk()
            ->assertSee('Tidak ada tagihan SPP saat ini.');
    }

    private function createSiswa(User $user): Siswa
    {
        return Siswa::create([
            'user_id' => $user->id,
            'nis' => 'NIS-' . uniqid(),
            'nama' => $user->name,
        ]);
    }

    private function createTagihan(Siswa $siswa): Tagihan
    {
        return Tagihan::create([
            'siswa_id' => $siswa->id,
            'bulan' => '10',
            'tahun' => 2026,
            'nominal' => 100000,
            'description' => 'SPP Bulanan',
            'status' => 'unpaid',
        ]);
    }
}
