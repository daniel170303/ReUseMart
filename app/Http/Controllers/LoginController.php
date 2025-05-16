<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pembeli;
use App\Models\Organisasi;
use App\Models\Penitip;

class LoginController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('login.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Cari user berdasarkan email
        $user = User::where('email', $credentials['email'])->first();

        // Jika user ditemukan dan password cocok
        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();
            
            // Redirect sesuai role
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'pegawai' => redirect()->route('pegawai.dashboard'),
                'owner' => redirect()->route('owner.dashboard'),
                'gudang' => redirect()->route('gudang.dashboard'),
                'cs' => redirect()->route('cs.dashboard'),
                'penitip' => redirect()->route('penitip.dashboard'),
                'organisasi' => redirect()->route('organisasi.dashboard'),
                'pembeli' => redirect()->route('pembeli.dashboard'),
                default => redirect('/'),
            };
        }

        // Jika login gagal, kembalikan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('pembeli')->logout();
        Auth::guard('organisasi')->logout();
        Auth::guard('penitip')->logout();
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
