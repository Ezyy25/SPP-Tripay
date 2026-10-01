<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\TagihanBaruNotification;
use App\Notifications\TagihanPembayaranNotification;

class TagihanController extends Controller
{
    /**
     * Menampilkan daftar tagihan
     */
    public function index()
    {
        $tagihans = Tagihan::with('siswa.user')
            ->whereDate('created_at', today())
            ->latest()
            ->paginate(10);

        // Ambil data Siswa beserta relasi User
        $siswas = Siswa::with('user')->get();

        // Fallback jika tabel siswas belum diisi tapi data ada di tabel User
        if ($siswas->isEmpty()) {
            $siswas = User::where('role', 'siswa')->get();
        }

        return view('admin.tagihan.index', compact('tagihans', 'siswas'));
    }

    /**
     * Menampilkan form tambah tagihan
     */
    public function create()
    {
        $siswas = Siswa::with('user')->get();

        if ($siswas->isEmpty()) {
            $siswas = User::where('role', 'siswa')->get();
        }

        return view('admin.tagihan.create', compact('siswas'));
    }

    /**
     * Menyimpan tagihan baru (Single / Bulk Generate) & Mengirimkan Email Notifikasi
     */
    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required',
            'nominal'  => 'required',
            'bulan'    => 'required|integer',
            'tahun'    => 'required|integer',
            'due_date' => 'nullable|date',
            'description' => 'required|string|max:1000',
        ]);

        $nominalClean = preg_replace('/[^0-9]/', '', $request->nominal);

        // -------------------------------------------------------------
        // OPSI 1: BULK GENERATE (Semua Santri)
        // -------------------------------------------------------------
        if ($request->siswa_id === 'all') {
            $siswas = Siswa::with('user')->get();

            // Jika tabel Siswa kosong, coba ambil dari tabel User yang ber-role siswa
            if ($siswas->isEmpty()) {
                $users = User::where('role', 'siswa')->get();
                
                if ($users->isEmpty()) {
                    return redirect()->back()->with('error', 'Tidak ada data santri/siswa ditemukan!');
                }

                $createdCount = 0;
                foreach ($users as $user) {
                    $siswa = Siswa::where('user_id', $user->id)->first();
                    if (!$siswa) {
                        continue;
                    }

                    $tagihan = Tagihan::firstOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'bulan'    => $request->bulan,
                            'tahun'    => $request->tahun,
                        ],
                        [
                            'nominal'  => $nominalClean,
                            'status'   => 'unpaid',
                            'due_date' => $request->due_date,
                            'description' => $request->description,
                        ]
                    );

                    if ($tagihan->wasRecentlyCreated) {
                        $createdCount++;
                        // Kirim Notifikasi (otomatis ke email_asli lewat routeNotificationForMail)
                        $siswa->notify(new TagihanBaruNotification($tagihan));
                    }
                }

                return redirect()->route('admin.tagihan.index')
                    ->with('success', "Berhasil membuat $createdCount tagihan baru dan mengirimkan email notifikasi!");
            }

            $createdCount = 0;
            foreach ($siswas as $siswa) {
                $tagihan = Tagihan::firstOrCreate(
                    [
                        'siswa_id' => $siswa->id,
                        'bulan'    => $request->bulan,
                        'tahun'    => $request->tahun,
                    ],
                    [
                        'nominal'  => $nominalClean,
                        'status'   => 'unpaid',
                        'due_date' => $request->due_date,
                        'description' => $request->description,
                    ]
                );

                if ($tagihan->wasRecentlyCreated) {
                    $createdCount++;
                    // Kirim Notifikasi
                    $siswa->notify(new TagihanBaruNotification($tagihan));
                }
            }

            return redirect()->route('admin.tagihan.index')
                ->with('success', "Berhasil membuat $createdCount tagihan baru dan mengirimkan email notifikasi!");
        }

        // -------------------------------------------------------------
        // OPSI 2: SINGLE GENERATE (Satu Santri)
  // -------------------------------------------------------------
// OPSI 2: SINGLE GENERATE (Satu Santri)
// -------------------------------------------------------------
$siswa = Siswa::with('user')->find($request->siswa_id);

if (!$siswa) {
    $siswa = Siswa::with('user')->where('user_id', $request->siswa_id)->first();
}

if (!$siswa) {
    return redirect()->back()->with('error', 'Data santri ini belum terdaftar lengkap!');
}

// Cari apakah ada tagihan di bulan & tahun yang sama yang BELUM LUNAS
$existingUnpaidTagihan = Tagihan::where('siswa_id', $siswa->id)
    ->where('bulan', $request->bulan)
    ->where('tahun', $request->tahun)
    ->whereIn('status', ['unpaid', 'pending'])
    ->first();

if ($existingUnpaidTagihan) {
    return redirect()->back()->with('error', 'Santri ini masih memiliki tagihan BELUM LUNAS pada periode bulan & tahun tersebut!');
}

// Jika tidak ada tagihan belum lunas (atau tagihan lama sudah 'paid'), buat tagihan baru
$tagihan = Tagihan::create([
    'siswa_id' => $siswa->id,
    'bulan'    => $request->bulan,
    'tahun'    => $request->tahun,
    'nominal'  => $nominalClean,
    'status'   => 'unpaid',
    'due_date' => $request->due_date,
    'description' => $request->description,
]);

// Kirim Notifikasi
$siswa->notify(new TagihanBaruNotification($tagihan));

return redirect()->route('admin.tagihan.index')->with('success', 'Tagihan berhasil dibuat dan email notifikasi telah terkirim!');
    }

    /**
     * Menampilkan Rekap Tagihan Berdasarkan Periode Bulan & Tahun
     */
    public function rekapPembayaran(Request $request)
    {
        $bulan = (int) $request->get('bulan', date('m'));
        $tahun = (int) $request->get('tahun', date('Y'));

        $tagihans = Tagihan::withTrashed()
            ->with(['siswa.user', 'pembayarans'])
            ->where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        $sudahBayar = $tagihans->filter(fn ($tagihan) => in_array(strtolower($tagihan->status), ['paid', 'lunas']))->values();
        $belumBayar = $tagihans->reject(fn ($tagihan) => in_array(strtolower($tagihan->status), ['paid', 'lunas']))->values();

        $totalUangMasuk = $sudahBayar->sum('nominal');
        $totalTunggakan = $belumBayar->sum('nominal');
        $totalTarget = $totalUangMasuk + $totalTunggakan;

        return view('admin.tagihan.rekap', compact(
            'sudahBayar', 'belumBayar', 'bulan', 'tahun',
            'totalUangMasuk', 'totalTunggakan', 'totalTarget'
        ));
    }

    /**
     * Mengubah status Lunas / Belum Lunas
     */
    public function toggleStatus($id)
    {
        $tagihan = Tagihan::withTrashed()->findOrFail($id);

        if (in_array(strtolower($tagihan->status), ['paid', 'lunas'])) {
            $tagihan->status = 'unpaid';
            \App\Models\Pembayaran::where('tagihan_id', $tagihan->id)->delete();
        } else {
            $tagihan->status = 'paid';
            $pembayaran = \App\Models\Pembayaran::create([
                'siswa_id' => $tagihan->siswa_id,
                'tagihan_id' => $tagihan->id,
                'nominal' => $tagihan->nominal,
                'bulan' => $tagihan->bulan,
                'tahun' => $tagihan->tahun,
                'paid_at' => now(),
            ]);
        }

        $tagihan->save();

        if (isset($pembayaran) && $tagihan->siswa) {
            $tagihan->siswa->notify(new TagihanPembayaranNotification($tagihan, $pembayaran));
        }

        return redirect()->back()->with('success', 'Status tagihan berhasil diperbarui!');
    }

    /**
     * Mengubah data tagihan
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nominal' => 'required',
            'status' => 'required',
            'due_date' => 'nullable|date',
            'description' => 'required|string|max:1000',
        ]);

        $tagihan = Tagihan::withTrashed()->findOrFail($id);
        $tagihan->update([
            'nominal' => preg_replace('/[^0-9]/', '', $request->nominal),
            'status' => $request->status,
            'due_date' => $request->due_date,
            'description' => $request->description,
        ]);

        $route = $request->input('redirect_to') === 'rekap' ? 'admin.tagihan.rekap' : 'admin.tagihan.index';
        return redirect()->route($route, $request->only(['bulan', 'tahun']))->with('success', 'Data tagihan berhasil diperbarui!');
    }

    /**
     * Menghapus tagihan
     */
    public function destroy($id)
    {
        $tagihan = Tagihan::withTrashed()->findOrFail($id);
        $tagihan->delete();

        $route = request('redirect_to') === 'rekap' ? 'admin.tagihan.rekap' : 'admin.tagihan.index';
        return redirect()->route($route, request()->only(['bulan', 'tahun']))->with('success', 'Tagihan berhasil dihapus!');
    }

    /**
     * Laporan Tunggakan
     */
    public function tunggakan()
    {
        $tagihans = Tagihan::whereIn('status', ['unpaid', 'belum_bayar'])->with('siswa.user')->paginate(10);
        return view('admin.tagihan.tunggakan', compact('tagihans'));
    }

    /**
     * Laporan Lunas
     */
    public function lunas()
    {
        $tagihans = Tagihan::whereIn('status', ['paid', 'lunas'])->with('siswa.user')->paginate(10);
        return view('admin.tagihan.lunas', compact('tagihans'));
    }
}