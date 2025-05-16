<?php

namespace App\Http\Controllers;

use App\Models\Pembeli;
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
            'email_pembeli' => 'required|email|max:50|unique:pembeli,email_pembeli',
            'password_pembeli' => 'required|string|min:6|max:50',
        ]);

        $validated['password_pembeli'] = Hash::make($validated['password_pembeli']);
        $pembeli = Pembeli::create($validated);

        return response()->json(['message' => 'Data pembeli berhasil ditambahkan', 'data' => $pembeli], 201);
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

        if (!$pembeli) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Load relasi rewards supaya bisa hitung poin
        $pembeli->load('rewards');

        // Ambil data transaksi pembeli dengan barang terkait
        $transactions = Transaksi::with('barang')
            ->where('id_pembeli', $pembeli->id_pembeli)
            ->orderBy('tanggal_pemesanan', 'desc')
            ->get();

        // Hitung total poin dari rewards
        $rewardPoints = $pembeli->rewards->sum('jumlah_poin_pembeli');

        // Kirim variabel ke view
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
}