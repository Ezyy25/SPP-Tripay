<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TripayService;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Notifications\TagihanPembayaranNotification;
use Illuminate\Support\Facades\Auth;

class TripayController extends Controller
{
    protected $tripayService;

    public function __construct(TripayService $tripayService)
    {
        $this->tripayService = $tripayService;
    }

    public function success($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $wasPaid = in_array(strtolower(trim((string) $tagihan->status)), ['paid', 'lunas', 'success', '1']);

        // Ubah status tagihan siswa menjadi lunas
        $tagihan->update([
            'status' => 'lunas',
        ]);

        // Simpan ke riwayat tabel Pembayaran
        $pembayaran = Pembayaran::firstOrCreate(
            ['tagihan_id' => $tagihan->id],
            [
                'kode_transaksi'    => 'TRX-' . time() . '-' . $tagihan->id,
                'siswa_id'          => $tagihan->siswa_id,
                'nominal'           => $tagihan->nominal,
                'bulan'             => $tagihan->bulan,
                'tahun'             => $tagihan->tahun,
                'metode_pembayaran' => $tagihan->payment_method ?? 'TriPay',
                'reference'         => $tagihan->reference,
                'paid_at'           => now(),
            ]
        );

        if (!$wasPaid && $tagihan->siswa) {
            $tagihan->siswa->notify(new TagihanPembayaranNotification($tagihan, $pembayaran));
        }

        return view('siswa.pembayaran.success', compact('tagihan'));
    }

    // Halaman Pilih Metode Pembayaran (Santri/Siswa)
    public function checkout($tagihan_id)
    {
        $tagihan = Tagihan::findOrFail($tagihan_id);

        // Ambil saluran pembayaran dari TriPay
        $channelsResponse = $this->tripayService->getPaymentChannels();

        // Logika ekstraksi data yang fleksibel
        $channels = [];

        if (is_array($channelsResponse)) {
            if (isset($channelsResponse['data']) && is_array($channelsResponse['data'])) {
                $channels = $channelsResponse['data'];
            } elseif (isset($channelsResponse['success']) && $channelsResponse['success'] === false) {
                $errorMessage = $channelsResponse['message'] ?? 'Gagal mengambil saluran pembayaran dari TriPay.';
                session()->flash('error', 'TriPay API Error: ' . $errorMessage);
            } else {
                $channels = $channelsResponse;
            }
        }

        return view('siswa.pembayaran.checkout', compact('tagihan', 'channels'));
    } 

    // Proses Buat Transaksi
    public function process(Request $request, $tagihan_id)
    {
        $request->validate([
            'method' => 'required|string',
        ]);

        $tagihan = Tagihan::findOrFail($tagihan_id);
        $user = Auth::user() ?? $tagihan->siswa;

        $response = $this->tripayService->requestTransaction($tagihan, $request->method, $user);

        // Cek jika transaksi berhasil
        if (isset($response['success']) && $response['success'] === true) {
            $data = $response['data'];

            $tagihan->update([
                'reference'      => $data['reference'],
                'payment_method' => $data['payment_method'],
                'checkout_url'   => $data['checkout_url'],
            ]);

            // Redirect langsung ke halaman checkout instruksi pembayaran TriPay
            return redirect()->away($data['checkout_url']);
        }

        // Ambil pesan error dari TriPay jika ada
        $errorMessage = $response['message'] ?? 'Gagal membuat transaksi di TriPay.';

        return back()->with('error', $errorMessage);
    }

    // Webhook Callback dari TriPay
    public function handleCallback(Request $request)
    {
        $callbackSignature = $request->header('X-Callback-Signature');
        $json = $request->getContent();

        $privateKey = config('tripay.private_key');
        $signature  = hash_hmac('sha256', $json, $privateKey);

        if ($signature !== $callbackSignature) {
            return response()->json(['success' => false, 'message' => 'Invalid Signature'], 400);
        }

        $data = json_decode($json, true);

        if ($request->header('X-Callback-Event') === 'payment_status') {
            $reference = $data['reference'];
            $status    = $data['status']; // PAID, EXPIRED, FAILED

            $tagihan = Tagihan::where('reference', $reference)->first();

            if ($tagihan) {
                if ($status === 'PAID') {
                    $wasPaid = in_array(strtolower(trim((string) $tagihan->status)), ['paid', 'lunas', 'success', '1']);
                    $tagihan->update([
                        'status' => 'lunas',
                    ]);

                    // Catat riwayat permanen ke tabel Pembayaran
                    $pembayaran = Pembayaran::firstOrCreate(
                        ['tagihan_id' => $tagihan->id],
                        [
                            'kode_transaksi'    => 'TRX-' . time() . '-' . $tagihan->id,
                            'siswa_id'          => $tagihan->siswa_id,
                            'nominal'           => $tagihan->nominal,
                            'bulan'             => $tagihan->bulan,
                            'tahun'             => $tagihan->tahun,
                            'metode_pembayaran' => $tagihan->payment_method ?? 'TriPay',
                            'reference'         => $reference,
                            'paid_at'           => now(),
                        ]
                    );

                    if (!$wasPaid && $tagihan->siswa) {
                        $tagihan->siswa->notify(new TagihanPembayaranNotification($tagihan, $pembayaran));
                    }

                } elseif (in_array($status, ['EXPIRED', 'FAILED'])) {
                    $tagihan->update([
                        'status' => 'unpaid',
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }
}