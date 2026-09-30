<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\SiswaController as AdminSiswaController;
use App\Http\Controllers\Admin\TagihanController as AdminTagihanController;
use App\Http\Controllers\Admin\TagihanScheduleController as AdminTagihanScheduleController;
use App\Http\Controllers\Siswa\DashboardController as SiswaDashboardController;
use App\Http\Controllers\TripayController;

// Redirect Halaman Utama ke Login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes (Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Callback / Webhook dari TriPay (Public / Tanpa Auth)
Route::post('/api/tripay/callback', [TripayController::class, 'handleCallback'])->name('tripay.callback');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // PORTAL ADMIN
    Route::group([
        'prefix' => 'admin',
        'as' => 'admin.',
        'middleware' => [function ($request, $next) {
            $role = strtolower(trim(auth()->user()->role ?? ''));

            if ($role === 'admin') {
                return $next($request);
            }

            if ($role === 'siswa') {
                return redirect()->route('siswa.dashboard');
            }

            abort(403, 'Akses khusus Admin.');
        }]
    ], function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('siswa', AdminSiswaController::class);

        // Tagihan
        Route::get('/tagihan', [AdminTagihanController::class, 'index'])->name('tagihan.index');
        Route::get('/tagihan/create', [AdminTagihanController::class, 'create'])->name('tagihan.create');
        Route::post('/tagihan/store', [AdminTagihanController::class, 'store'])->name('tagihan.store');
        Route::post('/tagihan/generate', [AdminTagihanController::class, 'generate'])->name('tagihan.generate');
        Route::put('/tagihan/{id}', [AdminTagihanController::class, 'update'])->name('tagihan.update');
        Route::delete('/tagihan/{id}', [AdminTagihanController::class, 'destroy'])->name('tagihan.destroy');

        // Jadwal Tagihan Otomatis (Full CRUD & Run Now)
        Route::get('/tagihan/schedule', [AdminTagihanScheduleController::class, 'index'])->name('tagihan.schedule');
        Route::post('/tagihan/schedule', [AdminTagihanScheduleController::class, 'store'])->name('tagihan.schedule.store');
        Route::put('/tagihan/schedule/{id}', [AdminTagihanScheduleController::class, 'update'])->name('tagihan.schedule.update');
        Route::delete('/tagihan/schedule/{id}', [AdminTagihanScheduleController::class, 'destroy'])->name('tagihan.schedule.destroy');
        Route::post('/tagihan/schedule/run-now', [AdminTagihanScheduleController::class, 'runNow'])->name('tagihan.schedule.run-now');

        Route::patch('/tagihan/{id}/status', [AdminTagihanController::class, 'toggleStatus'])->name('tagihan.toggle-status');
        Route::get('/tunggakan-bulanan', [AdminTagihanController::class, 'tunggakan'])->name('tagihan.tunggakan');
        Route::get('/pembayaran-lunas', [AdminTagihanController::class, 'lunas'])->name('tagihan.lunas');
        Route::get('/tagihan/rekap', [AdminTagihanController::class, 'rekapPembayaran'])->name('tagihan.rekap');
    });

    // PORTAL SISWA
    Route::group([
        'prefix' => 'siswa',
        'as' => 'siswa.',
        'middleware' => [function ($request, $next) {
            $role = strtolower(trim(auth()->user()->role ?? ''));

            if ($role === 'siswa') {
                return $next($request);
            }

            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            abort(403, 'Akses khusus Siswa.');
        }]
    ], function () {
        Route::get('/dashboard', [SiswaDashboardController::class, 'index'])->name('dashboard');
        Route::post('/tagihan/{id}/bayar', [SiswaDashboardController::class, 'bayar'])->name('tagihan.bayar');

        // Route Checkout TriPay
        Route::get('/pembayaran/{id}/checkout', [TripayController::class, 'checkout'])->name('pembayaran.checkout');
        Route::post('/pembayaran/{id}/process', [TripayController::class, 'process'])->name('pembayaran.process');
        Route::get('/pembayaran/{id}/success', [TripayController::class, 'success'])->name('pembayaran.success');
    });

});