<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Services\TripayService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    protected $tripayService;

    public function __construct(TripayService $tripayService)
    {
        $this->tripayService = $tripayService;
    }

    /**
     * Menampilkan daftar tagihan milik siswa yang sedang login.
     */
    public function index()
    {
        $siswa = Auth::user()->siswa;

        if (!$siswa) {
            return redirect()->back()->with('error', 'Data siswa tidak ditemukan.');
        }

        $tagihans = Tagihan::with(['sppRate', 'pembayarans'])
            ->where('siswa_id', $siswa->id)
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        return view('siswa.tagihan.index', compact('tagihans'));
    }

    /**
     * Proses pembuatan transaksi ke Tripay.
     */
    public function store(Request $request, $tagihanId)
    {
        $request->validate([
            'metode_pembayaran' => 'required|string', // Contoh: 'QRIS', 'BRIVA', 'BCAVA'
        ]);

        $siswa = Auth::user()->siswa;
        $tagihan = Tagihan::with('sppRate')->where('siswa_id', $siswa->id)->findOrFail($tagihanId);

        // Validasi jika tagihan sudah lunas
        if ($tagihan->status === 'paid') {
            return redirect()->back()->with('error', 'Tagihan ini sudah lunas.');
        }

        DB::beginTransaction();
        try {
            // 1. Request transaksi ke Tripay via Service
            $tripayResult = $this->tripayService->requestTransaction(
                $tagihan,
                $request->metode_pembayaran,
                $siswa
            );

            if (empty($tripayResult['success'])) {
                DB::rollBack();
                $pesan = $tripayResult['message'] ?? 'Tidak ada respons dari Tripay. Cek kembali konfigurasi API key.';
                return redirect()->back()->with('error', 'Gagal membuat transaksi: ' . $pesan);
            }

            $dataTripay = $tripayResult['data'];

            // 2. Simpan record pembayaran sementara di database (Status Pending)
            $pembayaran = Pembayaran::create([
                'tagihan_id'        => $tagihan->id,
                'kode_transaksi'    => $dataTripay['merchant_ref'],
                'metode_pembayaran' => $request->metode_pembayaran,
                'jumlah_bayar'      => $dataTripay['amount'],
                'tgl_bayar'         => now(),
                'reference_id_pg'   => $dataTripay['reference'],
            ]);

            // 3. Update status tagihan menjadi pending
            $tagihan->update(['status' => 'pending']);

            DB::commit();

            // Redirect ke halaman instruksi bayar atau tampilkan QRIS/URL Tripay
            return redirect()->route('siswa.pembayaran.show', $pembayaran->id)
                ->with('success', 'Transaksi berhasil dibuat. Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail pembayaran & instruksi cara bayar dari Tripay.
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::with(['tagihan.sppRate', 'tagihan.siswa.user'])
            ->findOrFail($id);

        // Opsional: Ambil detail instruksi pembayaran langsung dari API Tripay jika diperlukan
        $detailTripay = $this->tripayService->getDetailTransaction($pembayaran->reference_id_pg);

        return view('siswa.pembayaran.show', compact('pembayaran', 'detailTripay'));
    }
}