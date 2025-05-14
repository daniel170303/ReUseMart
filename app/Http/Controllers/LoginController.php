<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login.login');
    }

    // Proses login
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email|exists:users,email', // memastikan email valid dan terdaftar di database
            'password' => 'required|string|min:6', // validasi password harus ada dan memiliki panjang minimal 6 karakter
            'role' => 'required|in:admin,pegawai,owner,gudang,cs,penitip,pembeli,organisasi', // validasi role hanya diizinkan yang valid
        ]);

        // Cari user berdasarkan email dan role
        $user = User::where('email', $credentials['email'])
                    ->where('role', $credentials['role'])
                    ->first();

        // Cek password dan login user
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);

            // Redirect sesuai role
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'pegawai' => redirect()->route('pegawai.dashboard'),
                'owner' => redirect()->route('owner.dashboard'),
                'gudang' => redirect()->route('gudang.dashboard'),
                'cs' => redirect()->route('cs.dashboard'),
                'penitip' => redirect()->route('penitip.dashboard'),
                'organisasi' => redirect()->route('organisasi.dashboard'),
                default => redirect('/'),
            };
        }

        // Jika login gagal, kembalikan error
        return back()->withErrors([
            'email' => 'Email, password, atau role salah.',
        ])->onlyInput('email');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
