<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;
use App\Models\User;
use App\Models\RewardPembeli;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PembeliController extends Controller
{
    public function index()
    {
        return response()->json(Pembeli::all());
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'nama_pembeli' => 'required|string|max:50',
        'alamat_pembeli' => 'required|string|max:50',
        'nomor_telepon_pembeli' => 'required|string|max:50',
        'email_pembeli' => 'required|email|max:50|unique:pembeli,email_pembeli|unique:users,email',
        'password_pembeli' => 'required|string|min:6|max:50',
    ]);

    // Simpan ke tabel users
    $user = User::create([
        'name' => $validated['nama_pembeli'],
        'email' => $validated['email_pembeli'],
        'password' => Hash::make($validated['password_pembeli']),
        'role' => 'pembeli',
    ]);

    // Simpan ke tabel pembeli
    $pembeli = Pembeli::create([
        'nama_pembeli' => $validated['nama_pembeli'],
        'alamat_pembeli' => $validated['alamat_pembeli'],
        'nomor_telepon_pembeli' => $validated['nomor_telepon_pembeli'],
        'email_pembeli' => $validated['email_pembeli'],
        'password_pembeli' => Hash::make($validated['password_pembeli']),
        'id_user' => $user->id,
    ]);

    return response()->json([
        'message' => 'Data pembeli dan user berhasil ditambahkan',
        'data' => [ 
            'pembeli' => $pembeli,
            'user' => $user
        ]
    ], 201);
}

    public function show($id)
    {
        $pembeli = Pembeli::find($id);

        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli tidak ditemukan'], 404);
        }

        return response()->json($pembeli);
    }

    public function update(Request $request, $id)
    {
        $pembeli = Pembeli::find($id);

        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'nama_pembeli' => 'required|string|max:50',
            'alamat_pembeli' => 'required|string|max:50',
            'nomor_telepon_pembeli' => 'required|string|max:50',
            'email_pembeli' => 'required|email|max:50|unique:pembeli,email_pembeli,' . $pembeli->id_pembeli . ',id_pembeli',
            'password_pembeli' => 'required|string|min:6|max:50',
        ]);

        $validated['password_pembeli'] = Hash::make($validated['password_pembeli']);
        $pembeli->update($validated);

        return response()->json(['message' => 'Data pembeli berhasil diperbarui', 'data' => $pembeli]);
    }

    public function destroy($id)
    {
        $pembeli = Pembeli::find($id);

        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli tidak ditemukan'], 404);
        }

        $pembeli->delete();

        return response()->json(['message' => 'Data pembeli berhasil dihapus']);
    }

    public function search($keyword)
    {
        $results = Pembeli::where('nama_pembeli', 'like', "%$keyword%")
            ->orWhere('alamat_pembeli', 'like', "%$keyword%")
            ->orWhere('nomor_telepon_pembeli', 'like', "%$keyword%")
            ->orWhere('email_pembeli', 'like', "%$keyword%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Pembeli tidak ditemukan'], 404);
        }

        return response()->json([
            'status' => 'success',
            'total_results' => $results->count(),
            'data' => $results
        ], 200);
    }

    // View dashboard pembeli: profil + transaksi + poin
    public function dashboard()
{
    $pembeli = Auth::guard('pembeli')->user();

    // Muat relasi rewards (kalau masih ingin pakai poin)
    $pembeli->load('rewards');

    // Hitung total poin
    $rewardPoints = $pembeli->rewards->sum('jumlah_poin_pembeli');

    // Ambil transaksi pembeli beserta barangnya
    $transactions = Transaksi::with('barang')
        ->where('id_pembeli', $pembeli->id_pembeli)
        ->orderBy('tanggal_pemesanan', 'desc')
        ->get();

    return view('pembeli.dashboardPembeli', compact('pembeli', 'rewardPoints', 'transactions'));
}


    // API: profil pembeli yang login
    public function profile()
    {
        $pembeli = Auth::guard('pembeli')->user();

        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli belum login'], 401);
        }

        $pembeli->load('rewards');
        $totalPoin = $pembeli->rewards->sum('jumlah_poin_pembeli');

        return response()->json([
            'data' => $pembeli,
            'total_poin' => $totalPoin,
            'reward_detail' => $pembeli->rewards,
        ]);
    }

    // API: history transaksi pembeli
    public function historyTransaksi()
    {
        $pembeli = Auth::guard('pembeli')->user();

        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli belum login'], 401);
        }

        $transaksi = Transaksi::with('barang')
            ->where('id_pembeli', $pembeli->id_pembeli)
            ->orderBy('tanggal_pemesanan', 'desc')
            ->get();

        return response()->json([
            'message' => 'Riwayat transaksi ditemukan',
            'data' => $transaksi
        ]);
    }

    public function profilePage()
    {
        $pembeli = Auth::guard('pembeli')->user();

        if (!$pembeli) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $pembeli->load('rewards');
        $totalPoin = $pembeli->rewards->sum('jumlah_poin_pembeli');

        return view('pembeli.profilePage', compact('pembeli', 'totalPoin'));
    }

    public function dashboardPembeli()
    {
        $pembeli = Auth::guard('pembeli')->user();
        return view('pembeli.dashboardPembeli', compact('pembeli'));
    }


    public function updateAlamat(Request $request)
{
    $request->validate([
        'alamat_pembeli' => 'nullable|string|max:255', // nullable untuk bisa dikosongkan
    ]);

    $pembeli = Auth::guard('pembeli')->user();
    $pembeli->alamat_pembeli = $request->alamat_pembeli ?? null;
    $pembeli->save();

    return redirect()->back()->with('success', 'Alamat berhasil diperbarui.');
}


}