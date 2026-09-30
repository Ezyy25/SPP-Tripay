<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tagihan;
use App\Models\Pembayaran;

class TripayCallbackController extends Controller
{
    public function handle(Request $request)
    {
        $privateKey = config('tripay.private_key');
        $callbackSignature = $request->header('X-Callback-Signature');
        $json = $request->getContent();

        // Validasi Signature bawaan Tripay agar aman
        $signature = hash_hmac('sha256', $json, $privateKey);

        if ($credentials = $callbackSignature !== $signature) {
            return response()->json(['success' => false, 'message' => 'Invalid signature'], 400);
        }

        $data = json_decode($json);

        if ($data->status === 'PAID') {
            // Logika update status tagihan & simpan riwayat pembayaran
            // Gantilah pencarian tagihan sesuai dengan kriteria transaksi kamu
            
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }
}