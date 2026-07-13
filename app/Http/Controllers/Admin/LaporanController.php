<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        [$bulan, $tahun] = $this->filterPeriode($request);
        $laporan = $this->ambilLaporan($bulan, $tahun);

        return view('admin.laporan.index', array_merge(
            $laporan,
            compact('bulan', 'tahun')
        ));
    }

    public function exportPdf(Request $request)
    {
        [$bulan, $tahun] = $this->filterPeriode($request);
        $laporan = $this->ambilLaporan($bulan, $tahun);

        $pdf = Pdf::loadView(
            'admin.laporan.pdf',
            array_merge($laporan, compact('bulan', 'tahun'))
        );

        return $pdf->download("laporan-sirental-{$bulan}-{$tahun}.pdf");
    }

    private function filterPeriode(Request $request): array
    {
        $data = $request->validate([
            'bulan' => ['nullable', 'integer', 'between:1,12'],
            'tahun' => ['nullable', 'integer', 'min:2020', 'max:' . now()->year],
        ]);

        return [
            (int) ($data['bulan'] ?? now()->month),
            (int) ($data['tahun'] ?? now()->year),
        ];
    }

    private function ambilLaporan(int $bulan, int $tahun): array
    {
        $awalBulan = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $akhirBulan = $awalBulan->copy()->endOfMonth();

        // Untuk data baru, tanggal pelunasan berasal dari pembayaran denda
        // atau pembayaran sewa. updated_at menjadi fallback bagi data lama.
        $kolomTanggalLunas = DB::raw(
            'COALESCE(denda_dibayar_pada, dibayar_pada, updated_at)'
        );

        $transaksi = Transaksi::with([
                'customer',
                'detailTransaksi.alat',
                'denda',
            ])
            ->where('status', 'selesai')
            ->where('status_pembayaran', 'lunas')
            ->whereBetween($kolomTanggalLunas, [$awalBulan, $akhirBulan])
            ->orderByRaw(
                'COALESCE(denda_dibayar_pada, dibayar_pada, updated_at) DESC'
            )
            ->get();

        return [
            'transaksi' => $transaksi,
            'totalSewa' => (int) $transaksi->sum('total_harga'),
            'totalDenda' => (int) $transaksi->sum('total_denda'),
            'totalPendapatan' => (int) $transaksi->sum('total_dibayar'),
        ];
    }
}
