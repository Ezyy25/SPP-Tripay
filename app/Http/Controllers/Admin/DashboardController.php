<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Hanya panggil relasi 'siswa' yang sudah terdefinisi di model Tagihan
        $transaksiTerbaru = Tagihan::with('siswa')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('transaksiTerbaru'));
    }
}