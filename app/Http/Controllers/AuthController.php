<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Penitip;
use App\Models\Pembeli;
use App\Models\Organisasi;
use App\Models\Pegawai;
use App\Models\RolePegawai;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Register Pembeli (gabungkan API + Web)
    public function registerPembeli(Request $request)
    {
        $validated = $request->validate([
            'nama_pembeli' => 'required|string|max:50',
            'alamat_pembeli' => 'required|string|max:50',
            'nomor_telepon_pembeli' => 'required|string|max:50',
            'email_pembeli' => 'required|email|max:50|unique:pembeli,email_pembeli',
            'password_pembeli' => 'required|string|min:6|max:50',
        ]);

        $validated['password_pembeli'] = Hash::make($validated['password_pembeli']);
        $pembeli = Pembeli::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Registrasi berhasil',
                'pembeli' => $pembeli,
            ], 201);
        } else {
            return redirect()->route('login')->with('success', 'Registrasi berhasil! Silahkan login untuk melanjutkan.');
        }
    }

    // Register Organisasi (gabungkan API + Web)
    public function registerOrganisasi(Request $request)
    {
        $validated = $request->validate([
            'nama_organisasi' => 'required|string|max:100',
            'alamat_organisasi' => 'required|string|max:100',
            'nomor_telepon_organisasi' => 'required|string|max:20',
            'email_organisasi' => 'required|email|max:100|unique:organisasi,email_organisasi',
            'password_organisasi' => 'required|string|min:6|max:50',
        ]);

        $validated['password_organisasi'] = Hash::make($validated['password_organisasi']);
        $organisasi = Organisasi::create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Registrasi organisasi berhasil',
                'organisasi' => $organisasi,
            ], 201);
        } else {
            return redirect()->route('login')->with('success', 'Registrasi berhasil! Silahkan login untuk melanjutkan.');
        }
    }

    // Login Penitip (gabung API + Web)
    public function loginPenitip(Request $request)
    {
        $request->validate([
            'email_penitip' => 'required|email',
            'password_penitip' => 'required|string|min:3',
        ]);

        $penitip = Penitip::where('email_penitip', $request->email_penitip)->first();

        if (!$penitip || !Hash::check($request->password_penitip, $penitip->password_penitip)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Email atau password salah'], 401);
            } else {
                return back()->withErrors(['email_penitip' => 'Email atau password salah'])->withInput();
            }
        }

        if ($request->wantsJson()) {
            $token = $penitip->createToken('penitip-token')->plainTextToken;
            return response()->json([
                'message' => 'Login berhasil',
                'penitip' => $penitip,
                'token' => $token,
            ]);
        } else {
            Auth::login($penitip);
            return redirect()->route('dashboardPenitip');
        }
    }

    // Login Pembeli (gabung API + Web)
    public function loginPembeli(Request $request)
    {
        $request->validate([
            'email_pembeli' => 'required|email',
            'password_pembeli' => 'required|string|min:3',
        ]);

        $pembeli = Pembeli::where('email_pembeli', $request->email_pembeli)->first();

        if (!$pembeli || !Hash::check($request->password_pembeli, $pembeli->password_pembeli)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Email atau password salah'], 401);
            } else {
                return back()->withErrors(['email_pembeli' => 'Email atau password salah'])->withInput();
            }
        }

        if ($request->wantsJson()) {
            $token = $pembeli->createToken('pembeli-token')->plainTextToken;
            return response()->json([
                'message' => 'Login berhasil',
                'pembeli' => $pembeli,
                'token' => $token,
            ]);
        } else {
            Auth::login($pembeli);
            return redirect()->route('dashboardPembeli');
        }
    }

    // Login Organisasi (gabung API + Web)
    public function loginOrganisasi(Request $request)
    {
        $request->validate([
            'email_organisasi' => 'required|email',
            'password_organisasi' => 'required|string|min:3',
        ]);

        $organisasi = Organisasi::where('email_organisasi', $request->email_organisasi)->first();

        if (!$organisasi || !Hash::check($request->password_organisasi, $organisasi->password_organisasi)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Email atau password salah'], 401);
            } else {
                return back()->withErrors(['email_organisasi' => 'Email atau password salah'])->withInput();
            }
        }

        if ($request->wantsJson()) {
            $token = $organisasi->createToken('organisasi-token')->plainTextToken;
            return response()->json([
                'message' => 'Login berhasil',
                'organisasi' => $organisasi,
                'token' => $token,
            ]);
        } else {
            Auth::login($organisasi);
            return redirect()->route('dashboardOrganisasi');
        }
    }

    // Login Pegawai (admin, owner, cs, gudang) -- asumsi semua pegawai di satu model Pegawai + role
    public function loginPegawai(Request $request)
    {
        $request->validate([
            'email_pegawai' => 'required|email',
            'password_pegawai' => 'required|string|min:3',
        ]);

        $pegawai = Pegawai::where('email_pegawai', $request->email_pegawai)->first();

        if (!$pegawai || !Hash::check($request->password_pegawai, $pegawai->password_pegawai)) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Email atau password salah'], 401);
            } else {
                return back()->withErrors(['email_pegawai' => 'Email atau password salah'])->withInput();
            }
        }

        if ($request->wantsJson()) {
            $token = $pegawai->createToken('pegawai-token')->plainTextToken;
            return response()->json([
                'message' => 'Login berhasil',
                'pegawai' => $pegawai,
                'token' => $token,
            ]);
        } else {
            Auth::login($pegawai);

            // Redirect ke dashboard berdasar role pegawai
            switch ($pegawai->role) {
                case 'admin':
                    return redirect()->route('dashboardAdmin');
                case 'owner':
                    return redirect()->route('dashboardOwner');
                case 'cs':
                    return redirect()->route('dashboardCs');
                case 'gudang':
                    return redirect()->route('dashboardGudang');
                default:
                    Auth::logout();
                    return back()->withErrors(['email_pegawai' => 'Role tidak valid'])->withInput();
            }
        }
    }

    // Logout semua jenis user API (hapus token)
    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logout berhasil']);
        } else {
            return redirect()->route('login')->with('success', 'Logout berhasil');
        }
    }
}