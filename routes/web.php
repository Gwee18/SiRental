<?php

use App\Http\Controllers\Admin\AlatController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\TransaksiController as AdminTransaksiController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\ProfilController;
use App\Http\Controllers\Customer\RentalController;
use App\Http\Controllers\Customer\TransaksiController as CustomerTransaksiController;
use Illuminate\Support\Facades\Route;

// =====================
// ROUTE PUBLIK
// =====================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [HomeController::class, 'katalog'])->name('katalog.index');

Route::get('/syarat-ketentuan', function () {
    return view('pages.syarat-ketentuan');
})->name('terms');

Route::get('/kebijakan-privasi', function () {
    return view('pages.kebijakan-privasi');
})->name('privacy');

// =====================
// ROUTE AUTH CUSTOMER
// =====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'sendOtp'])->name('login.send-otp');

    Route::get('/login/verifikasi', [AuthenticatedSessionController::class, 'showVerifyForm'])->name('login.verify');
    Route::post('/login/verifikasi', [AuthenticatedSessionController::class, 'verifyOtp'])->name('login.verify.post');
    Route::post('/login/kirim-ulang', [AuthenticatedSessionController::class, 'resendOtp'])->name('login.resend');

    Route::get('/register', function () {
        return redirect()->route('login');
    })->name('register');

    Route::post('/register', function () {
        return redirect()->route('login');
    });
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// =====================
// LOGIN GOOGLE
// =====================
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// =====================
// ROUTE CUSTOMER
// =====================
Route::middleware(['customer'])->group(function () {
    Route::get('/rental', [RentalController::class, 'index'])->name('rental.index');
    Route::post('/rental', [RentalController::class, 'store'])->name('rental.store');

    Route::get('/transaksi', [CustomerTransaksiController::class, 'index'])->name('customer.transaksi.index');

    Route::get('/transaksi-saya', [CustomerTransaksiController::class, 'index'])->name('customer.transaksi');

    Route::get('/transaksi/{id}', [CustomerTransaksiController::class, 'show'])->name('customer.transaksi.show');

    Route::get('/profil', [ProfilController::class, 'index'])->name('customer.profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('customer.profil.update');
});

// =====================
// ROUTE ADMIN
// =====================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::patch('/alat/{id}/status', [AlatController::class, 'toggleStatus'])
            ->name('alat.toggle-status');

        Route::resource('/alat', AlatController::class)
            ->except('show');

        Route::resource('/transaksi', AdminTransaksiController::class);
        Route::post('/transaksi/{id}/approve', [AdminTransaksiController::class, 'approve'])->name('transaksi.approve');
        Route::post('/transaksi/{id}/tolak', [AdminTransaksiController::class, 'tolak'])->name('transaksi.tolak');
        Route::post('/transaksi/{id}/selesai', [AdminTransaksiController::class, 'selesai'])->name('transaksi.selesai');

        Route::get('/pengembalian', [AdminTransaksiController::class, 'pengembalianIndex'])->name('pengembalian.index');
        Route::post('/pengembalian/cari', [AdminTransaksiController::class, 'cariPengembalian'])->name('pengembalian.cari');
        Route::get('/pengembalian/{kode}', [AdminTransaksiController::class, 'detailPengembalian'])->name('pengembalian.detail');

        Route::resource('/pelanggan', PelangganController::class);

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    });
});
