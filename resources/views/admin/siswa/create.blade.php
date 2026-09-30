@extends('layouts.admin')

@section('content')
<div class="container mx-auto p-6 max-w-2xl">
    <div class="bg-white rounded-xl shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Tambah Data Siswa / Santri Baru</h2>

        <form action="{{ route('admin.siswa.store') }}" method="POST">
            @csrf

            <!-- NIS -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">NIS (Nomor Induk Siswa)</label>
                <input type="text" name="nis" value="{{ old('nis') }}" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" required>
                @error('nis') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Nama Lengkap -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" required>
                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email Login Siswa</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" required>
                @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password Login</label>
                <input type="password" name="password" placeholder="Masukkan password siswa" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" required>
                @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Pilihan Kelas 7 - 12 -->
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Kelas</label>
                <select name="kelas" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none" required>
                    <option value="">-- Pilih Kelas --</option>
                    <option value="Kelas 7" {{ old('kelas') == 'Kelas 7' ? 'selected' : '' }}>Kelas 7 (SMP/MTs)</option>
                    <option value="Kelas 8" {{ old('kelas') == 'Kelas 8' ? 'selected' : '' }}>Kelas 8 (SMP/MTs)</option>
                    <option value="Kelas 9" {{ old('kelas') == 'Kelas 9' ? 'selected' : '' }}>Kelas 9 (SMP/MTs)</option>
                    <option value="Kelas 10" {{ old('kelas') == 'Kelas 10' ? 'selected' : '' }}>Kelas 10 (SMA/MA/SMK)</option>
                    <option value="Kelas 11" {{ old('kelas') == 'Kelas 11' ? 'selected' : '' }}>Kelas 11 (SMA/MA/SMK)</option>
                    <option value="Kelas 12" {{ old('kelas') == 'Kelas 12' ? 'selected' : '' }}>Kelas 12 (SMA/MA/SMK)</option>
                </select>
                @error('kelas') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.siswa.index') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg font-semibold">Batal</a>
                <button type="submit" class="px-5 py-2 bg-blue-600 text-white rounded-lg font-bold shadow hover:bg-blue-700">Simpan Siswa</button>
            </div>
        </form>
    </div>
</div>
<x-loading />
@endsection