<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Customer;
use App\Models\Transaksi;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCustomer = Customer::count();
        $totalAlat = Alat::count();

        $transaksiMenunggu = Transaksi::where('status', 'menunggu')->count();
        $transaksiAktif = Transaksi::where('status', 'aktif')->count();

        $totalPendapatan = (int) Transaksi::whereIn(
            'status_pembayaran',
            ['sewa_lunas', 'lunas']
        )->sum('total_dibayar');

        $totalDenda = (int) Transaksi::where('status', 'selesai')
            ->where('status_pembayaran', 'lunas')
            ->sum('total_denda');

        $transaksiTerbaru = Transaksi::with('customer')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalCustomer',
            'totalAlat',
            'transaksiMenunggu',
            'transaksiAktif',
            'totalPendapatan',
            'totalDenda',
            'transaksiTerbaru'
        ));
    }
}
