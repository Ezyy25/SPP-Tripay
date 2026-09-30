@extends('layouts.admin')

@section('title', 'Buat Tagihan - Admin SPP')
@section('page_title', 'Buat Tagihan Baru')

@section('content')

<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-white tracking-tight">Terbitkan Tagihan SPP</h1>
            <p class="text-sm text-slate-400 mt-0.5">Buat tagihan SPP untuk perorangan atau seluruh santri sekaligus.</p>
        </div>
        <a href="{{ route('admin.tagihan.index') }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all">
            &larr; Batal
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-rose-500/10 border border-rose-500/20 text-rose-400 p-4 rounded-xl text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Glass Card -->
    <div class="glass-card rounded-3xl p-6 md:p-8 border border-slate-800 shadow-xl">
        <form action="{{ route('admin.tagihan.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Target Santri -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Penerima Tagihan</label>
                <select name="siswa_id" required class="w-full bg-slate-900/90 text-sm text-white px-4 py-3 rounded-xl border border-slate-800 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition-all">
                    <option value="all">-- Generasi Tagihan untuk SEMUA Santri --</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}">{{ $siswa->name ?? $siswa->nama }} (NIS: {{ $siswa->nis ?? '-' }}) - {{ $siswa->kelas ?? '-' }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Bulan -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Bulan Periode</label>
                    <select name="bulan" required class="w-full bg-slate-900/90 text-sm text-white px-4 py-3 rounded-xl border border-slate-800 focus:outline-none focus:border-indigo-500">
                        @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $index => $namaBulan)
                            <option value="{{ $index + 1 }}">{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tahun -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Tahun Periode</label>
                    <input type="number" name="tahun" value="{{ date('Y') }}" required class="w-full bg-slate-900/90 text-sm text-white px-4 py-3 rounded-xl border border-slate-800 focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- Nominal SPP -->
            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Nominal SPP (Rp)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                    <input type="text" name="nominal" value="300.000" inputmode="numeric" required class="nominal-input w-full bg-slate-900/90 text-sm text-white font-bold pl-12 pr-4 py-3 rounded-xl border border-slate-800 focus:outline-none focus:border-indigo-500">
                </div>
                <p class="text-[11px] text-slate-500">*Minimal nominal Rp 10.000 untuk kompatibilitas gateway TriPay.</p>
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold text-slate-300 uppercase tracking-wider">Deskripsi Tagihan</label>
                <textarea name="description" rows="3" required class="w-full bg-slate-900/90 text-sm text-white px-4 py-3 rounded-xl border border-slate-800 focus:outline-none focus:border-indigo-500" placeholder="Jelaskan tujuan tagihan"></textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex justify-end">
                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-sm transition-all shadow-lg shadow-indigo-600/30 active:scale-95 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Terbitkan Tagihan
                </button>
            </div>
        </form>
    </div>
</div>
<x-loading />
<script>
    document.querySelectorAll('.nominal-input').forEach((input) => input.addEventListener('input', () => {
        input.value = input.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }));
</script>
@endsection