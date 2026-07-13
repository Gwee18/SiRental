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

        // Seluruh uang yang benar-benar sudah diterima, termasuk biaya sewa
        // transaksi aktif dan denda transaksi yang sudah selesai.
        $totalPendapatan = (int) Transaksi::whereIn(
            'status_pembayaran',
            ['sewa_lunas', 'lunas']
        )->sum('total_dibayar');

        // Denda hanya dianggap terkumpul setelah transaksi selesai dan lunas.
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
