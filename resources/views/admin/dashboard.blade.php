@extends('layouts.admin')

@section('title', 'Dashboard Admin SPP')
@section('page_title', 'Ringkasan Dashboard')

@section('content')

<div class="space-y-6">
    <!-- Banner Header Minimalis (Tanpa Tombol Buat Tagihan Baru) -->
    <div class="relative overflow-hidden rounded-3xl glass-card p-6 md:p-8 border border-indigo-500/20 shadow-2xl">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-2 max-w-xl">
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">Selamat Datang 👋</span>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Kelola Tagihan & Pembayaran SPP</h1>
                <p class="text-sm text-slate-400 leading-relaxed">Pantau pembayaran otomatis santri melalui TriPay payment gateway secara real-time.</p>
            </div>
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold self-start md:self-auto">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                TriPay Gateway Active
            </div>
        </div>
    </div>

    <!-- Tabel Transaksi Terbaru (Minimalis) -->
    <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-white tracking-tight">Transaksi Terbaru</h3>
            <a href="{{ route('admin.tagihan.index') }}" class="text-xs font-bold text-indigo-400 hover:text-indigo-300">Lihat Semua &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" id="table-transaksi">
                <thead class="text-xs font-bold text-slate-400 uppercase bg-slate-800/40 border-b border-slate-800">
                    <tr>
                        <th class="p-3.5">Santri</th>
                        <th class="p-3.5">Periode</th>
                        <th class="p-3.5">Metode</th>
                        <th class="p-3.5">Nominal</th>
                        <th class="p-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    @forelse($transaksiTerbaru as $trx)
                        @php
                            // Pengecekan status yang fleksibel (menangani status 'PAID', 'paid', 'Lunas', 'lunas', atau angka 1)
                            $statusRaw = strtoupper((string)$trx->status);
                            $isLunas = in_array($statusRaw, ['PAID', 'LUNAS', '1', 'SUCCESS']);
                            
                            // Ambil nama santri dari relasi yang tersedia
                            $namaSantri = $trx->siswa->name 
                                ?? $trx->siswa->nama 
                                ?? $trx->user->name 
                                ?? $trx->nama_siswa 
                                ?? 'Santri';
                        @endphp
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="p-3.5 font-bold text-white">{{ $namaSantri }}</td>
                            <td class="p-3.5 text-slate-400">{{ $trx->bulan ?? '' }} {{ $trx->tahun ?? '' }}</td>
                            <td class="p-3.5 text-slate-400">{{ $trx->payment_method ?? $trx->metode_pembayaran ?? '-' }}</td>
                            <td class="p-3.5 font-semibold text-white">Rp {{ number_format($trx->nominal ?? $trx->jumlah ?? 0, 0, ',', '.') }}</td>
                            <td class="p-3.5 text-center">
                                @if($isLunas)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">LUNAS</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">BELUM LUNAS</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-slate-500 text-sm">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script Polling Realtime (Memperbarui Tabel Setiap 5 Detik) -->
<script>
    setInterval(function() {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTbody = doc.querySelector('#table-transaksi tbody');
                const currentTbody = document.querySelector('#table-transaksi tbody');
                if (newTbody && currentTbody) {
                    currentTbody.innerHTML = newTbody.innerHTML;
                }
            })
            .catch(err => console.log('Error refreshing table:', err));
    }, 3000);
</script>
@endsection