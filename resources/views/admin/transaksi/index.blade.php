@extends('layouts.admin')

@section('title', 'Transaksi TriPay - Admin SPP')
@section('page_title', 'Riwayat Transaksi TriPay')

@section('content')

<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold text-white tracking-tight">Riwayat Transaksi TriPay</h1>
        <p class="text-sm text-slate-400 mt-0.5">Daftar invoice & callback pembayaran otomatis yang masuk melalui gateway.</p>
    </div>

    <!-- Table Transaksi -->
    <div class="glass-card rounded-3xl p-6 border border-slate-800 space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs font-bold text-slate-400 uppercase bg-slate-800/40 border-b border-slate-800">
                    <tr>
                        <th class="p-3.5">Reference / TRX ID</th>
                        <th class="p-3.5">Santri</th>
                        <th class="p-3.5">Metode</th>
                        <th class="p-3.5">Total Bayar</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 text-slate-300">
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        <td class="p-3.5 font-mono text-xs text-indigo-400 font-bold">DEV-T242640001</td>
                        <td class="p-3.5 font-bold text-white">Ahmad Fahrezy</td>
                        <td class="p-3.5">BRIVA</td>
                        <td class="p-3.5 font-bold text-white">Rp 304.250</td>
                        <td class="p-3.5">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">PAID</span>
                        </td>
                        <td class="p-3.5 text-right">
                            <button class="px-3 py-1.5 rounded-lg bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-400 text-xs font-semibold transition-all">Detail Invoice</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection