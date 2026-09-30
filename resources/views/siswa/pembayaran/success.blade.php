@extends('layouts.siswa') <!-- Atau sesuaikan dengan layout portal siswa Anda -->

@section('content')
<div class="container mx-auto p-6 max-w-lg text-center">
    <div class="bg-white rounded-2xl shadow-lg p-8 border border-green-100">
        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
            ✓
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Pembayaran Berhasil!</h2>
        <p class="text-gray-600 text-sm mb-6">
            Tagihan SPP untuk periode bulan <strong>{{ $tagihan->bulan }} / {{ $tagihan->tahun }}</strong> telah berhasil dilunasi.
        </p>

        <div class="bg-gray-50 rounded-xl p-4 mb-6 text-left border text-sm">
            <div class="flex justify-between py-1 border-b border-gray-200">
                <span class="text-gray-500">Nominal Tagihan:</span>
                <span class="font-bold text-gray-800">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between py-1 pt-2">
                <span class="text-gray-500">Status:</span>
                <span class="font-bold text-green-600 uppercase">{{ $tagihan->status }}</span>
            </div>
        </div>

        <a href="{{ route('siswa.dashboard') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl shadow transition">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection