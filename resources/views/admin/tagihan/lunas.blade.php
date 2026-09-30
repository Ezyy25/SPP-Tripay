<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran Lunas</title>
    <!-- Script Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen p-6 md:p-10">

    <div class="max-w-7xl mx-auto space-y-6">
        
        <!-- Header Page -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Riwayat Pembayaran Lunas</h1>
                <p class="text-sm text-slate-500 mt-1">Daftar seluruh transaksi pembayaran SPP siswa yang telah diselesaikan.</p>
            </div>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition">
                    &larr; Kembali ke Dashboard
                </a>
            </div>
        </div>

        <!-- Metric Stat & Table Card -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <!-- Summary Banner -->
            <div class="p-6 bg-gradient-to-r from-emerald-50 to-teal-50/50 border-b border-emerald-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Total Kas Masuk</p>
                    <p class="text-sm font-medium text-slate-700 mt-0.5">Akumulasi Seluruh Tagihan Lunas</p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-xs text-slate-500">Total Diterima</p>
                    <p class="text-2xl font-extrabold text-emerald-600">Rp {{ number_format($totalDiterima, 0, ',', '.') }}</p>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-100 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                            <th class="py-4 px-6">No</th>
                            <th class="py-4 px-6">NIS</th>
                            <th class="py-4 px-6">Nama Siswa</th>
                            <th class="py-4 px-6">Periode Tagihan</th>
                            <th class="py-4 px-6">Nominal</th>
                            <th class="py-4 px-6">Tanggal Lunas</th>
                            <th class="py-4 px-6 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                        @forelse($tagihanLunas as $index => $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-6 font-medium text-slate-400">{{ $index + 1 }}</td>
                                <td class="py-4 px-6 font-semibold text-slate-800">{{ $item->siswa->nis ?? '-' }}</td>
                                <td class="py-4 px-6 font-medium text-slate-700">{{ $item->siswa->nama ?? '-' }}</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $item->bulan }} {{ $item->tahun }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 font-bold text-emerald-600">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-xs text-slate-500">{{ $item->updated_at->format('d M Y, H:i') }} WIB</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Lunas
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 px-6 text-center text-slate-400">
                                    Belum ada transaksi pembayaran lunas yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>
<x-loading />