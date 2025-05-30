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
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PenitipController;
use App\Http\Controllers\PenitipanController;
use App\Http\Controllers\ProfilePembeliController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

Route::get('/profil-pembeli', [ProfilePembeliController::class, 'index'])->name('profil.pembeli');
Route::get('/profil-pembeli/{id}', [ProfilePembeliController::class, 'show'])->name('profil.pembeli.show');




use App\Http\Controllers\PembeliController;

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






// // Menggunakan middleware api dan menambahkan prefix 'api'
// Route::prefix('api')->middleware('api')->group(function () {

Route::get('/', function () {
    $barangTitipan = BarangTitipan::take(3)->get();
    return view('landingPage.landingPage', compact('barangTitipan'));
});

Route::get('/login', function () {
    return view('login.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

// Tampilkan halaman register
Route::get('/register', function () {
    return view('register.register');
})->name('register');

Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');

// Proses data register
Route::post('/register', [AuthController::class, 'apiRegister']);


Route::get('/admin', function () {
    return view('admin');
})->name('admin');

Route::get('/barang/{id}', [BarangTitipanController::class, 'showDetail'])->name('barang.show');

Route::post('/diskusi/{id_barang}', [DiskusiProdukController::class, 'store'])->name('diskusi.store');

// Route untuk reset password pegawai (hanya admin yang bisa akses)
Route::middleware(['auth:sanctum', 'can:manage-pegawai'])->post(
    '/admin/pegawai/{id}/reset-password', 
    [PegawaiController::class, 'resetPasswordToBirthDate']
)->name('pegawai.reset-password');


// Routes untuk reset password pembeli
Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])
    ->name('password.request');
    
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])
    ->name('password.email');
    
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])
    ->name('password.reset');
    
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
    ->name('password.update');


    // Authentication routes
Route::get('/login', function () {
    return view('login.login');
})->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard routes - protected by auth middleware
// Route::middleware(['auth'])->group(function () {
    // Profile routes for all authenticated users
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Admin routes
    Route::prefix('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
        Route::get('/transactions', [AdminController::class, 'transactions'])->name('admin.transactions');
        // Other admin routes...
    });
    
    // Pembeli routes
    Route::prefix('pembeli')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'pembeliDashboard'])->name('pembeli.dashboard');
        Route::get('/products', [BarangTitipanController::class, 'index'])->name('pembeli.products');
        Route::get('/transactions', [TransaksiController::class, 'myTransactions'])->name('pembeli.transactions');
        // Other pembeli routes...
    });
    
    // Other role dashboards...
    Route::get('/pegawai/dashboard', [DashboardController::class, 'pegawaiDashboard'])->name('pegawai.dashboard');
    Route::get('/owner/dashboard', [DashboardController::class, 'ownerDashboard'])->name('owner.dashboard');
    Route::get('/penitip/dashboard', [DashboardController::class, 'penitipDashboard'])->name('penitip.dashboard');
    Route::get('/organisasi/dashboard', [DashboardController::class, 'organisasiDashboard'])->name('organisasi.dashboard');
// });

// routes/web.php



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

Route::prefix('penitip')->group(function () {
    Route::get('/profile/{id}', [PenitipController::class, 'profileById'])->name('penitip.profile.id');
    Route::get('/{id}/barang-titipan', [PenitipController::class, 'barangTitipanPenitip'])->name('penitip.barangTitipan');
    Route::post('penitipan/perpanjang/{id_penitipan}', [PenitipanController::class, 'perpanjang'])->name('penitip.penitipan.perpanjang');
    Route::post('/jadwal-pengambilan', [PenitipController::class, 'jadwalPengambilan'])->name('penitip.jadwalPengambilan');
});


Route::get('/search-organisasi', function(\Illuminate\Http\Request $request) {
    return redirect()->route('admin.organisasi.index', ['search' => $request->query('q')]);
})->name('search.organisasi');

// Optional: API route for AJAX search
Route::get('/api/organisasi/search', [OrganisasiController::class, 'search'])->name('api.organisasi.search');


Route::prefix('admin')->group(function () {
    Route::resource('alamat-pembeli', AlamatPembeliController::class)->names('alamat');
});

