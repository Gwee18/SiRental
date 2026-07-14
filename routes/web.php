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

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::get('/katalog', [HomeController::class, 'katalog'])
    ->name('katalog.index');

Route::view('/syarat-ketentuan', 'pages.syarat-ketentuan')
    ->name('terms');

Route::view('/kebijakan-privasi', 'pages.kebijakan-privasi')
    ->name('privacy');

// =====================
// AUTENTIKASI CUSTOMER
// =====================

Route::middleware('guest')->group(function () {
    Route::get('/login', [
        AuthenticatedSessionController::class,
        'create',
    ])->name('login');

    Route::post('/login', [
        AuthenticatedSessionController::class,
        'sendOtp',
    ])->name('login.send-otp');

    Route::get('/login/verifikasi', [
        AuthenticatedSessionController::class,
        'showVerifyForm',
    ])->name('login.verify');

    Route::post('/login/verifikasi', [
        AuthenticatedSessionController::class,
        'verifyOtp',
    ])->name('login.verify.post');

    Route::post('/login/kirim-ulang', [
        AuthenticatedSessionController::class,
        'resendOtp',
    ])->name('login.resend');

    /*
     * SiRental menggunakan OTP untuk login sekaligus membuat akun.
     * Route GET ini dipertahankan agar tombol "Daftar" lama tetap
     * mengarah ke alur OTP yang benar.
     */
    Route::get('/register', function () {
        return redirect()->route('login');
    })->name('register');

    Route::get('/auth/google', [
        GoogleController::class,
        'redirect',
    ])->name('google.redirect');

    Route::get('/auth/google/callback', [
        GoogleController::class,
        'callback',
    ])->name('google.callback');
});

// =====================
// ROUTE CUSTOMER
// =====================

Route::middleware('customer')->group(function () {
    Route::post('/logout', [
        AuthenticatedSessionController::class,
        'destroy',
    ])->name('logout');

    Route::get('/rental', [
        RentalController::class,
        'index',
    ])->name('rental.index');

    Route::post('/rental', [
        RentalController::class,
        'store',
    ])->name('rental.store');

    Route::get('/transaksi', [
        CustomerTransaksiController::class,
        'index',
    ])->name('customer.transaksi.index');

    Route::get('/transaksi/{id}', [
        CustomerTransaksiController::class,
        'show',
    ])
        ->whereNumber('id')
        ->name('customer.transaksi.show');

    Route::get('/profil', [
        ProfilController::class,
        'index',
    ])->name('customer.profil');

    Route::put('/profil', [
        ProfilController::class,
        'update',
    ])->name('customer.profil.update');
});

// =====================
// ROUTE ADMIN
// =====================

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/login', [
            AdminLoginController::class,
            'showLoginForm',
        ])->name('login');

        Route::post('/login', [
            AdminLoginController::class,
            'login',
        ])->name('login.post');

        Route::post('/logout', [
            AdminLoginController::class,
            'logout',
        ])->name('logout');

        Route::middleware('admin')->group(function () {
            Route::get('/dashboard', [
                DashboardController::class,
                'index',
            ])->name('dashboard');

            // Alat memang mendukung tambah, edit, hapus, dan status.
            Route::patch('/alat/{id}/status', [
                AlatController::class,
                'toggleStatus',
            ])
                ->whereNumber('id')
                ->name('alat.toggle-status');

            Route::resource('alat', AlatController::class)
                ->except('show');

            /*
             * Transaksi admin hanya memiliki index, show, dan aksi
             * khusus. Route create/store/edit/update/destroy yang
             * sebelumnya dibuat resource tidak memiliki method
             * controller dan karena itu dihapus.
             */
            Route::get('/transaksi', [
                AdminTransaksiController::class,
                'index',
            ])->name('transaksi.index');

            Route::get('/transaksi/{id}', [
                AdminTransaksiController::class,
                'show',
            ])
                ->whereNumber('id')
                ->name('transaksi.show');

            Route::post('/transaksi/{id}/approve', [
                AdminTransaksiController::class,
                'approve',
            ])
                ->whereNumber('id')
                ->name('transaksi.approve');

            Route::post('/transaksi/{id}/tolak', [
                AdminTransaksiController::class,
                'tolak',
            ])
                ->whereNumber('id')
                ->name('transaksi.tolak');

            Route::post('/transaksi/{id}/selesai', [
                AdminTransaksiController::class,
                'selesai',
            ])
                ->whereNumber('id')
                ->name('transaksi.selesai');

            Route::get('/pengembalian', [
                AdminTransaksiController::class,
                'pengembalianIndex',
            ])->name('pengembalian.index');

            Route::post('/pengembalian/cari', [
                AdminTransaksiController::class,
                'cariPengembalian',
            ])->name('pengembalian.cari');

            Route::get('/pengembalian/{kode}', [
                AdminTransaksiController::class,
                'detailPengembalian',
            ])
                ->where('kode', '[A-Za-z0-9\-]+')
                ->name('pengembalian.detail');

            /*
             * Pelanggan hanya mendukung daftar, detail, dan hapus.
             * Route create/store/edit/update yang tidak mempunyai
             * method controller tidak lagi dibuat.
             */
            Route::get('/pelanggan', [
                PelangganController::class,
                'index',
            ])->name('pelanggan.index');

            Route::get('/pelanggan/{id}', [
                PelangganController::class,
                'show',
            ])
                ->whereNumber('id')
                ->name('pelanggan.show');

            Route::delete('/pelanggan/{id}', [
                PelangganController::class,
                'destroy',
            ])
                ->whereNumber('id')
                ->name('pelanggan.destroy');

            Route::get('/laporan', [
                LaporanController::class,
                'index',
            ])->name('laporan.index');

            Route::get('/laporan/pdf', [
                LaporanController::class,
                'exportPdf',
            ])->name('laporan.pdf');
        });
    });
