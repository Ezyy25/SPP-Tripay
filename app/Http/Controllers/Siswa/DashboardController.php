<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Siswa; // Sesuaikan dengan Model Siswa kamu jika ada
use App\Models\Tagihan; // Sesuaikan jika ada

class DashboardController extends Controller
{
    public function index()
{
    $user = auth()->user();

    // 1. Cari data Siswa dari User yang sedang login
    $siswa = Siswa::where('user_id', $user->id)->first();

    // 2. Jika data Siswa ditemukan, ambil tagihannya berdasarkan $siswa->id
    if ($siswa) {
        $tagihans = Tagihan::where('siswa_id', $siswa->id)->latest()->get();
    } else {
        $tagihans = collect(); // Kosong jika belum ada profil siswa
    }

    return view('siswa.dashboard', compact('siswa', 'tagihans'));
}
    // Arahkan tombol Bayar ke halaman Checkout TriPay
    public function bayar(Request $request, $tagihanId)
    {
        $user = Auth::user();
        $siswa = Siswa::where('user_id', $user->id)->firstOrFail();

        // Pastikan tagihan tersebut memang milik siswa yang sedang login
        $tagihan = Tagihan::where('id', $tagihanId)
            ->where('siswa_id', $siswa->id)
            ->firstOrFail();

        // Redirect ke halaman pilih metode pembayaran TriPay
        return redirect()->route('santri.pembayaran.checkout', $tagihan->id);
    }
}