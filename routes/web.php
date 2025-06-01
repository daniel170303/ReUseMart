<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarangTitipanController;
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

Route::post('/diskusi/{id_barang}', [DiskusiProdukController::class, 'store'])->name('diskusi.store');

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
        Route::post('penitipan/perpanjang/{id_penitipan}', [PenitipanController::class, 'perpanjang'])->name('penitip.penitipan.perpanjang');
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
        Route::get('/penitip/search/{keyword}', [PenitipController::class, 'search'])->name('penitip.search');
        Route::post('/logout', function () {
            Auth::guard('pegawai')->logout();
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('success', 'Logout berhasil!');
        })->name('cs.logout');
    });

    //ROUTE OWNER
    Route::get('/owner', function () {
        return redirect()->route('owner.dashboard');
    });
    Route::get('/owner/dashboard', [PegawaiController::class, 'dashboardOwner'])->name('owner.dashboard');
    Route::prefix('owner')->name('owner.')->group(function () {
        Route::get('/profile/{id}', [OwnerController::class, 'profile'])->name('profile');
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

//Route PENITIP
Route::prefix('penitip')->group(function () {
    Route::get('/profile/{id}', [PenitipController::class, 'profileById'])->name('penitip.profile.id');
    Route::get('/{id}/barang-titipan', [PenitipController::class, 'barangTitipanPenitip'])->name('penitip.barangTitipan');
    Route::post('penitipan/perpanjang/{id_penitipan}', [PenitipanController::class, 'perpanjang'])->name('penitip.penitipan.perpanjang');
    Route::post('/jadwal-pengambilan', [PenitipController::class, 'jadwalPengambilan'])->name('penitip.jadwalPengambilan');
});

Route::get('/search-organisasi', function(Request $request) {
    return redirect()->route('admin.organisasi.index', ['search' => $request->query('q')]);
})->name('search.organisasi');

// Optional: API route for AJAX search
Route::get('/api/organisasi/search', [OrganisasiController::class, 'search'])->name('api.organisasi.search');


Route::prefix('admin')->group(function () {
    Route::resource('alamat-pembeli', AlamatPembeliController::class)->names('alamat');
});

Route::get('/pembeli/{id}/history', [PembeliController::class, 'history'])->name('pembeli.history');
Route::post('/rating', [RatingController::class, 'store'])->name('rating.store');