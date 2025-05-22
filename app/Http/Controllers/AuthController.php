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
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // Register Pembeli (gabungkan API + Web)
    public function registerPembeli(Request $request)
    {
        $validated = $request->validate([
            'nama_pembeli' => [
                'required',
                'string',
                'min:2',
                'max:255',
                'regex:/^[a-zA-Z\s]+$/', // Inputan hanya huruf dan spasi
            ],
            'alamat_pembeli' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],
            'nomor_telepon_pembeli' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[0-9+\-\s]+$/', // Inputan hanya angka, +, -, dan spasi
                'unique:pembeli,nomor_telepon_pembeli',
            ],
            'email_pembeli' => [
                'required',
                'email',
                'max:255',
                'unique:pembeli,email_pembeli',
            ],
            'password_pembeli' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', // Inputan harus ada huruf kecil, besar, dan angka
            ],
        ], [
            // Error messages
            'nama_pembeli.required' => 'Nama harus diisi!',
            'nama_pembeli.min' => 'Nama minimal 2 karakter!',
            'nama_pembeli.regex' => 'Nama hanya boleh berisi huruf dan spasi!',
            
            'alamat_pembeli.required' => 'Alamat harus diisi!',
            'alamat_pembeli.min' => 'Alamat minimal 10 karakter!',
            
            'nomor_telepon_pembeli.required' => 'Nomor telepon harus diisi!',
            'nomor_telepon_pembeli.min' => 'Nomor telepon minimal 10 digit!',
            'nomor_telepon_pembeli.max' => 'Nomor telepon maksimal 15 digit!',
            'nomor_telepon_pembeli.regex' => 'Nomor telepon hanya boleh berisi angka!',
            'nomor_telepon_pembeli.unique' => 'Nomor telepon sudah terdaftar!',
            
            'email_pembeli.required' => 'Email harus diisi!',
            'email_pembeli.email' => 'Format email tidak valid!',
            'email_pembeli.unique' => 'Email sudah terdaftar!',
            
            'password_pembeli.required' => 'Password harus diisi!',
            'password_pembeli.min' => 'Password minimal 8 karakter!',
            'password_pembeli.regex' => 'Password harus mengandung huruf besar, kecil, dan angka!',
        ]);

        try {
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
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat registrasi',
                    'error' => $e->getMessage()
                ], 500);
            } else {
                return back()->withInput()->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.');
            }
        }
    }

    // Register Organisasi (gabungkan API + Web) dengan validasi lengkap
    public function registerOrganisasi(Request $request)
    {
        $validated = $request->validate([
            'nama_organisasi' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[a-zA-Z0-9\s\.\,\-]+$/', // Huruf, angka, spasi, titik, koma, strip
            ],
            'alamat_organisasi' => [
                'required',
                'string',
                'min:10',
                'max:500',
            ],
            'nomor_telepon_organisasi' => [
                'required',
                'string',
                'min:10',
                'max:15',
                'regex:/^[0-9+\-\s]+$/',
                'unique:organisasi,nomor_telepon_organisasi',
            ],
            'email_organisasi' => [
                'required',
                'email',
                'max:255',
                'unique:organisasi,email_organisasi',
            ],
            'password_organisasi' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            ],
        ], [
            'nama_organisasi.required' => 'Nama organisasi harus diisi!',
            'nama_organisasi.min' => 'Nama organisasi minimal 3 karakter!',
            'nama_organisasi.regex' => 'Nama organisasi hanya boleh berisi huruf, angka, dan tanda baca dasar!',
            
            'alamat_organisasi.required' => 'Alamat organisasi harus diisi!',
            'alamat_organisasi.min' => 'Alamat organisasi minimal 10 karakter!',
            
            'nomor_telepon_organisasi.required' => 'Nomor telepon harus diisi!',
            'nomor_telepon_organisasi.min' => 'Nomor telepon minimal 10 digit!',
            'nomor_telepon_organisasi.max' => 'Nomor telepon maksimal 15 digit!',
            'nomor_telepon_organisasi.regex' => 'Nomor telepon hanya boleh berisi angka!',
            'nomor_telepon_organisasi.unique' => 'Nomor telepon sudah terdaftar!',
            
            'email_organisasi.required' => 'Email harus diisi!',
            'email_organisasi.email' => 'Format email tidak valid!',
            'email_organisasi.unique' => 'Email sudah terdaftar!',
            
            'password_organisasi.required' => 'Password harus diisi!',
            'password_organisasi.min' => 'Password minimal 8 karakter!',
            'password_organisasi.regex' => 'Password harus mengandung huruf besar, kecil, dan angka!',
        ]);

        try {
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
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Terjadi kesalahan saat registrasi',
                    'error' => $e->getMessage()
                ], 500);
            } else {
                return back()->withInput()->with('error', 'Terjadi kesalahan saat registrasi. Silakan coba lagi.');
            }
        }
    }

    // Login Penitip (gabung API + Web) dengan error handling
    public function loginPenitip(Request $request)
    {
        $request->validate([
            'email_penitip' => [
                'required',
                'email',
                'max:255',
            ],
            'password_penitip' => [
                'required',
                'string',
                'min:6',
            ],
        ], [
            'email_penitip.required' => 'Email harus diisi!',
            'email_penitip.email' => 'Format email tidak valid!',
            'password_penitip.required' => 'Password harus diisi!',
            'password_penitip.min' => 'Password minimal 6 karakter!',
        ]);

        try {
            $penitip = Penitip::where('email_penitip', $request->email_penitip)->first();

            if (!$penitip || !Hash::check($request->password_penitip, $penitip->password_penitip)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Email atau password salah'], 401);
                } else {
                    return back()->withErrors(['login_error' => 'Email atau password salah'])->withInput();
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
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan sistem'], 500);
            } else {
                return back()->withErrors(['login_error' => 'Terjadi kesalahan sistem'])->withInput();
            }
        }
    }

    // Login Pembeli (gabung API + Web) dengan error handling
    public function loginPembeli(Request $request)
    {
        $request->validate([
            'email_pembeli' => [
                'required',
                'email',
                'max:255',
            ],
            'password_pembeli' => [
                'required',
                'string',
                'min:6',
            ],
        ], [
            'email_pembeli.required' => 'Email harus diisi!',
            'email_pembeli.email' => 'Format email tidak valid!',
            'password_pembeli.required' => 'Password harus diisi!',
            'password_pembeli.min' => 'Password minimal 6 karakter!',
        ]);

        try {
            $pembeli = Pembeli::where('email_pembeli', $request->email_pembeli)->first();

            if (!$pembeli || !Hash::check($request->password_pembeli, $pembeli->password_pembeli)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Email atau password salah'], 401);
                } else {
                    return back()->withErrors(['login_error' => 'Email atau password salah'])->withInput();
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
                Auth::guard('pembeli')->login($pembeli);
                return redirect()->route('pembeli.dashboardPembeli');
            }
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan sistem'], 500);
            } else {
                return back()->withErrors(['login_error' => 'Terjadi kesalahan sistem'])->withInput();
            }
        }
    }

    // Login Organisasi (gabung API + Web) dengan error handling
    public function loginOrganisasi(Request $request)
    {
        $request->validate([
            'email_organisasi' => [
                'required',
                'email',
                'max:255',
            ],
            'password_organisasi' => [
                'required',
                'string',
                'min:6',
            ],
        ], [
            'email_organisasi.required' => 'Email harus diisi!',
            'email_organisasi.email' => 'Format email tidak valid!',
            'password_organisasi.required' => 'Password harus diisi!',
            'password_organisasi.min' => 'Password minimal 6 karakter!',
        ]);

        try {
            $organisasi = Organisasi::where('email_organisasi', $request->email_organisasi)->first();

            if (!$organisasi || !Hash::check($request->password_organisasi, $organisasi->password_organisasi)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Email atau password salah'], 401);
                } else {
                    return back()->withErrors(['login_error' => 'Email atau password salah'])->withInput();
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
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan sistem'], 500);
            } else {
                return back()->withErrors(['login_error' => 'Terjadi kesalahan sistem'])->withInput();
            }
        }
    }

    // Login Pegawai dengan error handling lengkap
    public function loginPegawai(Request $request)
    {
        $request->validate([
            'email_pegawai' => [
                'required',
                'email',
                'max:255',
            ],
            'password_pegawai' => [
                'required',
                'string',
                'min:6',
            ],
        ], [
            'email_pegawai.required' => 'Email harus diisi!',
            'email_pegawai.email' => 'Format email tidak valid!',
            'password_pegawai.required' => 'Password harus diisi!',
            'password_pegawai.min' => 'Password minimal 6 karakter!',
        ]);

        try {
            $pegawai = Pegawai::where('email_pegawai', $request->email_pegawai)->first();

            if (!$pegawai || !Hash::check($request->password_pegawai, $pegawai->password_pegawai)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Email atau password salah'], 401);
                } else {
                    return back()->withErrors(['login_error' => 'Email atau password salah'])->withInput();
                }
            }

            // Validasi role pegawai
            $validRoles = ['admin', 'owner', 'cs', 'gudang'];
            if (!in_array($pegawai->role, $validRoles)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Role tidak valid'], 403);
                } else {
                    return back()->withErrors(['login_error' => 'Role tidak valid'])->withInput();
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
                        return back()->withErrors(['login_error' => 'Role tidak valid'])->withInput();
                }
            }
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan sistem'], 500);
            } else {
                return back()->withErrors(['login_error' => 'Terjadi kesalahan sistem'])->withInput();
            }
        }
    }

    // Logout semua jenis user API (hapus token)
    public function logout(Request $request)
    {
        try {
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }

            // Untuk web logout
            if (!$request->wantsJson()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Logout berhasil']);
            } else {
                return redirect()->route('login')->with('success', 'Logout berhasil');
            }
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat logout'], 500);
            } else {
                return redirect()->route('login')->with('error', 'Terjadi kesalahan saat logout');
            }
        }
    }
}