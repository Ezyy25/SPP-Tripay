@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6" x-data="{ activeTab: 'belum', editModal: false, activeTagihan: {} }">
    <!-- Header & Filter -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Rekap Tagihan Bulanan</h1>
            <p class="text-xs text-slate-400 mt-1">
                Menampilkan riwayat dan total keuangan tagihan periode 
                <span class="font-bold text-indigo-400">
                    {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}
                </span>
            </p>
        </div>

        <!-- Filter Bulan & Tahun -->
        <form method="GET" action="{{ route('admin.tagihan.rekap') }}" class="flex items-center gap-2 bg-slate-900/80 p-2 rounded-2xl border border-slate-800">
            <select name="bulan" class="bg-slate-800 border border-slate-700 text-white text-xs rounded-xl px-3 py-2 outline-none focus:border-indigo-500">
                @for ($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ (int)$m === (int)$bulan ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                    </option>
                @endfor
            </select>

            <select name="tahun" class="bg-slate-800 border border-slate-700 text-white text-xs rounded-xl px-3 py-2 outline-none focus:border-indigo-500">
                @for ($y = date('Y'); $y >= date('Y') - 3; $y--)
                    <option value="{{ $y }}" {{ (int)$y === (int)$tahun ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>

            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl transition shadow-lg shadow-indigo-600/20">
                Tampilkan Rekap
            </button>
        </form>
    </div>

    <!-- Ringkasan Nominal Keuangan & Jumlah Santri -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card Uang Masuk (Lunas) -->
        <div class="p-5 rounded-2xl border border-emerald-500/20 bg-slate-900/50 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Total Uang Masuk</p>
                <h3 class="text-2xl font-bold text-white mt-1">Rp {{ number_format($totalUangMasuk, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $sudahBayar->count() }} Santri Sudah Lunas</p>
            </div>
            <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-xl flex items-center justify-center font-bold text-lg">
                ✓
            </div>
        </div>

        <!-- Card Sisa Tunggakan (Belum Bayar) -->
        <div class="p-5 rounded-2xl border border-rose-500/20 bg-slate-900/50 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-rose-400 uppercase tracking-wider">Total Sisa Tunggakan</p>
                <h3 class="text-2xl font-bold text-white mt-1">Rp {{ number_format($totalTunggakan, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">{{ $belumBayar->count() }} Santri Belum Bayar</p>
            </div>
            <div class="w-12 h-12 bg-rose-500/10 text-rose-400 border border-rose-500/20 rounded-xl flex items-center justify-center font-bold text-lg">
                !
            </div>
        </div>

        <!-- Card Total Target Tagihan -->
        <div class="p-5 rounded-2xl border border-slate-800 bg-slate-900/50 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Target Tagihan</p>
                <h3 class="text-2xl font-bold text-white mt-1">Rp {{ number_format($totalTarget, 0, ',', '.') }}</h3>
                <p class="text-xs text-slate-400 mt-1">Total {{ $sudahBayar->count() + $belumBayar->count() }} Santri Memiliki Tagihan</p>
            </div>
            <div class="w-12 h-12 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-xl flex items-center justify-center font-bold text-lg">
                Σ
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 pb-2">
        <button @click="activeTab = 'belum'" 
                :class="activeTab === 'belum' ? 'bg-rose-500/10 text-rose-400 border-rose-500/30' : 'text-slate-400 hover:text-white'"
                class="px-4 py-2 text-xs font-semibold rounded-xl border border-transparent transition">
            Belum Bayar ({{ $belumBayar->count() }})
        </button>
        <button @click="activeTab = 'sudah'" 
                :class="activeTab === 'sudah' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30' : 'text-slate-400 hover:text-white'"
                class="px-4 py-2 text-xs font-semibold rounded-xl border border-transparent transition">
            Sudah Bayar ({{ $sudahBayar->count() }})
        </button>
    </div>

    <!-- TAB 1: BELUM BAYAR -->
    <div x-show="activeTab === 'belum'" class="rounded-2xl border border-slate-800 overflow-hidden bg-slate-900/50">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-300">
                <thead class="bg-slate-800/60 text-xs text-slate-400 uppercase">
                    <tr>
                        <th class="px-6 py-4">Nama Santri</th>
                        <th class="px-6 py-4">NISN / Kelas</th>
                        <th class="px-6 py-4">Judul Tagihan</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">Nominal Tagihan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($belumBayar as $item)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-semibold text-white">{{ $item->siswa->nama ?? $item->siswa->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-400">{{ $item->siswa->nisn ?? '-' }} / {{ $item->siswa->kelas ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $item->title ?? $item->judul ?? 'SPP Bulanan' }}</td>
                            <td class="px-6 py-4 max-w-xs">{{ $item->description ?: '-' }}</td>
                            <td class="px-6 py-4 text-rose-400 font-bold">Rp {{ number_format($item->nominal ?? $item->jumlah ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                    BELUM BAYAR
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button type="button" @click="editModal = true; activeTagihan = { id: {{ $item->id }}, nominal: '{{ $item->nominal }}', status: '{{ $item->status }}', description: '{{ addslashes($item->description ?? '') }}', due_date: '{{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('Y-m-d') : '' }}' }" class="text-indigo-400 hover:text-indigo-300 text-xs font-semibold">Edit</button>
                                <form action="{{ route('admin.tagihan.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tagihan ini dari Rekap?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="rekap"><input type="hidden" name="bulan" value="{{ $bulan }}"><input type="hidden" name="tahun" value="{{ $tahun }}">
                                    <button type="submit" class="ml-2 text-rose-400 hover:text-rose-300 text-xs font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-500">
                                Tidak ada tunggakan tagihan untuk bulan {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TAB 2: SUDAH BAYAR -->
    <div x-show="activeTab === 'sudah'" class="rounded-2xl border border-slate-800 overflow-hidden bg-slate-900/50" x-cloak>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-300">
                <thead class="bg-slate-800/60 text-xs text-slate-400 uppercase">
                    <tr>
                        <th class="px-6 py-4">Nama Santri</th>
                        <th class="px-6 py-4">NISN / Kelas</th>
                        <th class="px-6 py-4">Judul Tagihan</th>
                        <th class="px-6 py-4">Deskripsi</th>
                        <th class="px-6 py-4">Nominal</th>
                        <th class="px-6 py-4">Tanggal Pelunasan</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($sudahBayar as $item)
                        @php($payment = $item->pembayarans->sortByDesc('paid_at')->first())
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-semibold text-white">{{ $item->siswa->nama ?? $item->siswa->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs text-slate-400">{{ $item->siswa->nisn ?? '-' }} / {{ $item->siswa->kelas ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $item->title ?? $item->judul ?? 'SPP Bulanan' }}</td>
                            <td class="px-6 py-4 max-w-xs">{{ $item->description ?: '-' }}</td>
                            <td class="px-6 py-4 text-emerald-400 font-bold">Rp {{ number_format($item->nominal ?? $item->jumlah ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-xs text-slate-400">
                                {{ $payment?->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('d M Y H:i') : ($item->updated_at ? \Carbon\Carbon::parse($item->updated_at)->format('d M Y H:i') : '-') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                    LUNAS
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button type="button" @click="editModal = true; activeTagihan = { id: {{ $item->id }}, nominal: '{{ $item->nominal }}', status: '{{ $item->status }}', description: '{{ addslashes($item->description ?? '') }}', due_date: '{{ $item->due_date ? \Carbon\Carbon::parse($item->due_date)->format('Y-m-d') : '' }}' }" class="text-indigo-400 hover:text-indigo-300 text-xs font-semibold">Edit</button>
                                <form action="{{ route('admin.tagihan.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tagihan ini dari Rekap?')">
                                    @csrf @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="rekap"><input type="hidden" name="bulan" value="{{ $bulan }}"><input type="hidden" name="tahun" value="{{ $tahun }}">
                                    <button type="submit" class="ml-2 text-rose-400 hover:text-rose-300 text-xs font-semibold">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-10 text-slate-500">
                                Belum ada tagihan yang dilunasi untuk bulan {{ DateTime::createFromFormat('!m', $bulan)->format('F') }} {{ $tahun }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
    <form :action="'{{ route('admin.tagihan.update', ':id') }}'.replace(':id', activeTagihan.id)" method="POST" class="w-full max-w-lg space-y-4 rounded-xl bg-white p-6 text-gray-900">
        @csrf @method('PUT')
        <input type="hidden" name="redirect_to" value="rekap"><input type="hidden" name="bulan" value="{{ $bulan }}"><input type="hidden" name="tahun" value="{{ $tahun }}">
        <h3 class="text-lg font-bold">Edit Tagihan</h3>
        <input type="text" name="nominal" inputmode="numeric" x-model="activeTagihan.nominal" class="w-full rounded-lg border p-2" required>
        <textarea name="description" x-model="activeTagihan.description" rows="3" class="w-full rounded-lg border p-2" required></textarea>
        <select name="status" x-model="activeTagihan.status" class="w-full rounded-lg border p-2" required><option value="unpaid">Belum Bayar</option><option value="pending">Pending</option><option value="paid">Lunas</option></select>
        <input type="date" name="due_date" x-model="activeTagihan.due_date" class="w-full rounded-lg border p-2">
        <div class="flex justify-end gap-2"><button type="button" @click="editModal = false" class="rounded-lg bg-gray-200 px-4 py-2">Batal</button><button class="rounded-lg bg-indigo-600 px-4 py-2 text-white">Simpan</button></div>
    </form>
</div>
@endsection