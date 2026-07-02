<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AlatController;
use App\Http\Controllers\Admin\TransaksiController as AdminTransaksiController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Customer\HomeController;
use App\Http\Controllers\Customer\RentalController;
use App\Http\Controllers\Customer\TransaksiController as CustomerTransaksiController;
use App\Http\Controllers\Customer\ProfilController;

// =====================
// ROUTE PUBLIK
// =====================
Route::get('/', [HomeController::class, 'index'])->name('home');

// =====================
// ROUTE AUTH CUSTOMER
// =====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'store']);
});

Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Login Google
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');

// =====================
// ROUTE VERIFIKASI EMAIL CUSTOMER
// =====================
Route::middleware(['customer'])->group(function () {

    Route::get('/verify-email', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('home')->with('status', 'Email berhasil diverifikasi!');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Link verifikasi baru sudah dikirim ke email kamu.');
    })->middleware('throttle:6,1')->name('verification.send');

});

// =====================
// ROUTE CUSTOMER
// =====================
Route::middleware(['customer', 'verified'])->group(function () {
    Route::get('/rental', [RentalController::class, 'index'])->name('rental.index');
    Route::post('/rental', [RentalController::class, 'store'])->name('rental.store');

    Route::get('/transaksi', [CustomerTransaksiController::class, 'index'])->name('customer.transaksi');
    Route::get('/transaksi', [CustomerTransaksiController::class, 'index'])->name('customer.transaksi.index');
    Route::get('/transaksi/{id}', [CustomerTransaksiController::class, 'show'])->name('customer.transaksi.show');

    Route::get('/profil', [ProfilController::class, 'index'])->name('customer.profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('customer.profil.update');
});

// =====================
// ROUTE ADMIN
// =====================
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth Admin
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

    // Protected Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('/alat', AlatController::class);

        Route::resource('/transaksi', AdminTransaksiController::class);
        Route::post('/transaksi/{id}/approve', [AdminTransaksiController::class, 'approve'])->name('transaksi.approve');
        Route::post('/transaksi/{id}/tolak', [AdminTransaksiController::class, 'tolak'])->name('transaksi.tolak');
        Route::post('/transaksi/{id}/selesai', [AdminTransaksiController::class, 'selesai'])->name('transaksi.selesai');

        // Verifikasi Pengembalian
        Route::get('/pengembalian', [AdminTransaksiController::class, 'pengembalianIndex'])->name('pengembalian.index');
        Route::post('/pengembalian/cari', [AdminTransaksiController::class, 'cariPengembalian'])->name('pengembalian.cari');
        Route::get('/pengembalian/{kode}', [AdminTransaksiController::class, 'detailPengembalian'])->name('pengembalian.detail');

        Route::resource('/pelanggan', PelangganController::class);

        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    });
});