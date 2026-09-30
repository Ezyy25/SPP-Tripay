@extends('layouts.admin')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">Kelola Data Siswa</h1>
            <p class="text-gray-400 text-sm">Daftar seluruh siswa yang terdaftar dalam sistem</p>
        </div>
        <button type="button" onclick="openModalTambah()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow transition">
            + Tambah Siswa
        </button>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-500/20 border border-green-500 text-green-400 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Alert Error Validation -->
    @if($errors->any())
        <div class="mb-4 p-4 bg-red-500/20 border border-red-500 text-red-400 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Tabel Data Siswa -->
    <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700 overflow-hidden">
        <table class="w-full text-left text-gray-300">
            <thead class="bg-gray-900 text-gray-400 text-xs uppercase tracking-wider border-b border-gray-700">
                <tr>
                    <th class="py-3 px-4">NIS</th>
                    <th class="py-3 px-4">NAMA</th>
                    <th class="py-3 px-4">EMAIL LOGIN</th>
                    <th class="py-3 px-4">EMAIL NOTIFIKASI</th>
                    <th class="py-3 px-4">KELAS</th>
                    <th class="py-3 px-4 text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse ($siswas as $siswa)
                <tr class="hover:bg-gray-750 transition">
                    <td class="py-3 px-4 font-mono text-sm">{{ $siswa->siswa->nis ?? '-' }}</td>
                    <td class="py-3 px-4 font-semibold text-white">{{ $siswa->name }}</td>
                    <td class="py-3 px-4 text-gray-400 text-sm">{{ $siswa->email }}</td>
                    <td class="py-3 px-4 text-indigo-400 text-sm">{{ $siswa->siswa->email_asli ?? '-' }}</td>
                    <td class="py-3 px-4">{{ $siswa->siswa->kelas ?? '-' }}</td>
                    <td class="py-3 px-4 text-center space-x-2">
                        <!-- Tombol Edit menggunakan Data Attributes -->
                        <button type="button" 
                            class="btn-edit bg-yellow-500/20 text-yellow-400 hover:bg-yellow-500 hover:text-white px-3 py-1 rounded-md text-sm font-medium transition"
                            data-id="{{ $siswa->id }}"
                            data-nis="{{ $siswa->siswa->nis ?? '' }}"
                            data-name="{{ $siswa->name }}"
                            data-email="{{ $siswa->email }}"
                            data-emailasli="{{ $siswa->siswa->email_asli ?? '' }}"
                            data-kelas="{{ $siswa->siswa->kelas ?? '' }}"
                            onclick="openModalEditFromBtn(this)">
                            Edit
                        </button>

                        <!-- Tombol Hapus -->
                        <form action="{{ route('admin.siswa.destroy', $siswa->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus siswa ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500/20 text-red-400 hover:bg-red-500 hover:text-white px-3 py-1 rounded-md text-sm font-medium transition">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-6 text-gray-500">Belum ada data siswa.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $siswas->links() }}
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div id="modalTambah" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-gray-800 border border-gray-700 rounded-xl max-w-lg w-full p-6 shadow-2xl">
        <h2 class="text-xl font-bold text-white mb-4">Tambah Siswa Baru</h2>
        <form action="{{ route('admin.siswa.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">NIS</label>
                    <input type="text" name="nis" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Kelas</label>
                    <input type="text" name="kelas" placeholder="Contoh: 10" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-400 mb-1">Nama Lengkap</label>
                <input type="text" name="name" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Email Login</label>
                    <input type="email" name="email" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none" placeholder="siswa@sekolah.com" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Email Asli (Notifikasi)</label>
                    <input type="email" name="email_asli" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none" placeholder="siswa@gmail.com" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 mb-1">Password Login</label>
                <input type="password" name="password" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none" required>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModalTambah()" class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg text-sm">Batal</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">Simpan Siswa</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Siswa -->
<div id="modalEdit" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50">
    <div class="bg-gray-800 border border-gray-700 rounded-xl max-w-lg w-full p-6 shadow-2xl">
        <h2 class="text-xl font-bold text-white mb-4">Edit Data Siswa</h2>
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">NIS</label>
                    <input type="text" name="nis" id="edit_nis" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Kelas</label>
                    <input type="text" name="kelas" id="edit_kelas" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-400 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-400 mb-1">Email Login (Akun)</label>
                    <input type="email" name="email" id="edit_email" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-indigo-400 mb-1">Email Asli (Notifikasi)</label>
                    <input type="email" name="email_asli" id="edit_email_asli" class="w-full bg-gray-900 border border-indigo-500/50 text-white rounded-lg p-2.5 text-sm focus:border-indigo-500 outline-none" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-400 mb-1">Password Baru <span class="text-gray-500">(Kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" class="w-full bg-gray-900 border border-gray-700 text-white rounded-lg p-2.5 text-sm focus:border-blue-500 outline-none">
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="closeModalEdit()" class="bg-gray-700 text-gray-300 px-4 py-2 rounded-lg text-sm">Batal</button>
                <button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-yellow-700">Update Siswa</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambah() {
        const modal = document.getElementById('modalTambah');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalTambah() {
        const modal = document.getElementById('modalTambah');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openModalEditFromBtn(button) {
        const id = button.getAttribute('data-id');
        const nis = button.getAttribute('data-nis') || '';
        const name = button.getAttribute('data-name') || '';
        const email = button.getAttribute('data-email') || '';
        const emailAsli = button.getAttribute('data-emailasli') || '';
        const kelas = button.getAttribute('data-kelas') || '';

        // Mengatur action form edit secara dinamis
        document.getElementById('formEdit').action = '/admin/siswa/' + id;
        
        // Mengisi nilai input modal edit dari attribute tombol yang diklik
        document.getElementById('edit_nis').value = nis;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_email_asli').value = emailAsli;
        document.getElementById('edit_kelas').value = kelas;

        const modal = document.getElementById('modalEdit');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeModalEdit() {
        const modal = document.getElementById('modalEdit');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection