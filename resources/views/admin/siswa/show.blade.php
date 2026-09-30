<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Siswa - {{ $siswa->nama ?? $siswa->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Profil Siswa Card -->
        <div class="bg-white p-6 rounded-xl shadow-md">
            <div class="flex justify-between items-center border-b pb-4 mb-4">
                <h1 class="text-2xl font-bold text-gray-800">Profil Siswa</h1>
                <a href="{{ route('admin.siswa.index') }}" class="text-sm text-indigo-600 hover:underline">← Kembali</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <span class="text-xs text-gray-500 block">Nama Lengkap</span>
                    <strong class="text-base text-gray-900">{{ $siswa->nama ?? $siswa->user->name }}</strong>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">NIS</span>
                    <strong class="text-base text-gray-900">{{ $siswa->nis }}</strong>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Kelas</span>
                    <strong class="text-base text-gray-900">{{ $siswa->kelas }}</strong>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block">Email Login</span>
                    <strong class="text-base text-gray-900">{{ $siswa->user->email ?? '-' }}</strong>
                </div>
            </div>
        </div>

        <!-- Form Tambah Tagihan Khusus Siswa Ini -->
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">+ Buat Tagihan / Tunggakan Baru</h2>
            <form action="{{ route('admin.tagihan.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Bulan</label>
                    <select name="bulan" required class="w-full border rounded-lg p-2 text-sm">
                        @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $bulan)
                            <option value="{{ $bulan }}">{{ $bulan }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Tahun</label>
                    <input type="number" name="tahun" value="{{ date('Y') }}" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Nominal (Rp)</label>
                    <input type="number" name="nominal" required class="w-full border rounded-lg p-2 text-sm" placeholder="Contoh: 150000">
                </div>
                <div>
                    <label class="block text-xs text-gray-600 mb-1">Deskripsi Tagihan</label>
                    <input type="text" name="description" required class="w-full border rounded-lg p-2 text-sm" placeholder="Contoh: SPP bulan berjalan">
                </div>
                <div>
                    <input type="hidden" name="status" value="unpaid">
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-lg text-sm font-medium">Buat Tagihan</button>
                </div>
            </form>
        </div>

        <!-- Daftar Tagihan & Tunggakan -->
        <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Riwayat Tagihan & Tunggakan</h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-3 text-sm font-semibold text-gray-700">Periode</th>
                            <th class="p-3 text-sm font-semibold text-gray-700">Nominal</th>
                            <th class="p-3 text-sm font-semibold text-gray-700">Status</th>
                            <th class="p-3 text-sm font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa->tagihans as $tagihan)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-3 text-sm font-medium text-gray-900">{{ $tagihan->bulan }} {{ $tagihan->tahun }}</td>
                                <td class="p-3 text-sm text-gray-800">Rp {{ number_format($tagihan->nominal, 0, ',', '.') }}</td>
                                <td class="p-3 text-sm">
                                    @if($tagihan->status === 'paid')
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded font-semibold">LUNAS</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded font-semibold">BELUM DIBAYAR</span>
                                    @endif
                                </td>
                                <td class="p-3 text-sm space-x-2">
                                    @if($tagihan->status !== 'paid')
                                        <form action="{{ route('admin.tagihan.update', $tagihan->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="paid">
                                            <button class="bg-emerald-500 text-white px-2 py-1 rounded text-xs hover:bg-emerald-600">Tandai Lunas</button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.tagihan.destroy', $tagihan->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tagihan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="bg-red-500 text-white px-2 py-1 rounded text-xs hover:bg-red-600">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center text-gray-500 text-sm">Tidak ada riwayat tagihan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>