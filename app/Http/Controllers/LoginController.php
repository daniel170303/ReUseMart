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

        $email = $credentials['email'];
        $password = $credentials['password'];

        // Cek di pembeli (gunakan kolom email_pembeli)
        $user = Pembeli::where('email_pembeli', $email)->first();
        if ($user && Hash::check($password, $user->password_pembeli)) {
            Auth::guard('pembeli')->login($user);
            $request->session()->regenerate();
            return redirect()->route('dashboard.pembeli');
        }

        // Cek di organisasi (gunakan kolom email_organisasi)
        $user = Organisasi::where('email_organisasi', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            Auth::guard('organisasi')->login($user);
            $request->session()->regenerate();
            return redirect()->route('dashboard.organisasi');
        }

        // Cek di penitip (gunakan kolom email_penitip)
        $user = Penitip::where('email_penitip', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            Auth::guard('penitip')->login($user);
            $request->session()->regenerate();
            return redirect()->route('penitip.dashboard');
        }

        // Cek di users (user biasa, pakai kolom email)
        $user = User::where('email', $email)->first();
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            switch ($user->role) {
                case 'admin':
                    return redirect()->route('dashboard.admin');
                case 'pegawai':
                    return redirect()->route('dashboard.pegawai');
                case 'owner':
                    return redirect()->route('dashboard.owner');
                case 'gudang':
                    return redirect()->route('dashboard.gudang');
                case 'cs':
                    return redirect()->route('dashboard.cs');
                default:
                    Auth::logout();
                    return back()->withErrors(['role' => 'Role tidak dikenali']);
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    // Logout untuk semua guard
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