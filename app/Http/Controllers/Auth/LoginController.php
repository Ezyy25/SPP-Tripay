<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        // Cek apakah input berupa Email atau NIS
        $fieldType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nis';

        $credentials = [
            $fieldType => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();
            return $this->authenticated($request, Auth::user());
        }

        return back()->withErrors([
            'login' => 'Email/NIS atau Password salah.',
        ])->onlyInput('login');
    }

    protected function authenticated(Request $request, $user)
    {
        $role = strtolower(trim($user->role ?? ''));

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($role === 'siswa') {
            return redirect()->route('siswa.dashboard');
        }

        // Jika role tidak dikenali, logout dan kembalikan ke login
        Auth::logout();
        return redirect()->route('login')->withErrors([
            'login' => 'Role pengguna tidak valid.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}