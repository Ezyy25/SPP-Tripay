@extends('layouts.siswa')

@section('title', 'Dashboard Siswa')

@section('content')
<div class="space-y-6">
    
    <!-- Welcome Header Card -->
    <div class="glass-card p-6 rounded-2xl border border-slate-800">
        <h1 class="text-2xl font-bold text-white">Selamat datang, {{ auth()->user()->name }}</h1>
        <p class="text-sm text-slate-400 mt-1">
            NISN: <span class="text-slate-200 font-semibold">{{ auth()->user()->siswa->nis ?? auth()->user()->nisn ?? '-' }}</span> | 
            Kelas: <span class="text-slate-200 font-semibold">{{ auth()->user()->siswa->kelas ?? auth()->user()->kelas ?? '-' }}</span>
        </p>
    </div>

    <!-- Tabel Daftar Tagihan & Tunggakan -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden">
        <div class="p-5 border-b border-slate-800 flex justify-between items-center">
            <h3 class="font-bold text-white text-base">Daftar Tagihan SPP Anda</h3>
            <span class="text-xs text-slate-400">Total Tagihan: {{ count($tagihans ?? []) }}</span>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-300">
                <thead class="text-xs uppercase bg-slate-800/60 text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="px-6 py-4">Periode</th>
                        <th class="px-6 py-4">Nominal</th>
                        <th class="px-6 py-4">Batas Akhir</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($tagihans as $tagihan)
                        @php
                            // Mengambil data jatuh tempo dari kolom due_date atau jatuhtempo
                            $dueDate = $tagihan->due_date ?? $tagihan->jatuhtempo ?? $tagihan->deadline;
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-semibold text-white">
                                {{ $tagihan->bulan ?? 'Bulan ' . $tagihan->periode }} / {{ $tagihan->tahun ?? date('Y') }}
                            </td>
                            <td class="px-6 py-4 font-bold text-indigo-400">
                                Rp {{ number_format($tagihan->nominal ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-slate-400">
                                {{ $dueDate ? \Carbon\Carbon::parse($dueDate)->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if(in_array(strtolower($tagihan->status), ['lunas', 'paid', 'success']))
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        Lunas
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(in_array(strtolower($tagihan->status), ['lunas', 'paid', 'success']))
                                    <span class="text-slate-500 text-xs font-semibold">Terbayar</span>
                                @else
                                    <a href="{{ route('siswa.pembayaran.checkout', $tagihan->id) }}" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-xs transition shadow-lg shadow-indigo-600/30">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Bayar Sekarang
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                Tidak ada tagihan SPP saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection