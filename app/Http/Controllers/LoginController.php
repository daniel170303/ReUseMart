<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pembeli;
use App\Models\Organisasi;
use App\Models\Penitip;
use App\Models\Pegawai;
use App\Models\RolePegawai;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class LoginController extends Controller
{
    // Tampilkan form login
    public function showLoginForm()
    {
        return view('login.login');
    }

    // Unified Login Method - Handle semua role dari 1 form
    public function login(Request $request)
    {
        // Jika ada parameter role, gunakan method yang sesuai
        if ($request->has('role')) {
            return $this->handleRoleBasedLogin($request);
        }

        // Default login behavior (tanpa role parameter)
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        $email = $credentials['email'];
        $password = $credentials['password'];

        // Log untuk debugging
        Log::info('Login attempt for email: ' . $email);

        // Cari user di berbagai tabel
        $user = $this->findUserAcrossTables($email);

        if (!$user) {
            Log::info('User not found for email: ' . $email);
            return back()->withErrors([
                'email' => 'Email tidak terdaftar dalam sistem.',
            ])->withInput($request->except('password'));
        }

        // Verifikasi password
        if (!Hash::check($password, $user['password'])) {
            Log::info('Password mismatch for email: ' . $email);
            return back()->withErrors([
                'password' => 'Password yang Anda masukkan salah.',
            ])->withInput($request->except('password'));
        }

        // Login dengan guard yang sesuai
        $guardLogin = $this->loginWithAppropriateGuard($user, $request);
        
        if (!$guardLogin) {
            Log::error('Failed to login with appropriate guard for email: ' . $email);
            return back()->withErrors([
                'login_error' => 'Terjadi kesalahan saat login. Silakan coba lagi.',
            ])->withInput($request->except('password'));
        }

        Log::info('User logged in successfully: ' . $email . ' with role: ' . $user['role']);

        // Redirect sesuai role
        return $this->redirectByRole($user['role']);
    }

    /**
     * Login dengan guard yang sesuai berdasarkan role user
     */
    private function loginWithAppropriateGuard($userData, $request)
    {
        try {
            // Tentukan guard berdasarkan role
            $guardName = $this->getGuardByRole($userData['role']);
            
            // Ambil model original berdasarkan tipe user
            $originalModel = $userData['original_data'];
            
            // Login menggunakan guard yang sesuai
            Auth::guard($guardName)->login($originalModel, false); // true untuk remember me
            
            // Regenerate session untuk keamanan
            $request->session()->regenerate();
            
            // Simpan data tambahan ke session
            session([
                'user_role' => $userData['role'],
                'user_model' => $userData['model'],
                'user_id' => $userData['id'],
                'user_name' => $userData['name'],
                'user_email' => $userData['email']
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Login guard error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Dapatkan nama guard berdasarkan role
     */
    private function getGuardByRole($role)
    {
        $guardMapping = [
            'pembeli' => 'pembeli',
            'organisasi' => 'organisasi',
            'penitip' => 'penitip',
            'owner' => 'pegawai',
            'admin' => 'pegawai',
            'cs' => 'pegawai',
            'gudang' => 'pegawai',
            'hunter' => 'pegawai',
            'kurir' => 'pegawai',
        ];

        return $guardMapping[$role] ?? 'web';
    }

    // Handle login dengan role parameter (untuk backward compatibility dengan AuthController)
    private function handleRoleBasedLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:penitip,pembeli,organisasi,pegawai'
        ], [
            'email.required' => 'Email harus diisi!',
            'email.email' => 'Format email tidak valid!',
            'password.required' => 'Password harus diisi!',
            'password.min' => 'Password minimal 6 karakter!',
            'role.required' => 'Role harus dipilih!',
            'role.in' => 'Role tidak valid!'
        ]);

        // Dispatch ke method login yang sesuai berdasarkan role
        switch($request->role) {
            case 'penitip':
                return $this->handlePenitipLogin($request);
            case 'pembeli':
                return $this->handlePembeliLogin($request);
            case 'organisasi':
                return $this->handleOrganisasiLogin($request);
            case 'pegawai':
                return $this->handlePegawaiLogin($request);
            default:
                return back()->withErrors(['login_error' => 'Role tidak valid'])->withInput();
        }
    }

    /**
     * Cari user di semua tabel yang relevan
     */
    private function findUserAcrossTables($email)
    {
        // Cek di tabel pegawai (owner, admin, cs, gudang, hunter, kurir)
        $pegawai = Pegawai::where('email_pegawai', $email)->first();
        if ($pegawai) {
            // Ambil nama role dari tabel role_pegawai
            $rolePegawai = RolePegawai::find($pegawai->id_role);
            
            // Mapping nama role dari database ke role yang digunakan di sistem
            $roleMapping = [
                'Owner' => 'owner',
                'Admin' => 'admin', 
                'Customer Service' => 'cs',
                'Gudang' => 'gudang',
                'Hunter' => 'hunter',
                'Kurir' => 'kurir'
            ];
            
            $roleName = 'pegawai'; // default
            if ($rolePegawai && isset($roleMapping[$rolePegawai->nama_role])) {
                $roleName = $roleMapping[$rolePegawai->nama_role];
            }
            
            return [
                'id' => $pegawai->id_pegawai,
                'name' => $pegawai->nama_pegawai,
                'email' => $pegawai->email_pegawai,
                'password' => $pegawai->password_pegawai,
                'role' => $roleName,
                'phone' => $pegawai->nomor_telepon_pegawai,
                'model' => 'Pegawai',
                'role_id' => $pegawai->id_role,
                'role_name' => $rolePegawai ? $rolePegawai->nama_role : null,
                'original_data' => $pegawai
            ];
        }

        // Cek di tabel pembeli
        $pembeli = Pembeli::where('email_pembeli', $email)->first();
        if ($pembeli) {
            return [
                'id' => $pembeli->id_pembeli,
                'name' => $pembeli->nama_pembeli,
                'email' => $pembeli->email_pembeli,
                'password' => $pembeli->password_pembeli,
                'role' => 'pembeli',
                'phone' => $pembeli->nomor_telepon_pembeli ?? null,
                'model' => 'Pembeli',
                'original_data' => $pembeli
            ];
        }

        // Cek di tabel organisasi
        $organisasi = Organisasi::where('email_organisasi', $email)->first();
        if ($organisasi) {
            return [
                'id' => $organisasi->id_organisasi,
                'name' => $organisasi->nama_organisasi,
                'email' => $organisasi->email_organisasi,
                'password' => $organisasi->password_organisasi,
                'role' => 'organisasi',
                'phone' => $organisasi->nomor_telepon_organisasi ?? null,
                'model' => 'Organisasi',
                'original_data' => $organisasi
            ];
        }

        // Cek di tabel penitip
        $penitip = Penitip::where('email_penitip', $email)->first();
        if ($penitip) {
            return [
                'id' => $penitip->id_penitip,
                'name' => $penitip->nama_penitip,
                'email' => $penitip->email_penitip,
                'password' => $penitip->password_penitip,
                'role' => 'penitip',
                'phone' => $penitip->nomor_telepon_penitip ?? null,
                'model' => 'Penitip',
                'original_data' => $penitip
            ];
        }

        return null;
    }

    /**
     * Redirect berdasarkan role
     */
    private function redirectByRole($role)
    {
        $routes = [
            // Role dari tabel pegawai
            'owner' => 'owner.dashboard',
            'admin' => 'admin.dashboard',
            'cs' => 'cs.dashboard',
            'gudang' => 'gudang.dashboard',
            'hunter' => 'hunter.dashboard',
            'kurir' => 'kurir.dashboard',
            
            // Role dari tabel lain
            'pembeli' => 'pembeli.dashboard',
            'organisasi' => 'organisasi.dashboard',
            'penitip' => 'penitip.dashboard',
        ];

        $routeName = $routes[$role] ?? 'dashboard';

        // Cek apakah route exists
        if (\Route::has($routeName)) {
            return redirect()->route($routeName)->with('success', 'Login berhasil! Selamat datang ' . ucfirst($role));
        }

        // Fallback redirect
        return redirect('/dashboard')->with('success', 'Login berhasil!');
    }

    // Handle Penitip Login (untuk API dan backward compatibility)
    private function handlePenitipLogin(Request $request)
    {
        try {
            $penitip = Penitip::where('email_penitip', $request->email)->first();

            if (!$penitip || !Hash::check($request->password, $penitip->password_penitip)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Email atau password salah'], 401);
                } else {
                    return back()->withErrors(['login_error' => 'Email atau password salah untuk penitip'])->withInput();
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
                Auth::guard('penitip')->login($penitip, true);
                $request->session()->regenerate();

                session([
                    'user_role' => 'penitip',
                    'user_model' => 'Penitip',
                    'user_id' => $penitip->id_penitip,
                    'user_name' => $penitip->nama_penitip,
                    'user_email' => $penitip->email_penitip
                ]);

                return $this->redirectByRole('penitip');
            }
        } catch (\Exception $e) {
            Log::error('Penitip login error: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan sistem'], 500);
            } else {
                return back()->withErrors(['login_error' => 'Terjadi kesalahan sistem'])->withInput();
            }
        }
    }

    // Handle Pembeli Login (untuk API dan backward compatibility)
    private function handlePembeliLogin(Request $request)
    {
        try {
            $pembeli = Pembeli::where('email_pembeli', $request->email)->first();

            if (!$pembeli || !Hash::check($request->password, $pembeli->password_pembeli)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Email atau password salah'], 401);
                } else {
                    return back()->withErrors(['login_error' => 'Email atau password salah untuk pembeli'])->withInput();
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
                // Login dengan guard pembeli
                Auth::guard('pembeli')->login($pembeli, true);
                $request->session()->regenerate();
                
                session([
                    'user_role' => 'pembeli',
                    'user_model' => 'Pembeli',
                    'user_id' => $pembeli->id_pembeli,
                    'user_name' => $pembeli->nama_pembeli,
                    'user_email' => $pembeli->email_pembeli
                ]);
                
                return $this->redirectByRole('pembeli');
            }
        } catch (\Exception $e) {
            Log::error('Pembeli login error: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan sistem'], 500);
            } else {
                return back()->withErrors(['login_error' => 'Terjadi kesalahan sistem'])->withInput();
            }
        }
    }

    // Handle Organisasi Login (untuk API dan backward compatibility)
    private function handleOrganisasiLogin(Request $request)
    {
        try {
            $organisasi = Organisasi::where('email_organisasi', $request->email)->first();

            if (!$organisasi || !Hash::check($request->password, $organisasi->password_organisasi)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Email atau password salah'], 401);
                } else {
                    return back()->withErrors(['login_error' => 'Email atau password salah untuk organisasi'])->withInput();
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
                // Login dengan guard organisasi
                Auth::guard('organisasi')->login($organisasi, true);
                $request->session()->regenerate();
                
                session([
                    'user_role' => 'organisasi',
                    'user_model' => 'Organisasi',
                    'user_id' => $organisasi->id_organisasi,
                    'user_name' => $organisasi->nama_organisasi,
                    'user_email' => $organisasi->email_organisasi
                ]);
                
                return $this->redirectByRole('organisasi');
            }
        } catch (\Exception $e) {
            Log::error('Organisasi login error: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan sistem'], 500);
            } else {
                return back()->withErrors(['login_error' => 'Terjadi kesalahan sistem'])->withInput();
            }
        }
    }

    // Handle Pegawai Login (untuk API dan backward compatibility)
    private function handlePegawaiLogin(Request $request)
    {
        try {
            $pegawai = Pegawai::where('email_pegawai', $request->email)->first();

            if (!$pegawai || !Hash::check($request->password, $pegawai->password_pegawai)) {
                if ($request->wantsJson()) {
                    return response()->json(['message' => 'Email atau password salah'], 401);
                } else {
                    return back()->withErrors(['login_error' => 'Email atau password salah untuk pegawai'])->withInput();
                }
            }

            // Ambil role pegawai
            $rolePegawai = RolePegawai::find($pegawai->id_role);

            // Pemetaan nama role ke route atau identifier
            $roleMapping = [
                'Owner' => 'owner',
                'Admin' => 'admin',
                'Customer Service' => 'cs',
                'Gudang' => 'gudang',
                'Hunter' => 'hunter',
                'Kurir' => 'kurir'
            ];

            $roleName = 'pegawai'; // default fallback
            if ($rolePegawai && isset($roleMapping[$rolePegawai->nama_role])) {
                $roleName = $roleMapping[$rolePegawai->nama_role];
            }

            // Login menggunakan guard pegawai
            Auth::guard('pegawai')->login($pegawai, true);
            $request->session()->regenerate();

            // Set session pegawai (bisa diakses global)
            session([
                'pegawai_id' => $pegawai->id_pegawai,
                'pegawai_nama' => $pegawai->nama_pegawai,
                'pegawai_email' => $pegawai->email_pegawai,
                'pegawai_role_id' => $pegawai->id_role,
                'pegawai_role_name' => $rolePegawai ? $rolePegawai->nama_role : null,
                'user_role' => 'pegawai', // tag sebagai user tipe pegawai
            ]);

            // Jika API request
            if ($request->wantsJson()) {
                $token = $pegawai->createToken('pegawai-token')->plainTextToken;
                return response()->json([
                    'message' => 'Login berhasil',
                    'pegawai' => $pegawai,
                    'role' => $roleName,
                    'token' => $token,
                ]);
            }

            // Redirect sesuai role
            return $this->redirectByRole($roleName);

        } catch (\Exception $e) {
            Log::error('Pegawai login error: ' . $e->getMessage());

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan sistem'], 500);
            } else {
                return back()->withErrors(['login_error' => 'Terjadi kesalahan sistem'])->withInput();
            }
        }
    }

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
            Log::error('Register pembeli error: ' . $e->getMessage());
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
            Log::error('Register organisasi error: ' . $e->getMessage());
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

    // Logout - Perbaikan untuk multi-guard
    public function logout(Request $request)
    {
        try {
            // Untuk API logout (hapus token)
            if ($request->user() && method_exists($request->user(), 'currentAccessToken')) {
                $request->user()->currentAccessToken()->delete();
            }

            // Logout dari semua guard
            Auth::guard('web')->logout();
            Auth::guard('pembeli')->logout();
            Auth::guard('organisasi')->logout();
            Auth::guard('penitip')->logout();
            Auth::guard('pegawai')->logout();

            // Clear session data
            $request->session()->forget(['user_model', 'user_original_data', 'user_role', 'user_id', 'user_name', 'user_email', 'role_id', 'role_name']);
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Logout berhasil']);
            } else {
                return redirect()->route('login')->with('success', 'Berhasil logout.');
            }
        } catch (\Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Terjadi kesalahan saat logout'], 500);
            } else {
                return redirect()->route('login')->with('error', 'Terjadi kesalahan saat logout');
            }
        }
    }

    // Backward compatibility methods - tetap bisa digunakan untuk API terpisah
    public function loginPenitip(Request $request)
    {
        $request->merge(['role' => 'penitip']);
        return $this->handlePenitipLogin($request);
    }

    public function loginPembeli(Request $request)
    {
        $request->merge(['role' => 'pembeli']);
        return $this->handlePembeliLogin($request);
    }

    public function loginOrganisasi(Request $request)
    {
        $request->merge(['role' => 'organisasi']);
        return $this->handleOrganisasiLogin($request);
    }

    public function loginPegawai(Request $request)
    {
        $request->merge(['role' => 'pegawai']);
        return $this->handlePegawaiLogin($request);
    }
}
