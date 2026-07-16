<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $notifikasi = [
                'adminNotifikasiTransaksiBaru' => 0,
                'adminNotifikasiPelangganBaru' => 0,
                'adminHasAnyNotification' => false,
            ];

            $admin = Auth::guard('admin')->user();

            if (! $admin) {
                $view->with($notifikasi);

                return;
            }

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

            $pelangganBaruQuery = Customer::query();

            if ($admin->last_seen_pelanggan_at) {
                $pelangganBaruQuery->where(
                    'created_at',
                    '>',
                    $admin->last_seen_pelanggan_at
                );
            }

            $jumlahPelangganBaru = $pelangganBaruQuery->count();

            if (request()->routeIs('admin.transaksi.index')) {
                $admin->forceFill([
                    'last_seen_transaksi_at' => now(),
                ])->saveQuietly();

                $jumlahTransaksiBaru = 0;
            }

            if (request()->routeIs('admin.pelanggan.index')) {
                $admin->forceFill([
                    'last_seen_pelanggan_at' => now(),
                ])->saveQuietly();

                $jumlahPelangganBaru = 0;
            }

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
