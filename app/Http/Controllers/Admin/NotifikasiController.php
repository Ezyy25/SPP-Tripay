<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Notifications\TagihanBaruNotification;
use App\Notifications\TagihanPengingatNotification;
use Illuminate\Notifications\DatabaseNotification;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = DatabaseNotification::query()
            ->whereIn('type', [TagihanBaruNotification::class, TagihanPengingatNotification::class])
            ->latest()
            ->paginate(20);

        return view('admin.notifikasi.index', compact('notifikasi'));
    }
}