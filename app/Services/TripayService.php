<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class TripayService
{
    protected $apiKey;
    protected $privateKey;
    protected $merchantCode;
    protected $baseUrl;

    public function __construct()
    {
        // Mengambil dari config(), jika null/kosong otomatis fallback mengambil dari env()
        $this->apiKey       = config('tripay.api_key') ?? env('TRIPAY_API_KEY');
        $this->privateKey   = config('tripay.private_key') ?? env('TRIPAY_PRIVATE_KEY');
        $this->merchantCode = config('tripay.merchant_code') ?? env('TRIPAY_MERCHANT_CODE');

        $isProduction = config('tripay.is_production') ?? env('TRIPAY_IS_PRODUCTION', false);

        $this->baseUrl = $isProduction
            ? 'https://tripay.co.id/api/'
            : 'https://tripay.co.id/api-sandbox/';
    }

    // Mengambil daftar saluran pembayaran (QRIS, VA, Minimarket, dll)
    public function getPaymentChannels()
    {
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . 'payment/channel');

        return $response->json();
    }

    // Membuat Transaksi Baru di TriPay
    public function requestTransaction($tagihan, $method, $user)
{
    $merchantRef = 'TRX-' . time() . '-' . $tagihan->id;

    $signature = hash_hmac('sha256', $this->merchantCode . $merchantRef . $tagihan->nominal, $this->privateKey);

    // Pastikan due_date terbaca dengan benar
    $dueDate = $tagihan->due_date ?? null;

    if ($dueDate) {
        // Konversi due_date ke jam 23:59:59 sesuai zona waktu lokal
        $expiredTime = Carbon::parse($dueDate)->endOfDay()->timestamp;
    } else {
        // Fallback 24 jam jika due_date tidak diisi admin
        $expiredTime = time() + (24 * 60 * 60);
    }

    $payload = [
        'method'         => $method,
        'merchant_ref'   => $merchantRef,
        'amount'         => (int) $tagihan->nominal,
        'customer_name'  => $user->nama ?? $user->name ?? 'Santri',
        'customer_email' => $user->email ?? $tagihan->siswa->email_asli ?? 'santri@example.com',
        'customer_phone' => $user->no_hp ?? '081234567890',
        'order_items'    => [
            [
                'sku'      => 'SPP-' . $tagihan->id,
                'name'     => 'Pembayaran SPP ' . $tagihan->bulan . ' ' . $tagihan->tahun,
                'price'    => (int) $tagihan->nominal,
                'quantity' => 1,
            ]
        ],
        'return_url'   => route('siswa.pembayaran.success', ['id' => $tagihan->id]),
        'expired_time' => (int) $expiredTime, // Wajib di-cast ke Integer Unix Timestamp
        'signature'    => $signature
    ];

    $response = Http::withoutVerifying()->withHeaders([
        'Authorization' => 'Bearer ' . $this->apiKey,
    ])->post($this->baseUrl . 'transaction/create', $payload);

    return $response->json();
}

    // Detail Transaksi TriPay
    public function getDetailTransaction($reference)
    {
        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . 'transaction/detail', [
            'reference' => $reference
        ]);

        return $response->json();
    }
}