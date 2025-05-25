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

use App\Http\Controllers\ProfilePembeliController;

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




Route::prefix('admin/organisasi')->name('admin.organisasi.')->group(function () {
    Route::get('/', [OrganisasiController::class, 'index'])->name('index');
    Route::get('/create', [OrganisasiController::class, 'create'])->name('create');
    Route::post('/store', [OrganisasiController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [OrganisasiController::class, 'edit'])->name('edit');
    Route::get('/{id}', [OrganisasiController::class, 'show'])->name('show'); // ← ini tambahan
    Route::put('/{id}', [OrganisasiController::class, 'update'])->name('update');
    Route::delete('/{id}', [OrganisasiController::class, 'destroy'])->name('destroy');
});

// routes/web.php



// Organization management routes without auth middleware
Route::prefix('admin')->name('admin.')->group(function () {
    // List all organizations with search
    Route::get('/organisasi', [OrganisasiController::class, 'index'])->name('organisasi.index');
    
    // Show form to create a new organization
    Route::get('/organisasi/create', [OrganisasiController::class, 'create'])->name('organisasi.create');
    
    // Store a new organization
    Route::post('/organisasi', [OrganisasiController::class, 'store'])->name('organisasi.store');
    
    // Show organization details
    Route::get('/organisasi/{id}', [OrganisasiController::class, 'show'])->name('organisasi.show');
    
    // Show form to edit an organization
    Route::get('/organisasi/{id}/edit', [OrganisasiController::class, 'edit'])->name('organisasi.edit');
    
    // Update an organization
    Route::put('/organisasi/{id}', [OrganisasiController::class, 'update'])->name('organisasi.update');
    
    // Delete an organization
    Route::delete('/organisasi/{id}', [OrganisasiController::class, 'destroy'])->name('organisasi.destroy');
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
    Route::resource('/penitip', PembeliController::class)->names([
        'index'   => 'penitip.index',
        'create'  => 'penitip.create',
        'store'   => 'penitip.store',
        'show'    => 'penitip.show',
        'edit'    => 'penitip.edit',
        'update'  => 'penitip.update',
        'destroy' => 'penitip.destroy',
    ]);

    // Barang Titipan
    Route::get('/barang-titipan', [BarangTitipanController::class, 'index'])->name('barang.index');
    Route::get('/barang-titipan/{id}', [BarangTitipanController::class, 'show'])->name('barang.show');
    Route::get('/barang-titipan/{id}/edit', [BarangTitipanController::class, 'edit'])->name('barang.edit');
    Route::put('/barang-titipan/{id}', [BarangTitipanController::class, 'update'])->name('barang.update');
    Route::delete('/barang-titipan/{id}', [BarangTitipanController::class, 'destroy'])->name('barang.destroy');
});

// You can also add a direct search route for convenience
Route::get('/search-organisasi', function(\Illuminate\Http\Request $request) {
    return redirect()->route('admin.organisasi.index', ['search' => $request->query('q')]);
})->name('search.organisasi');

// Optional: API route for AJAX search
Route::get('/api/organisasi/search', [OrganisasiController::class, 'search'])->name('api.organisasi.search');


Route::prefix('admin')->group(function () {
    Route::resource('alamat-pembeli', AlamatPembeliController::class)->names('alamat');
});

