<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Denda;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $transaksi = Transaksi::with('customer', 'detailTransaksi.alat', 'denda')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'selesai')
            ->get();

        $totalPendapatan = $transaksi->sum('total_harga');
        $totalDenda      = $transaksi->sum('total_denda');

        return view('admin.laporan.index', compact(
            'transaksi',
            'totalPendapatan',
            'totalDenda',
            'bulan',
            'tahun'
        ));
    }

    public function exportPdf(Request $request)
    {
        $bulan = $request->bulan ?? now()->month;
        $tahun = $request->tahun ?? now()->year;

        $transaksi = Transaksi::with('customer', 'detailTransaksi.alat', 'denda')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->where('status', 'selesai')
            ->get();

        $totalPendapatan = $transaksi->sum('total_harga');
        $totalDenda      = $transaksi->sum('total_denda');

        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'transaksi',
            'totalPendapatan',
            'totalDenda',
            'bulan',
            'tahun'
        ));

        return $pdf->download("laporan-sirental-{$bulan}-{$tahun}.pdf");
    }
}