@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6" x-data="{ openModal: false, editModal: false, activeTagihan: {} }">
    
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Data Tagihan SPP</h1>
            <p class="text-xs text-slate-400 mt-1">Kelola dan generate tagihan bulanan siswa/santri</p>
        </div>
        <button @click="openModal = true" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow flex items-center gap-2 transition">
            + Generate / Buat Tagihan
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="font-bold text-green-800">&times;</button>
        </div>
    @endif

    <!-- Alert Error -->
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6 flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="font-bold text-red-800">&times;</button>
        </div>
    @endif

    <!-- Tabel Data Tagihan -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Santri/Siswa</th>
                    <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Periode</th>
                    <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Deskripsi</th>
                    <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Nominal (Rp)</th>
                    <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Batas Waktu</th>
                    <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="py-3 px-4 text-xs font-semibold text-gray-600 uppercase text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($tagihans as $tagihan)
                @php
                    $nominalTagihan = $tagihan->nominal ?? $tagihan->sppRate->nominal ?? 0;
                    $isLunas = in_array(strtolower($tagihan->status), ['paid', 'lunas']);
                @endphp
                <tr class="hover:bg-gray-50">
                    <td class="py-3 px-4 font-medium text-gray-900">
                        {{ $tagihan->siswa->name ?? $tagihan->siswa->nama ?? 'N/A' }}
                    </td>
                    <td class="py-3 px-4 text-gray-600">
                        Bulan {{ $tagihan->bulan }} / {{ $tagihan->tahun }}
                    </td>
                    <td class="py-3 px-4 text-gray-600 max-w-xs">{{ $tagihan->description ?: '-' }}</td>
                    <td class="py-3 px-4 text-gray-900 font-semibold">
                        Rp {{ number_format($nominalTagihan, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-4 text-gray-600">
                        {{ $tagihan->due_date ? \Carbon\Carbon::parse($tagihan->due_date)->format('d M Y') : '-' }}
                    </td>
                    <td class="py-3 px-4">
                        <span @class([
                            'text-xs font-bold px-2.5 py-1 rounded-full inline-block',
                            'bg-green-100 text-green-800 border border-green-200' => $isLunas,
                            'bg-red-100 text-red-800 border border-red-200' => !$isLunas,
                        ])>
                            {{ $isLunas ? 'Lunas' : 'Belum Bayar' }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            
                            <!-- Toggle Status Button -->
                            <form action="{{ route('admin.tagihan.toggle-status', $tagihan->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                @if($isLunas)
                                    <button type="submit" 
                                            onclick="return confirm('Tandai tagihan ini sebagai BELUM LUNAS?')"
                                            class="px-3 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-300 font-semibold text-xs rounded-lg transition"
                                            title="Ubah status ke Belum Bayar">
                                        Batalkan Lunas
                                    </button>
                                @else
                                    <button type="submit" 
                                            onclick="return confirm('Tandai tagihan ini sebagai LUNAS?')"
                                            class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg shadow-sm transition"
                                            title="Konfirmasi pembayaran santri">
                                        Tandai Lunas
                                    </button>
                                @endif
                            </form>

                            <!-- Edit Button -->
                            <button @click="editModal = true; activeTagihan = {
                                id: {{ $tagihan->id }},
                                name: '{{ addslashes($tagihan->siswa->name ?? $tagihan->siswa->nama ?? 'N/A') }}',
                                nominal: {{ $nominalTagihan }},
                                status: '{{ $tagihan->status }}',
                                description: '{{ addslashes($tagihan->description ?? '') }}',
                                due_date: '{{ $tagihan->due_date ? \Carbon\Carbon::parse($tagihan->due_date)->format('Y-m-d') : '' }}'
                            }" class="px-2 py-1 text-blue-600 hover:text-blue-800 font-semibold text-xs hover:underline transition">
                                Edit
                            </button>

                            <!-- Delete Button -->
                            <form action="{{ route('admin.tagihan.destroy', $tagihan->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tagihan ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 text-red-600 hover:text-red-800 font-semibold text-xs hover:underline transition">
                                    Hapus
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">Belum ada data tagihan SPP hari ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t">
            {{ $tagihans->links() }}
        </div>
    </div>

    <!-- MODAL 1: Generate / Buat Tagihan Baru -->
    <div x-show="openModal" class="fixed inset-0 bg-black/60 flex items-center justify-center p-4 z-50" x-cloak>
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Buat Tagihan Baru</h3>

            <form action="{{ route('admin.tagihan.store') }}" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                @csrf

                <!-- Pilih Santri / Siswa -->
                <div class="mb-4">
                    <label for="siswa_id" class="block text-sm font-semibold text-gray-800 mb-1">Pilih Santri / Siswa</label>
                    <select name="siswa_id" id="siswa_id" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                        <option value="">-- Pilih Santri --</option>
                        <option value="all">⚡ Semua Santri (Bulk Generate)</option>
                        @foreach($siswas as $siswa)
                                <option value="{{ $siswa->id }}">
                                    {{ $siswa->user->name ?? $siswa->name ?? $siswa->nama ?? 'Santri #'.$siswa->id }}
                                </option>
                         @endforeach
                    </select>
                </div>

                <!-- Input Nominal SPP -->
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 mb-1">Nominal SPP (Rp)</label>
                    <input type="text" name="nominal" inputmode="numeric" placeholder="Contoh: 250.000" class="nominal-input w-full bg-white text-gray-900 placeholder-gray-400 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 mb-1">Deskripsi Tagihan</label>
                    <textarea name="description" rows="3" class="w-full bg-white text-gray-900 placeholder-gray-400 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" placeholder="Jelaskan tujuan tagihan" required></textarea>
                </div>

                <!-- Bulan & Tahun -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">Bulan</label>
                        <select name="bulan" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }} class="text-gray-900">
                                    Bulan {{ $m }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-800 mb-1">Tahun</label>
                        <input type="number" name="tahun" value="{{ date('Y') }}" class="w-full bg-white text-gray-900 placeholder-gray-400 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                    </div>
                </div>

                <!-- Due Date -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-800 mb-1">Batas Akhir Pembayaran</label>
                    <input type="date" name="due_date" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="openModal = false" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold rounded-lg shadow transition">Proses Buat</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: Edit Tagihan -->
    <div x-show="editModal" class="fixed inset-0 bg-black/60 flex items-center justify-center p-4 z-50" x-cloak>
        <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl border border-gray-100">
            <h3 class="text-xl font-bold text-gray-900 mb-1">Edit Tagihan</h3>
            <p class="text-sm font-medium text-gray-600 mb-4" x-text="'Santri: ' + activeTagihan.name"></p>

            <form :action="'{{ route('admin.tagihan.update', ':id') }}'.replace(':id', activeTagihan.id)" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 mb-1">Nominal SPP (Rp)</label>
                    <input type="text" name="nominal" inputmode="numeric" x-model="activeTagihan.nominal" class="nominal-input w-full bg-white text-gray-900 placeholder-gray-400 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 mb-1">Deskripsi Tagihan</label>
                    <textarea name="description" rows="3" x-model="activeTagihan.description" class="w-full bg-white text-gray-900 placeholder-gray-400 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-800 mb-1">Status Pembayaran</label>
                    <select name="status" x-model="activeTagihan.status" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition" required>
                        <option value="unpaid" class="text-gray-900">Belum Bayar (Unpaid)</option>
                        <option value="paid" class="text-gray-900">Lunas (Paid)</option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-800 mb-1">Batas Akhir Pembayaran</label>
                    <input type="date" name="due_date" x-model="activeTagihan.due_date" class="w-full bg-white text-gray-900 border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-lg transition">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-bold rounded-lg shadow transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
<script>
    document.querySelectorAll('.nominal-input').forEach((input) => input.addEventListener('input', () => {
        input.value = input.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }));
</script>
@endsection