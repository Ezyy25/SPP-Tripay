@extends('layouts.app') {{-- Sesuaikan dengan layout admin Anda --}}

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-xl">
        <h2 class="text-xl font-bold text-white mb-1">Edit Data Siswa</h2>
        <p class="text-xs text-slate-400 mb-6">Perbarui informasi akun login dan email notifikasi siswa.</p>

        <form action="{{ route('admin.siswa.update', $siswa->id) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Nama Siswa -->
            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-400 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $siswa->name) }}" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
            </div>

            <!-- Grid 2 Kolom untuk Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- Email Login -->
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Email Login (Akun)</label>
                    <input type="email" name="email" value="{{ old('email', $siswa->email) }}" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                    <p class="text-[10px] text-slate-500 mt-1">*Digunakan siswa untuk login ke sistem.</p>
                </div>

                <!-- Email Asli (Penerima Notifikasi) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Email Asli (Notifikasi)</label>
                    <input type="email" name="email_asli" value="{{ old('email_asli', $siswa->siswa->email_asli ?? '') }}" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" placeholder="siswa@gmail.com" required>
                    <p class="text-[10px] text-indigo-400 mt-1">*Notifikasi tagihan akan dikirim ke email ini.</p>
                </div>
            </div>

            <!-- Grid 2 Kolom untuk NIS & Kelas -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- NIS -->
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">NIS</label>
                    <input type="text" name="nis" value="{{ old('nis', $siswa->siswa->nis ?? '') }}" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>

                <!-- Kelas -->
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Kelas</label>
                    <input type="text" name="kelas" value="{{ old('kelas', $siswa->siswa->kelas ?? '') }}" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" required>
                </div>
            </div>

            <!-- Password Baru (Opsional) -->
            <div class="mb-6">
                <label class="block text-xs font-semibold text-slate-400 mb-1">Password Baru (Opsional)</label>
                <input type="password" name="password" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-2 text-white text-sm focus:outline-none focus:border-indigo-500" placeholder="Kosongkan jika tidak ingin mengubah password">
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('admin.siswa.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-xs font-semibold transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
<x-loading />
@endsection