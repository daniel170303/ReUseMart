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


public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string|min:6',
        'role' => 'required|in:admin,pegawai,owner,gudang,cs,penitip,pembeli,organisasi',
    ]);

    // Find user based on email and role
    $user = User::where('email', $credentials['email'])
                ->where('role', $credentials['role'])
                ->first();

    if ($user && Hash::check($credentials['password'], $user->password)) {
        Auth::login($user);

        // Redirect based on role
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

    return back()->withErrors([
        'email' => 'Email, password, atau role salah.',
    ])->onlyInput('email');
}


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
