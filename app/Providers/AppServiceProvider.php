<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $notifikasi = [
                'adminNotifikasiTransaksiBaru' => 0,
                'adminNotifikasiPelangganBaru' => 0,
                'adminHasAnyNotification' => false,
            ];

            $admin = Auth::guard('admin')->user();

            /*
            |--------------------------------------------------------------------------
            | Jika admin belum login
            |--------------------------------------------------------------------------
            */

            if (! $admin) {
                $view->with($notifikasi);

                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Hitung pesanan baru
            |--------------------------------------------------------------------------
            |
            | Pesanan baru hanya menghitung transaksi dengan status menunggu yang
            | dibuat setelah admin terakhir membuka halaman transaksi.
            |
            */

            $transaksiBaruQuery = Transaksi::query()
                ->where('status', 'menunggu');

            if ($admin->last_seen_transaksi_at) {
                $transaksiBaruQuery->where(
                    'created_at',
                    '>',
                    $admin->last_seen_transaksi_at
                );
            }

            $jumlahTransaksiBaru = $transaksiBaruQuery->count();

            /*
            |--------------------------------------------------------------------------
            | Hitung pelanggan baru
            |--------------------------------------------------------------------------
            |
            | Pelanggan baru dihitung berdasarkan akun customer yang dibuat setelah
            | admin terakhir membuka halaman pelanggan.
            |
            */

            $pelangganBaruQuery = Customer::query();

            if ($admin->last_seen_pelanggan_at) {
                $pelangganBaruQuery->where(
                    'created_at',
                    '>',
                    $admin->last_seen_pelanggan_at
                );
            }

            $jumlahPelangganBaru = $pelangganBaruQuery->count();

            /*
            |--------------------------------------------------------------------------
            | Tandai pesanan sudah dilihat
            |--------------------------------------------------------------------------
            |
            | Ketika admin membuka halaman Konfirmasi Peminjaman, waktu terakhir
            | dilihat diperbarui dan titik merah langsung dihilangkan.
            |
            */

            if (request()->routeIs('admin.transaksi.index')) {
                $admin->forceFill([
                    'last_seen_transaksi_at' => now(),
                ])->saveQuietly();

                $jumlahTransaksiBaru = 0;
            }

            /*
            |--------------------------------------------------------------------------
            | Tandai pelanggan sudah dilihat
            |--------------------------------------------------------------------------
            */

            if (request()->routeIs('admin.pelanggan.index')) {
                $admin->forceFill([
                    'last_seen_pelanggan_at' => now(),
                ])->saveQuietly();

                $jumlahPelangganBaru = 0;
            }

            /*
            |--------------------------------------------------------------------------
            | Bagikan data notifikasi ke layout admin
            |--------------------------------------------------------------------------
            */

            $notifikasi = [
                'adminNotifikasiTransaksiBaru' => $jumlahTransaksiBaru,
                'adminNotifikasiPelangganBaru' => $jumlahPelangganBaru,
                'adminHasAnyNotification' => (
                    $jumlahTransaksiBaru > 0 ||
                    $jumlahPelangganBaru > 0
                ),
            ];

            $view->with($notifikasi);
        });
    }
}
