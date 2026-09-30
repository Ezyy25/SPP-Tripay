<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * Menampilkan daftar seluruh siswa
     */
    public function index()
    {
        $siswas = User::where('role', 'siswa')
            ->orWhereHas('siswa')
            ->with('siswa')
            ->latest()
            ->paginate(10);

        return view('admin.siswa.index', compact('siswas'));
    }

    /**
     * Show the form for creating a new resource.
     * 
     * Menampilkan form tambah siswa
     */
    public function create()
    {
        return view('admin.siswa.create');
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Menyimpan data siswa baru dari form (termasuk email_asli)
     */
    public function store(Request $request)
    {
        // Validasi Input Form
        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'email_asli' => 'required|email', // Validasi email asli untuk notifikasi
            'password'   => 'required|min:6',
            'nis'        => 'required|string',
            'kelas'      => 'required|string',
        ]);

        // 1. Simpan ke tabel users (untuk akun login)
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'siswa';
        $user->save();

        // 2. Simpan/Hubungkan ke tabel siswas (termasuk email_asli)
        Siswa::create([
            'user_id'    => $user->id,
            'email_asli' => $request->email_asli,
            'nis'        => $request->nis,
            'kelas'      => $request->kelas,
        ]);

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     * 
     * Menampilkan form edit siswa
     */
    public function edit($id)
    {
        $siswa = User::with('siswa')->findOrFail($id);
        return view('admin.siswa.edit', compact('siswa'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Memperbarui data siswa (termasuk email_asli)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $id,
            'email_asli' => 'required|email', // Validasi email asli
            'nis'        => 'required|string',
            'kelas'      => 'required|string',
        ]);

        // Update data User (email login & nama)
        $user->name = $request->name;
        $user->email = $request->email;
        
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        // Update atau Buat data di tabel siswas (termasuk email_asli)
        if ($user->siswa) {
            $user->siswa->update([
                'email_asli' => $request->email_asli,
                'nis'        => $request->nis,
                'kelas'      => $request->kelas,
            ]);
        } else {
            Siswa::create([
                'user_id'    => $user->id,
                'email_asli' => $request->email_asli,
                'nis'        => $request->nis,
                'kelas'      => $request->kelas,
            ]);
        }

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Menghapus data siswa
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->siswa) {
            $user->siswa->delete();
        }
        
        $user->delete();

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil dihapus!');
    }
}