@extends('layouts.admin')

@section('content')
<div class="p-6 space-y-6" x-data="{ editModal: false, editData: {} }">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white">Pengaturan Jadwal Tagihan Otomatis</h1>
            <p class="text-xs text-slate-400 mt-1">Kelola, edit, atau hapus jadwal rutin terbitnya tagihan SPP bulanan.</p>
        </div>

        <!-- Tombol Eksekusi Instan -->
        @if(Route::has('admin.tagihan.schedule.run-now'))
            <form action="{{ route('admin.tagihan.schedule.run-now') }}" method="POST">
                @csrf
                <button type="submit" 
                        onclick="return confirm('Apakah Anda yakin ingin langsung menerbitkan tagihan bulan ini sekarang ke seluruh siswa?')" 
                        class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-xs transition flex items-center gap-2 shadow-lg shadow-emerald-600/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    Jalankan Tagihan Sekarang
                </button>
            </form>
        @endif
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Form Create / Tambah Jadwal Baru -->
    <div class="glass-card p-6 rounded-2xl border border-slate-800 bg-slate-900/50">
        <h3 class="text-lg font-semibold text-white mb-4">+ Buat Jadwal Baru</h3>
        <form action="{{ route('admin.tagihan.schedule.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Judul Tagihan</label>
                <input type="text" name="title" value="SPP Bulanan" placeholder="Misal: SPP Bulanan" required class="w-full bg-slate-800 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-white text-sm outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Tgl Terbit (Tiap Bulan)</label>
                <input type="number" name="generate_day" min="1" max="31" placeholder="Contoh: 1 atau 24" required class="w-full bg-slate-800 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-white text-sm outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Tgl Jatuh Tempo</label>
                <input type="number" name="due_day" min="1" max="31" placeholder="Contoh: 10" required class="w-full bg-slate-800 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-white text-sm outline-none transition">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Nominal (Rp)</label>
                <input type="number" name="nominal" placeholder="2100000" required class="w-full bg-slate-800 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-white text-sm outline-none transition">
            </div>

            <div class="md:col-span-2 lg:col-span-4 flex items-center justify-between mt-2 pt-2 border-t border-slate-800/80">
                <label class="inline-flex items-center gap-2 text-sm text-slate-300 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-indigo-600 focus:ring-0">
                    Aktifkan Jadwal Ini
                </label>

                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-sm transition shadow-lg shadow-indigo-600/30">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Daftar Jadwal (Read, Toggle, Edit & Delete) -->
    <div class="glass-card rounded-2xl border border-slate-800 overflow-hidden bg-slate-900/50">
        <div class="px-6 py-4 border-b border-slate-800">
            <h3 class="text-sm font-semibold text-slate-300">Daftar Jadwal Tagihan Rutin</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-300">
                <thead class="bg-slate-800/60 text-xs text-slate-400 uppercase">
                    <tr>
                        <th class="px-6 py-4">Judul</th>
                        <th class="px-6 py-4">Tgl Terbit</th>
                        <th class="px-6 py-4">Tgl Jatuh Tempo</th>
                        <th class="px-6 py-4">Nominal</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($schedules as $item)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="px-6 py-4 font-semibold text-white">{{ $item->title }}</td>
                            <td class="px-6 py-4">Setiap Tanggal {{ $item->generate_day }}</td>
                            <td class="px-6 py-4">Tanggal {{ $item->due_day }}</td>
                            <td class="px-6 py-4 text-indigo-400 font-bold">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if($item->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">Non-Aktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    <!-- Toggle Status Quick Form -->
                                    <form action="{{ route('admin.tagihan.schedule.update', $item->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="toggle_status_only" value="1">
                                        <label class="inline-flex items-center gap-1.5 cursor-pointer">
                                            <input type="checkbox" name="is_active" value="1" onchange="this.form.submit()" {{ $item->is_active ? 'checked' : '' }} class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-indigo-600 focus:ring-0">
                                            <span class="text-xs text-slate-400">Aktif</span>
                                        </label>
                                    </form>

                                    <!-- Tombol Edit Modal -->
                                    <button type="button" 
                                            @click="editModal = true; editData = {{ json_encode($item) }}" 
                                            class="px-2.5 py-1 bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 border border-amber-500/20 rounded-lg text-xs font-medium transition">
                                        Edit
                                    </button>

                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('admin.tagihan.schedule.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/20 rounded-lg text-xs font-medium transition">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-8 text-slate-500">Belum ada jadwal tetap yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL EDIT JADWAL -->
    <div x-show="editModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4">
        <div @click.away="editModal = false" class="bg-slate-900 border border-slate-800 rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <h3 class="text-lg font-bold text-white">Edit Jadwal Tagihan</h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-white">&times;</button>
            </div>

            <form :action="'{{ url('admin/tagihan/schedule') }}/' + editData.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Judul Tagihan</label>
                    <input type="text" name="title" x-model="editData.title" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Tgl Terbit (Tiap Bulan)</label>
                        <input type="number" name="generate_day" min="1" max="31" x-model="editData.generate_day" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1">Tgl Jatuh Tempo</label>
                        <input type="number" name="due_day" min="1" max="31" x-model="editData.due_day" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Nominal (Rp)</label>
                    <input type="number" name="nominal" x-model="editData.nominal" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm">
                </div>

                <div>
                    <label class="inline-flex items-center gap-2 text-sm text-slate-300">
                        <input type="checkbox" name="is_active" value="1" :checked="editData.is_active" class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-indigo-600">
                        Aktifkan Jadwal Ini
                    </label>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-slate-800">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-medium transition">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-medium transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection