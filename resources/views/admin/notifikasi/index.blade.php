@extends('layouts.admin')

@section('title', 'Riwayat Notifikasi')
@section('page_title', 'Riwayat Notifikasi Terkirim')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Riwayat Notifikasi</h1>
            <p class="mt-1 text-sm text-slate-400">Pesan tagihan baru dan pengingat yang telah dikirim kepada siswa.</p>
        </div>
        <span class="text-sm text-slate-400">{{ $notifikasi->total() }} notifikasi</span>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-900/70">
        <table class="w-full min-w-[760px] text-left text-sm">
            <thead class="border-b border-slate-800 bg-slate-800/50 text-xs uppercase text-slate-400">
                <tr>
                    <th class="p-4">Waktu</th>
                    <th class="p-4">Penerima</th>
                    <th class="p-4">Jenis</th>
                    <th class="p-4">Isi Notifikasi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800 text-slate-300">
                @forelse($notifikasi as $item)
                    <tr class="align-top hover:bg-slate-800/30">
                        <td class="whitespace-nowrap p-4 text-slate-400">{{ $item->created_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="p-4">
                            <div class="font-semibold text-white">{{ $item->data['recipient_name'] ?? 'Siswa' }}</div>
                            <div class="mt-1 text-xs text-slate-400">{{ $item->data['recipient_email'] ?? '-' }}</div>
                        </td>
                        <td class="p-4">
                            <span class="inline-flex rounded-md border border-cyan-500/20 bg-cyan-500/10 px-2 py-1 text-xs font-semibold text-cyan-300">
                                {{ $item->data['title'] ?? 'Notifikasi tagihan' }}
                            </span>
                        </td>
                        <td class="max-w-xl whitespace-normal p-4 leading-relaxed">
                            <p>{{ $item->data['message'] ?? '-' }}</p>
                            @if(isset($item->data['tagihan_id']))
                                <p class="mt-2 text-xs text-slate-500">ID Tagihan: {{ $item->data['tagihan_id'] }}</p>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="p-10 text-center text-slate-400">Belum ada notifikasi yang tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>{{ $notifikasi->links() }}</div>
</div>
@endsection