<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TagihanSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TagihanScheduleController extends Controller
{
    public function index()
    {
        $schedules = TagihanSchedule::latest()->get();
        return view('admin.tagihan.schedule', compact('schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'generate_day' => 'required|integer|min:1|max:31',
            'due_day' => 'required|integer|min:1|max:31',
            'nominal' => 'required|numeric|min:0',
        ]);

        TagihanSchedule::create([
            'title' => $request->title,
            'generate_day' => $request->generate_day,
            'due_day' => $request->due_day,
            'nominal' => $request->nominal,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Jadwal tagihan otomatis berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $schedule = TagihanSchedule::findOrFail($id);

        // Jika hanya toggle status dari checkbox tabel
        if ($request->has('toggle_status_only')) {
            $schedule->update([
                'is_active' => $request->has('is_active'),
            ]);
            return redirect()->back()->with('success', 'Status jadwal berhasil diperbarui!');
        }

        // Update penuh (Edit Jadwal)
        $request->validate([
            'title' => 'required|string|max:255',
            'generate_day' => 'required|integer|min:1|max:31',
            'due_day' => 'required|integer|min:1|max:31',
            'nominal' => 'required|numeric|min:0',
        ]);

        $schedule->update([
            'title' => $request->title,
            'generate_day' => $request->generate_day,
            'due_day' => $request->due_day,
            'nominal' => $request->nominal,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.tagihan.schedule.index')->with('success', 'Jadwal tagihan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $schedule = TagihanSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->back()->with('success', 'Jadwal tagihan berhasil dihapus!');
    }

    public function runNow()
    {
        Artisan::call('tagihan:generate-otomatis', ['--force' => true]);
        return redirect()->back()->with('success', 'Proses generate tagihan otomatis telah berhasil dijalankan!');
    }
}