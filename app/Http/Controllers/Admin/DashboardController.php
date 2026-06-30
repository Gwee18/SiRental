<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Customer;
use App\Models\Alat;
use App\Models\Denda;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCustomer    = Customer::count();
        $totalAlat        = Alat::count();
        $transaksiMenunggu = Transaksi::where('status', 'menunggu')->count();
        $transaksiAktif   = Transaksi::where('status', 'aktif')->count();
        $totalPendapatan  = Transaksi::where('status', 'selesai')->sum('total_harga');
        $totalDenda       = Denda::sum('total_denda');
        $transaksiTerbaru = Transaksi::with('customer')
                            ->orderBy('created_at', 'desc')
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