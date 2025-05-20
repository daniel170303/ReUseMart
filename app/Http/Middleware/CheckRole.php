<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles  Role yang diizinkan
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek jika user sudah login
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            return redirect('login');
        }

        // Cek jika user memiliki salah satu role yang diizinkan
        if (!in_array(Auth::user()->role, $roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak memiliki izin untuk mengakses resource ini.'
                ], 403);
            }
            
            // Redirect berdasarkan role user
            switch (Auth::user()->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'penitip':
                    return redirect()->route('penitip.dashboard');
                case 'pembeli':
                    return redirect()->route('pembeli.dashboard');
                case 'organisasi':
                    return redirect()->route('organisasi.dashboard');
                case 'owner':
                    return redirect()->route('owner.dashboard');
                case 'pegawai':
                    return redirect()->route('pegawai.dashboard');
                case 'gudang':
                    return redirect()->route('gudang.dashboard');
                case 'cs':
                    return redirect()->route('cs.dashboard');
                default:
                    return redirect('/');
            }
        }

        return $next($request);
    }
}