<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangTitipanController;
use App\Http\Controllers\HunterController;
use App\Http\Controllers\AuthController;
use App\Models\BarangTitipan;
use App\Http\Controllers\DiskusiProdukController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrganisasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\PembeliController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\PenitipanController;
use App\Http\Controllers\ProfilePembeliController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

Route::get('/profil-pembeli', [ProfilePembeliController::class, 'index'])->name('profil.pembeli');
Route::get('/profil-pembeli/{id}', [ProfilePembeliController::class, 'show'])->name('profil.pembeli.show');



Route::resource('pembelis', PembeliController::class);


use App\Http\Controllers\AlamatPembeliController;

// Routes untuk Alamat Pembeli (tanpa auth protection)
Route::get('/alamat', [AlamatPembeliController::class, 'index'])->name('alamat.index');
Route::get('/alamat/create', [AlamatPembeliController::class, 'create'])->name('alamat.create');
Route::post('/alamat', [AlamatPembeliController::class, 'store'])->name('alamat.store');
Route::get('/alamat/{id}/edit', [AlamatPembeliController::class, 'edit'])->name('alamat.edit');
Route::put('/alamat/{id}', [AlamatPembeliController::class, 'update'])->name('alamat.update');
Route::delete('/alamat/{id}', [AlamatPembeliController::class, 'destroy'])->name('alamat.destroy');
Route::post('/alamat/{id}/main', [AlamatPembeliController::class, 'setAsMain'])->name('alamat.set-main');
Route::get('/alamat/search', [AlamatPembeliController::class, 'search'])->name('alamat.search');

Route::get('/', function () {
    $barangTitipan = BarangTitipan::all();
    return view('landingPage.landingPage', compact('barangTitipan'));
});

Route::get('/admin', function () {
    return view('admin');
})->name('admin');

Route::get('/barang/{id}', [BarangTitipanController::class, 'showDetail'])->name('barang.show');

// Route untuk menampilkan form
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/register', function () {
    return view('register.register');
})->name('register');
Route::get('/register/pembeli', function () {
    return view('register.registerPembeli');
})->name('register.pembeli.form');
Route::get('/register/organisasi', function () {
    return view('register.registerOrganisasi');
})->name('register.organisasi.form');

// Route untuk proses registrasi
Route::post('/register/pembeli', [LoginController::class, 'registerPembeli'])->name('register.pembeli.submit');
Route::post('/register/organisasi', [LoginController::class, 'registerOrganisasi'])->name('register.organisasi.submit');

// Route untuk proses login (gunakan LoginController)
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

// Route untuk logout (gunakan LoginController)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route dengan middleware multiauth
Route::middleware(['multiauth'])->group(function () {

    // ROUTE PEMBELI
    Route::get('/pembeli', [PembeliController::class, 'dashboardPembeli'])->name('pembeli.dashboard');
    Route::prefix('pembeli')->group(function () {
        Route::get('/pembeli/profile', [PembeliController::class, 'dashboardPembeli'])->name('pembeli.profilePembeli');
        
        // Profile dan history
        Route::post('/rating', [RatingController::class, 'store'])->name('rating.store');
        Route::post('/pembeli/update-alamat', [PembeliController::class, 'updateAlamat'])->name('pembeli.updateAlamat');
        
        // Cart routes (PERBAIKAN UTAMA - gunakan middleware auth:pembeli)
        Route::get('/keranjang', [CartController::class, 'index'])->name('keranjang');
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index'); // alias
        Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
        Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
        
        // Checkout routes
        Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
        
        // Transaksi routes
        Route::get('/history', [TransaksiController::class, 'history'])->name('pembeli.history');
        Route::get('/transaksi/{id}', [TransaksiController::class, 'showDetail'])->name('transaksi.detail');
        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
        Route::post('/transaksi/{id}/upload-bukti', [TransaksiController::class, 'uploadBuktiPembayaran'])->name('transaksi.upload-bukti');
        
        // Diskusi produk
        Route::post('/diskusi/{id_barang}', [DiskusiProdukController::class, 'store'])->name('diskusi.store');
    });
    
    //PENITIP
    Route::get('/penitip', function () {
        return redirect()->route('penitip.dashboard');
    });
    Route::get('/penitip/profile', function () {
        $id = session('user_id');
        return redirect()->route('penitip.profile.id', ['id' => $id]);
    });
    Route::get('/penitip/dashboard', function () {
        return view('penitip.dashboardPenitip');
    })->name('penitip.dashboard');
    Route::prefix('penitip')->group(function () {
        Route::get('/profile/{id}', [PenitipController::class, 'profileById'])->name('penitip.profile.id');
        Route::get('/{id}/barang-titipan', [PenitipController::class, 'barangTitipanPenitip'])->name('penitip.barangTitipan');
        Route::post('/perpanjang-penitipan', [PenitipanController::class, 'perpanjangPenitipan'])->name('penitip.perpanjang');
        Route::post('/jadwal-pengambilan', [PenitipController::class, 'jadwalPengambilan'])->name('penitip.jadwalPengambilan');
    });
    Route::post('/penitipan/perpanjang', [PenitipanController::class, 'perpanjang'])->name('penitipan.perpanjang');

    //CUSTOMER SERVICE
    Route::get('/cs', function () {
        return redirect()->route('cs.dashboard');
    });
    Route::get('/cs/dashboard', [PegawaiController::class, 'dashboardCs'])->name('cs.dashboard');
    Route::prefix('cs')->middleware('auth:pegawai')->group(function () {
        Route::get('/penitip', [PenitipController::class, 'index'])->name('cs.penitip');
        Route::post('/penitip', [PenitipController::class, 'store'])->name('penitip.store');
        Route::get('/penitip/{id}', [PenitipController::class, 'show'])->name('penitip.show');
        Route::put('/penitip/{id}', [PenitipController::class, 'update'])->name('penitip.update');
        Route::delete('/penitip/{id}', [PenitipController::class, 'destroy'])->name('penitip.destroy');
        Route::get('/penitip/search/{keyword?}', [PenitipController::class, 'search'])->name('penitip.search');
        Route::post('/logout', function () {
            Auth::guard('pegawai')->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('success', 'Logout berhasil!');
        })->name('cs.logout');
    });

    //PEGAWAI GUDANG - Pindahkan route gudang ke sini
    Route::get('/gudang', function () {
        return redirect()->route('gudang.dashboard');
    });
    Route::get('/gudang/dashboard', [PegawaiController::class, 'dashboardGudang'])->name('gudang.dashboard');
    Route::prefix('gudang')->name('gudang.')->middleware('auth:pegawai')->group(function () {
        Route::get('/', [BarangTitipanController::class, 'index'])->name('index');
        Route::post('/', [BarangTitipanController::class, 'store'])->name('store');
        Route::get('/barang/{id}/detail', [BarangTitipanController::class, 'showDetail'])->name('barang.showDetail');
        Route::put('/barang/{id}', [BarangTitipanController::class, 'update'])->name('update');
        Route::delete('/destroy/{id}', [BarangTitipanController::class, 'destroy'])->name('destroy');
        //Route::post('/penitipan/jadwalkan-pengiriman', [PenitipanController::class, 'jadwalkanPengiriman'])->name('penitipan.jadwalkanPengiriman');
        Route::get('/jadwal-pengembalian', [PenitipanController::class, 'halamanJadwalPengembalian'])->name('jadwalPengembalian');
        Route::post('/konfirmasi-pengembalian', [PenitipanController::class, 'konfirmasiPengembalian'])->name('konfirmasiPengembalian');
        Route::get('/jadwal-pengiriman', [PenitipanController::class, 'jadwalPengiriman'])->name('jadwalPengiriman');
        Route::post('/jadwal-pengiriman/proses', [PenitipanController::class, 'prosesJadwalkanPengiriman'])->name('penitipan.prosesJadwalkanPengiriman');
        Route::get('/transaksi/{id}/cetak-pdfJual', [TransaksiController::class, 'cetakPDF'])->name('transaksi.cetakPDF');
        Route::get('/transaksi/{id}/cetak-pdfAmbil', [TransaksiController::class, 'cetakPDFAmbil'])->name('transaksi.cetakPDFAmbil');
        Route::get('/konfirmasi-pengambilan', [PenitipanController::class, 'indexKonfirmasiPengambilan'])->name('konfirmasiPengambilan');
        Route::put('/konfirmasi-pengambilan/{id_transaksi}', [PenitipanController::class, 'konfirmasiPengambilan'])->name('pengambilan.konfirmasi');
        // Menampilkan halaman transaksi sederhana (Siap Diambil & Selesai)
        Route::get('/transaksi-sederhana', [PenitipanController::class, 'viewTransaksiSederhana'])->name('listTransaksi');

        // Konfirmasi pengambilan: dari 'Siap Diambil' menjadi 'Selesai'
        Route::post('/transaksi/{id_transaksi}/konfirmasi', [PenitipanController::class, 'konfirmasiPengambilan'])->name('gudang.transaksi.konfirmasiPengambilan');
        
        // Tambahan logout untuk pegawai gudang
        Route::post('/logout', function () {
            Auth::guard('pegawai')->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('success', 'Logout berhasil!');
        })->name('gudang.logout');
    });

    //ROUTE OWNER
    Route::get('/owner', function () {
        return redirect()->route('owner.dashboard');
    });
    Route::get('/owner/dashboard', [PegawaiController::class, 'dashboardOwner'])->name('owner.dashboard');
    Route::prefix('owner')->name('owner.')->middleware('auth:pegawai')->group(function () {
        Route::get('/profile/{id}', [OwnerController::class, 'profile'])->name('profile');
        Route::get('/laporan-penjualan', [OwnerController::class, 'laporanPenjualan'])->name('laporanPenjualan');
        Route::get('/laporan-penjualan-pdf', [OwnerController::class, 'laporanPenjualanPDF'])->name('laporanPenjualanPDF');
        Route::get('/laporan-komisi', [OwnerController::class, 'laporanKomisi'])->name('laporanKomisi');
        Route::get('/laporan-komisi-pdf', [OwnerController::class, 'laporanKomisiPDF'])->name('laporanKomisiPDF');
        Route::get('/laporan-stok-gudang', [OwnerController::class, 'laporanStokGudang'])->name('laporanStokGudang');
        Route::get('/laporan-stok-gudang-pdf', [OwnerController::class, 'laporanStokGudangPDF'])->name('laporanStokGudangPDF');
        Route::get('/laporan-penjualan-per-kategori', [OwnerController::class, 'laporanPenjualanPerKategori'])->name('laporanPenjualanPerKategori');
        Route::get('/laporan-penjualan-per-kategori-pdf', [OwnerController::class, 'laporanPenjualanPerKategoriPDF'])->name('laporanPenjualanPerKategoriPDF');
        Route::get('/laporan-masa-penitipan-habis', [OwnerController::class, 'laporanMasaPenitipanHabis'])->name('laporanMasaPenitipanHabis');
        Route::get('/laporan-masa-penitipan-habis-pdf', [OwnerController::class, 'laporanMasaPenitipanHabisPDF'])->name('laporanMasaPenitipanHabisPDF');
        Route::get('/laporan-komisi-per-hunter', [OwnerController::class, 'laporanKomisiPerHunter'])->name('laporanKomisiPerHunter');
        Route::get('/laporan-komisi-per-hunter-pdf', [OwnerController::class, 'laporanKomisiPerHunterPDF'])->name('laporanKomisiPerHunterPDF');
        // Routes untuk Donasi
        Route::get('/barang-donasi', [OwnerController::class, 'barangDonasi'])->name('barang.donasi');
        Route::post('/request/{id}/terima', [OwnerController::class, 'terimaRequest'])->name('request.terima');
        Route::post('/request/{id}/tolak', [OwnerController::class, 'tolakRequest'])->name('request.tolak');
        Route::post('/donasi/konfirmasi', [OwnerController::class, 'konfirmasiDonasi'])->name('donasi.konfirmasi');
        Route::put('/donasi/{id}/edit', [OwnerController::class, 'editDonasi'])->name('donasi.edit');
        Route::delete('/donasi/{id}/hapus', [OwnerController::class, 'hapusDonasi'])->name('donasi.hapus');

        Route::post('/logout', function () {
            Auth::guard('pegawai')->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('success', 'Logout berhasil!');
        })->name('owner.logout');
    });

    //HUNTER
    Route::get('/hunter', function () {
        return redirect()->route('hunter.dashboard');
    });
    Route::get('/hunter/dashboard', function () {
        return view('pegawai.hunter.dashboardHunter');
    })->name('hunter.dashboard');
    Route::prefix('hunter')->name('hunter.')->group(function () {
        Route::get('/profile/{id}', [HunterController::class, 'profile'])->name('profile');
        Route::post('/logout', function () {
            Auth::guard('pegawai')->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('success', 'Logout berhasil!');
        })->name('hunter.logout');
    });

    //ROUTE ADMIN
    Route::get('/admin', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/create', [PegawaiController::class, 'create'])->name('pegawai.create');
        Route::post('/pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
        Route::get('/pegawai/{id}', [PegawaiController::class, 'show'])->name('pegawai.show');
        Route::get('/pegawai/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
        Route::put('/pegawai/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
        Route::delete('/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');
        Route::get('/pegawai/search/{keyword}', [PegawaiController::class, 'search'])->name('pegawai.search');
        Route::post('/logout', function () {
            Auth::guard('pegawai')->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('success', 'Logout berhasil!');
        })->name('admin.logout');
    });

    //ROUTE ORGANISASI
    Route::get('/organisasi', function () {
        return redirect()->route('organisasi.dashboard');
    });
    Route::get('/organisasi/dashboard', [OrganisasiController::class, 'dashboard'])->name('organisasi.dashboard');
    Route::prefix('organisasi')->name('organisasi.')->group(function () {
        // Profile
        Route::get('/profile', [OrganisasiController::class, 'profile'])->name('profile');
        Route::put('/profile', [OrganisasiController::class, 'updateProfile'])->name('profile.update');
        
        // Request Barang Routes
        Route::prefix('request-barang')->name('requestBarang.')->group(function () {
            Route::get('/', [RequestController::class, 'index'])->name('index');
            Route::get('/search', [RequestController::class, 'search'])->name('search');
            Route::post('/', [RequestController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [RequestController::class, 'edit'])->name('edit');
            Route::put('/{id}', [RequestController::class, 'update'])->name('update');
            Route::delete('/{id}', [RequestController::class, 'destroy'])->name('destroy');
            Route::get('/create', [RequestController::class, 'create'])->name('create');
        });
        Route::post('/logout', function () {
            Auth::guard('pegawai')->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('success', 'Logout berhasil!');
        })->name('organisasi.logout');
    });
});


Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');

    // Organisasi
    Route::get('/organisasi', [OrganisasiController::class, 'index'])->name('organisasi.index');
    Route::get('/organisasi/create', [OrganisasiController::class, 'create'])->name('organisasi.create');
    Route::post('/organisasi', [OrganisasiController::class, 'store'])->name('organisasi.store');
    Route::get('/organisasi/{id}', [OrganisasiController::class, 'show'])->name('organisasi.show');
    Route::get('/organisasi/{id}/edit', [OrganisasiController::class, 'edit'])->name('organisasi.edit');
    Route::put('/organisasi/{id}', [OrganisasiController::class, 'update'])->name('organisasi.update');
    Route::delete('/organisasi/{id}', [OrganisasiController::class, 'destroy'])->name('organisasi.destroy');

    // Pegawai
    Route::resource('/pegawai', PegawaiController::class)->names([
        'index'   => 'pegawai.index',
        'create'  => 'pegawai.create',
        'store'   => 'pegawai.store',
        'edit'    => 'pegawai.edit',
        'update'  => 'pegawai.update',
        'destroy' => 'pegawai.destroy',
    ]);

    // Penitip (gunakan resource untuk pembeli)
    Route::resource('/penitip', PenitipController::class)->names([
        'index'   => 'penitip.index',
        'create'  => 'penitip.create',
        'store'   => 'penitip.store',
        'show'    => 'penitip.show',
        'edit'    => 'penitip.edit',
        'update'  => 'penitip.update',
        'destroy' => 'penitip.destroy',
    ]);
});

Route::prefix('gudang')->name('gudang.')->group(function () {
    Route::get('/', [BarangTitipanController::class, 'index'])->name('index');
    Route::post('/', [BarangTitipanController::class, 'store'])->name('store');
    Route::get('/barang/{id}/detail', [BarangTitipanController::class, 'showDetail'])->name('barang.showDetail');
    //Route::get('/edit/{id}', [BarangTitipanController::class, 'edit'])->name('edit');
    Route::put('/barang/{id}', [BarangTitipanController::class, 'update'])->name('update');
    Route::delete('/destroy/{id}', [BarangTitipanController::class, 'destroy'])->name('destroy');
    Route::post('/penitipan/jadwalkan-pengiriman', [PenitipanController::class, 'jadwalkanPengiriman'])->name('penitipan.jadwalkanPengiriman');
});

//download Nota
Route::get('/nota/{filename}', function ($filename) {
    $path = storage_path('app/public/nota/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->name('nota.download');

// Penitipan Routes (Tanpa middleware dan resource controller lengkap)
Route::prefix('penitipan')->name('penitipan.')->group(function () {
    Route::get('/', [PenitipanController::class, 'index'])->name('index');
    Route::get('/create', [PenitipanController::class, 'create'])->name('create');
    Route::post('/', [PenitipanController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [PenitipanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PenitipanController::class, 'update'])->name('update');
    Route::delete('/{id}', [PenitipanController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/konfirmasi-pengambilan', [PenitipanController::class, 'konfirmasiPengambilan'])
        ->name('konfirmasiPengambilan');
    Route::get('/{id}/download', [PenitipanController::class, 'downloadNota'])->name('download');
    Route::post('/perpanjang', [PenitipanController::class, 'perpanjangPenitipan'])->name('perpanjang');
});

// Detail Penitipan Routes
Route::prefix('detail-penitipan')->name('detail-penitipan.')->group(function () {
    Route::get('/', [DetailPenitipanController::class, 'index'])->name('index');
    Route::get('/create', [DetailPenitipanController::class, 'create'])->name('create');
    Route::post('/', [DetailPenitipanController::class, 'store'])->name('store');
    Route::delete('/{id_barang}/{id_penitipan}', [DetailPenitipanController::class, 'destroy'])->name('destroy');
});

Route::prefix('pegawai')->name('pegawai.')->group(function () {
    Route::get('/gudang', [BarangTitipanController::class, 'index'])->name('gudang');
});

Route::get('/search-organisasi', function(Request $request) {
    return redirect()->route('admin.organisasi.index', ['search' => $request->query('q')]);
})->name('search.organisasi');

// Optional: API route for AJAX search
Route::get('/api/organisasi/search', [OrganisasiController::class, 'search'])->name('api.organisasi.search');


Route::prefix('admin')->group(function () {
    Route::resource('alamat-pembeli', AlamatPembeliController::class)->names('alamat');
});