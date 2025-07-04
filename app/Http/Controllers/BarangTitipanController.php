<?php

namespace App\Http\Controllers;

use App\Models\BarangTitipan;
use App\Models\Transaksi;
use App\Models\GambarBarangTitipan;
use App\Models\PencatatanPegawaiGudang;
use App\Models\Pegawai;
use App\Models\BarangTitipanHunter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class BarangTitipanController extends Controller
{
    // HAPUS constructor middleware - tidak diperlukan karena sudah ada di routes

    // Menampilkan semua barang
    public function index(Request $request)
    {
        // Cek apakah user adalah pegawai gudang (role_id = 4 atau sesuai dengan sistem Anda)
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai || !in_array($pegawai->id_role, [4, 1])) { // 4 = gudang, 1 = owner (bisa akses semua)
            return redirect()->route('login')->with('error', 'Akses ditolak. Anda bukan pegawai gudang.');
        }

        // Mulai query untuk barang titipan dengan relasi gambar tambahan
        $query = BarangTitipan::with(['transaksiTerakhir', 'gambarBarangTitipan']);

        // Jika ada parameter pencarian
        if ($request->has('search') && $request->search != '') {
            $keyword = $request->search;

            // Filter berdasarkan keyword
            $query->where(function($q) use ($keyword) {
                $q->where('nama_barang_titipan', 'like', "%$keyword%")
                ->orWhere('harga_barang', 'like', "%$keyword%")
                ->orWhere('deskripsi_barang', 'like', "%$keyword%")
                ->orWhere('jenis_barang', 'like', "%$keyword%")
                ->orWhere('berat_barang', 'like', "%$keyword%")
                ->orWhere('status_barang', 'like', "%$keyword%");
            });
        }

        // Ambil semua barang titipan
        $barangTitipan = $query->get();

        // Proses status garansi berdasarkan tanggal akhir garansi
        foreach ($barangTitipan as $barang) {
            if ($barang->garansi_barang === null) {
                $barang->status_garansi = 'Tanpa Garansi';
            } else {
                $tanggalGaransi = Carbon::parse($barang->garansi_barang);
                $hariIni = Carbon::now();
                
                if ($tanggalGaransi->gte($hariIni)) {
                    $sisaHari = $hariIni->diffInDays($tanggalGaransi);
                    $barang->status_garansi = 'Masih Bergaransi sampai ' . $tanggalGaransi->translatedFormat('d M Y') . " ({$sisaHari} hari lagi)";
                    $barang->garansi_masih_berlaku = true;
                } else {
                    $hariLewat = $tanggalGaransi->diffInDays($hariIni);
                    $barang->status_garansi = 'Garansi Habis pada ' . $tanggalGaransi->translatedFormat('d M Y') . " ({$hariLewat} hari yang lalu)";
                    $barang->garansi_masih_berlaku = false;
                }
            }
        }

        // Ambil transaksi yang statusnya 'Dikirim' atau 'Diambil'
        $transaksiProses = Transaksi::with(['barangTitipan', 'barangTitipan.gambarBarangTitipan'])
            ->whereIn('status_transaksi', ['Dikirim', 'Diambil'])
            ->get();

        // Ambil data kurir
        $kurirs = Pegawai::where('id_role', 6)->get();

        // Ambil data hunter - PASTIKAN ROLE ID BENAR
        // Ganti angka 5 dengan role ID yang sesuai untuk hunter di database Anda
        $hunters = Pegawai::where('id_role', 5)->get(); // Sesuaikan dengan role hunter

        // Debug: Cek apakah ada data hunter
        \Log::info('Jumlah hunter ditemukan: ' . $hunters->count());
        foreach ($hunters as $hunter) {
            \Log::info('Hunter: ' . $hunter->nama_pegawai . ' (ID: ' . $hunter->id_pegawai . ')');
        }

        // Kembalikan tampilan dengan data
        return view('pegawai.gudang.manajemenBarangTitipan', compact('barangTitipan', 'transaksiProses', 'kurirs', 'hunters'));
    }

    // Menyimpan barang baru
    public function store(Request $request)
    {
        // Cek apakah user adalah pegawai gudang
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai || !in_array($pegawai->id_role, [4, 1])) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda bukan pegawai gudang.');
        }

        // Define allowed categories as array for better maintenance
        $allowedCategories = [
            'Elektronik & Gadget',
            'Pakaian & Aksesori',
            'Perabotan Rumah Tangga',
            'Buku, Alat Tulis, & Peralatan Sekolah',
            'Hobi, Mainan, & Koleksi',
            'Perlengkapan Bayi & Anak',
            'Otomotif & Aksesori',
            'Perlengkapan Taman & Outdoor',
            'Peralatan Kantor & Industri',
            'Kosmetik & Perawatan Diri'
        ];

        // Validasi berbeda berdasarkan mode
        $rules = [
            'nama_barang_titipan' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'deskripsi_barang' => 'required|string',
            'jenis_barang' => ['required', Rule::in($allowedCategories)],
            'garansi_barang' => 'required|date',
            'berat_barang' => 'required|integer',
            'status_barang' => 'required|in:dijual,terjual,sudah diambil penitip,sudah didonasikan,barang untuk donasi',
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'mode' => 'required|in:penitip,hunter',
        ];

        // Jika mode hunter, hunter_id wajib diisi
        if ($request->mode === 'hunter') {
            $rules['hunter_id'] = 'required|exists:pegawai,id_pegawai';
        }

        // Add custom error messages
        $messages = [
            'jenis_barang.required' => 'Jenis barang harus dipilih.',
            'jenis_barang.in' => 'Jenis barang yang dipilih tidak valid. Pilih salah satu dari kategori yang tersedia.',
        ];

        try {
            $validatedData = $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Debug: Log the actual value being validated
            \Log::error('Validation failed for jenis_barang', [
                'submitted_value' => $request->jenis_barang,
                'allowed_values' => $allowedCategories,
                'errors' => $e->errors()
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Validasi gagal. Periksa kembali data yang dimasukkan.');
        }

        // Rest of your store method...
        try {
            DB::beginTransaction();

            // Upload gambar utama jika ada
            if ($request->hasFile('gambar_barang')) {
                $path = $request->file('gambar_barang')->store('gambar_barang', 'public');
                $validatedData['gambar_barang'] = $path;
            }

            // Format tanggal garansi
            if ($request->garansi_barang) {
                $validatedData['garansi_barang'] = Carbon::parse($request->garansi_barang)->format('Y-m-d');
            }

            // Hapus field yang tidak ada di tabel barang_titipan
            $barangData = $validatedData;
            unset($barangData['mode'], $barangData['hunter_id']);

            // Simpan data barang utama
            $barang = BarangTitipan::create($barangData);

            // Simpan gambar tambahan jika ada
            if ($request->hasFile('gambar')) {
                foreach ($request->file('gambar') as $file) {
                    $path = $file->store('gambar_barang_titipan', 'public');
                    
                    GambarBarangTitipan::create([
                        'id_barang' => $barang->id_barang,
                        'nama_file_gambar' => basename($path),
                    ]);
                }
            }

            // SELALU catat pegawai gudang yang menambahkan barang (di kedua mode)
            try {
                $this->catatPegawaiGudang($barang->id_barang);
            } catch (\Exception $e) {
                \Log::warning('Gagal mencatat pegawai gudang: ' . $e->getMessage());
            }

            // Pencatatan tambahan berdasarkan mode
            if ($request->mode === 'hunter') {
                // Mode Hunter: Catat hunter yang dipilih SELAIN pegawai gudang
                BarangTitipanHunter::create([
                    'id_barang' => $barang->id_barang,
                    'id_pegawai' => $request->hunter_id, // ID Hunter yang dipilih
                ]);
                
                $hunterName = Pegawai::find($request->hunter_id)->nama_pegawai;
                $successMessage = 'Barang berhasil ditambahkan untuk Hunter: ' . $hunterName . 
                    ' (Pegawai gudang: ' . $pegawai->nama_pegawai . ' juga tercatat)';
            } else {
                // Mode Penitip: Hanya pegawai gudang yang tercatat
                $successMessage = 'Barang berhasil ditambahkan untuk Penitip (Pegawai gudang: ' . 
                    $pegawai->nama_pegawai . ' tercatat)';
            }

            DB::commit();

            return redirect()->route('gudang.index')->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error storing barang titipan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan barang: ' . $e->getMessage());
        }
    }

    /**
     * Mencatat pegawai gudang yang menambah barang titipan
     */
    private function catatPegawaiGudang($idBarang)
    {
        // Ambil ID pegawai dari auth guard pegawai
        $pegawai = Auth::guard('pegawai')->user();
        
        if (!$pegawai) {
            throw new \Exception('Pegawai tidak ditemukan. Pastikan pegawai sudah login.');
        }

        // Simpan pencatatan
        PencatatanPegawaiGudang::create([
            'id_barang' => $idBarang,
            'id_pegawai' => $pegawai->id_pegawai,
        ]);
    }

    // Fitur pencarian
    public function search($keyword)
    {
        $results = BarangTitipan::where('nama_barang_titipan', 'like', "%$keyword%")
            ->orWhere('harga_barang', 'like', "%$keyword%")
            ->orWhere('deskripsi_barang', 'like', "%$keyword%")
            ->orWhere('jenis_barang', 'like', "%$keyword%")
            ->orWhere('garansi_barang', 'like', "%$keyword%")
            ->orWhere('berat_barang', 'like', "%$keyword%")
            ->orWhere('status_barang', 'like', "%$keyword%")
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        return response()->json($results, 200);
    }

    // Menampilkan barang berdasarkan nama
    public function show($nama)
    {
        $barang = BarangTitipan::where('nama_barang_titipan', 'like', '%' . $nama . '%')->get();
        if ($barang->isEmpty()) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }
        return response()->json($barang);
    }

    // Menampilkan detail barang + gambar + diskusi + rating
    public function showDetail($id)
    {
        $barang = BarangTitipan::with([
            'gambarBarangTitipan',
            'transaksiTerakhir',
            'penitipan',  // ← ambil data penitip via hasOneThrough
            'rating'
        ])->findOrFail($id);

        $penitip = $barang->detailPenitipan->penitipan->penitip ?? null;

        // Hitung rata-rata rating untuk semua barang milik penitip
        $averageRating = null;

        if ($penitip) {
            // Ambil semua barang milik penitip
            $barangMilikPenitip = \App\Models\BarangTitipan::whereHas('detailPenitipan.penitipan', function ($query) use ($penitip) {
                $query->where('id_penitip', $penitip->id_penitip);
            })->with('rating')->get();

            // Hitung rata-rata rating dari semua barang yang sudah diberi rating
            $totalRating = 0;
            $jumlahRating = 0;

            foreach ($barangMilikPenitip as $b) {
                if ($b->rating) {
                    $totalRating += $b->rating->rating;
                    $jumlahRating++;
                }
            }

            if ($jumlahRating > 0) {
                $averageRating = round($totalRating / $jumlahRating, 2);
            }
        }

        // Garansi
        if ($barang->transaksiTerakhir && $barang->garansi_bulan !== null) {
            $tanggalPelunasan = Carbon::parse($barang->transaksiTerakhir->tanggal_pelunasan);
            $barang->tanggal_habis_garansi = $tanggalPelunasan->addMonths($barang->garansi_bulan)->translatedFormat('d F Y');
        } else {
            $barang->tanggal_habis_garansi = 'Tidak ada garansi atau belum terjual';
        }

        $diskusi = DB::table('diskusi_produk')
            ->where('id_barang', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('detailBarang', compact('barang', 'diskusi', 'averageRating', 'penitip'));
    }

    // Update barang
    public function update(Request $request, $id)
    {
        // Cek apakah user adalah pegawai gudang
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai || !in_array($pegawai->id_role, [4, 1])) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda bukan pegawai gudang.');
        }

        $barang = BarangTitipan::findOrFail($id);

        // Define allowed categories
        $allowedCategories = [
            'Elektronik & Gadget',
            'Pakaian & Aksesori',
            'Perabotan Rumah Tangga',
            'Buku, Alat Tulis, & Peralatan Sekolah',
            'Hobi, Mainan, & Koleksi',
            'Perlengkapan Bayi & Anak',
            'Otomotif & Aksesori',
            'Perlengkapan Taman & Outdoor',
            'Peralatan Kantor & Industri',
            'Kosmetik & Perawatan Diri'
        ];

        $rules = [
            'nama_barang_titipan' => 'required|string|max:255',
            'harga_barang' => 'required|numeric',
            'jenis_barang' => ['required', Rule::in($allowedCategories)],
            'garansi_barang' => 'required|date',
            'berat_barang' => 'required|numeric',
            'status_barang' => 'required|string|in:dijual,terjual,sudah diambil penitip,sudah didonasikan,barang untuk donasi',
            'deskripsi_barang' => 'required|string',
            'gambar_barang' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gambar.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        $messages = [
            'jenis_barang.required' => 'Jenis barang harus dipilih.',
            'jenis_barang.in' => 'Jenis barang yang dipilih tidak valid.',
        ];

        try {
            $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Update validation failed for jenis_barang', [
                'submitted_value' => $request->jenis_barang,
                'allowed_values' => $allowedCategories,
                'errors' => $e->errors()
            ]);
            
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Validasi gagal. Periksa kembali data yang dimasukkan.');
        }

        // Rest of your update method...
        $updateData = $request->only([
            'nama_barang_titipan',
            'harga_barang',
            'jenis_barang',
            'berat_barang',
            'status_barang',
            'deskripsi_barang',
        ]);

        // Format tanggal garansi
        if ($request->garansi_barang) {
            $updateData['garansi_barang'] = Carbon::parse($request->garansi_barang)->format('Y-m-d');
        } else {
            $updateData['garansi_barang'] = null;
        }

        // Update field utama
        $barang->update($updateData);

        // Ganti gambar utama jika ada
        if ($request->hasFile('gambar_barang')) {
            // Hapus gambar lama
            if ($barang->gambar_barang && file_exists(storage_path('app/public/' . $barang->gambar_barang))) {
                \Storage::delete('public/' . $barang->gambar_barang);
            }

            // Upload baru
            $path = $request->file('gambar_barang')->store('gambar_barang_titipan', 'public');
            $barang->gambar_barang = $path;
            $barang->save();
        }

        // Simpan gambar tambahan baru jika ada
        if ($request->hasFile('gambar')) {
            foreach ($request->file('gambar') as $file) {
                $path = $file->store('gambar_barang_titipan', 'public');

                GambarBarangTitipan::create([
                    'id_barang' => $barang->id_barang,
                    'nama_file_gambar' => basename($path),
                ]);
            }
        }

        return redirect()->back()->with('success', 'Barang berhasil diperbarui.');
    }

    // Hapus barang
    public function destroy($id)
    {
        // Cek apakah user adalah pegawai gudang
        $pegawai = Auth::guard('pegawai')->user();
        if (!$pegawai || !in_array($pegawai->id_role, [4, 1])) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda bukan pegawai gudang.');
        }

        try {
            $barang = BarangTitipan::findOrFail($id);

            // Hapus gambar utama jika ada
            if ($barang->gambar_barang && Storage::disk('public')->exists($barang->gambar_barang)) {
                Storage::disk('public')->delete($barang->gambar_barang);
            }

            // Hapus gambar tambahan jika ada
            $gambarTambahan = GambarBarangTitipan::where('id_barang', $id)->get();
            foreach ($gambarTambahan as $gambar) {
                if (Storage::disk('public')->exists('gambar_barang_titipan/' . $gambar->nama_file_gambar)) {
                    Storage::disk('public')->delete('gambar_barang_titipan/' . $gambar->nama_file_gambar);
                }
                $gambar->delete();
            }

            // Hapus pencatatan pegawai gudang jika ada
            try {
                PencatatanPegawaiGudang::where('id_barang', $id)->delete();
            } catch (\Exception $e) {
                \Log::warning('Gagal menghapus pencatatan pegawai gudang: ' . $e->getMessage());
            }

            // Hapus barang
            $barang->delete();

            return redirect()->route('gudang.index')->with('success', 'Barang berhasil dihapus');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus barang: ' . $e->getMessage());
        }
    }
    
}

