<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Tampilkan form untuk request reset password
     */
    public function showRequestForm()
    {
        return view('auth.passwords.email');
    }
    
    /**
     * Proses request dan kirim link reset
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $pembeli = Pembeli::where('email_pembeli', $request->email)->first();
        
        if (!$pembeli) {
            return back()->withErrors(['email' => 'Email tidak ditemukan']);
        }
        
        // Generate token
        $token = Str::random(60);
        
        // Hapus token yang sudah ada untuk email ini
        DB::table('password_resets')->where('email', $pembeli->email_pembeli)->delete();
        
        // Simpan token di database
        DB::table('password_resets')->insert([
            'email' => $pembeli->email_pembeli,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);
        
        // Buat link reset
        $resetLink = url('/reset-password/' . $token . '?email=' . urlencode($pembeli->email_pembeli));
        
        // Kirim email dengan link reset
        Mail::to($pembeli->email_pembeli)->send(new \App\Mail\ResetPasswordMail($resetLink));
        
        return back()->with('status', 'Link reset password telah dikirim ke email Anda');
    }
    
    /**
     * Tampilkan form reset password
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.passwords.reset', [
            'token' => $token, 
            'email' => $request->email
        ]);
    }
    
    /**
     * Proses reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);
        
        $tokenData = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();
        
        if (!$tokenData || !Hash::check($request->token, $tokenData->token)) {
            return back()->withErrors(['email' => 'Token tidak valid']);
        }
        
        // Cek apakah token sudah kadaluarsa (1 jam)
        if (Carbon::parse($tokenData->created_at)->addHour()->isPast()) {
            return back()->withErrors(['email' => 'Token telah kadaluarsa']);
        }
        
        $pembeli = Pembeli::where('email_pembeli', $request->email)->first();
        
        if (!$pembeli) {
            return back()->withErrors(['email' => 'Email tidak ditemukan']);
        }
        
        // Update password
        $pembeli->password_pembeli = Hash::make($request->password);
        $pembeli->save();
        
        // Hapus token
        DB::table('password_resets')->where('email', $request->email)->delete();
        
        return redirect()->route('login')->with('status', 'Password berhasil diubah');
    }
}