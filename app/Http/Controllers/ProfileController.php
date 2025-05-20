<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pembeli;
use App\Models\Pegawai;
use App\Models\Penitip;
use App\Models\Organisasi;

class ProfileController extends Controller
{
    // Show profile based on user role
    public function show()
    {
        $user = Auth::user();
        $profileData = null;
        
        // Get profile data based on user role
        switch ($user->role) {
            case 'admin':
            case 'pegawai':
            case 'owner':
            case 'gudang':
            case 'cs':
                $profileData = Pegawai::where('email_pegawai', $user->email)->first();
                return view('profile.pegawai', compact('profileData'));
                
            case 'pembeli':
                $profileData = Pembeli::where('email_pembeli', $user->email)->first();
                return view('profile.pembeli', compact('profileData'));
                
            case 'penitip':
                $profileData = Penitip::where('email_penitip', $user->email)->first();
                return view('profile.penitip', compact('profileData'));
                
            case 'organisasi':
                $profileData = Organisasi::where('email_organisasi', $user->email)->first();
                return view('profile.organisasi', compact('profileData'));
                
            default:
                return redirect('/')->with('error', 'Unknown user role');
        }
    }
    
    // Update profile
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Different validation rules based on role
        switch ($user->role) {
            case 'pembeli':
                $validated = $request->validate([
                    'nama_pembeli' => 'required|string|max:50',
                    'alamat_pembeli' => 'required|string|max:50',
                    'nomor_telepon_pembeli' => 'required|string|max:50',
                    'password' => 'nullable|string|min:6|confirmed',
                ]);
                
                $pembeli = Pembeli::where('email_pembeli', $user->email)->first();
                
                if ($pembeli) {
                    $pembeli->nama_pembeli = $validated['nama_pembeli'];
                    $pembeli->alamat_pembeli = $validated['alamat_pembeli'];
                    $pembeli->nomor_telepon_pembeli = $validated['nomor_telepon_pembeli'];
                    
                    if (!empty($validated['password'])) {
                        $pembeli->password_pembeli = Hash::make($validated['password']);
                        $user->password = Hash::make($validated['password']);
                        $user->save();
                    }
                    
                    $pembeli->save();
                    return back()->with('success', 'Profile updated successfully');
                }
                break;
                
            // Add cases for other roles...
        }
        
        return back()->with('error', 'Failed to update profile');
    }
}