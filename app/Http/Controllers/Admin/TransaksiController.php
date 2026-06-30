<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Denda;
use App\Models\Alat;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::with('customer', 'detailTransaksi.alat')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function show($id)
    {
        $transaksi = Transaksi::with('customer', 'detailTransaksi.alat', 'denda')
            ->findOrFail($id);

        return view('admin.transaksi.detail', compact('transaksi'));
    }

    public function approve($id)
    {
        $transaksi = Transaksi::findOrFail($id);
        $transaksi->update([
            'status'         => 'aktif',
            'tanggal_mulai'  => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(
                $transaksi->detailTransaksi->first()->lama_sewa
            )->toDateString(),
        ]);

        return redirect()->route('admin.transaksi.show', $id)
            ->with('success', 'Pesanan berhasil disetujui!');
    }

    public function tolak($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        // Kembalikan stok
        foreach ($transaksi->detailTransaksi as $detail) {
            $detail->alat->increment('stok_tersedia', $detail->jumlah);
        }

        $transaksi->update(['status' => 'ditolak']);

        return redirect()->route('admin.transaksi.show', $id)
            ->with('success', 'Pesanan berhasil ditolak!');
    }

    public function selesai(Request $request, $id)
    {
        $transaksi = Transaksi::with('detailTransaksi.alat')->findOrFail($id);

        // Hitung denda jika terlambat
        $tanggalSelesai = $transaksi->tanggal_selesai;
        $hariIni        = now()->toDateString();
        $hariTerlambat  = 0;

        if ($hariIni > $tanggalSelesai) {
            $hariTerlambat = now()->diffInDays($tanggalSelesai);
            $dendaPerHari  = $transaksi->total_harga * 0.1;
            $totalDenda    = $hariTerlambat * $dendaPerHari;

            Denda::create([
                'transaksi_id'  => $transaksi->id,
                'hari_terlambat' => $hariTerlambat,
                'denda_per_hari' => $dendaPerHari,
                'total_denda'   => $totalDenda,
                'keterangan'    => "Terlambat {$hariTerlambat} hari",
            ]);

            $transaksi->update([
                'status'      => 'selesai',
                'total_denda' => $totalDenda,
            ]);
        } else {
            $transaksi->update(['status' => 'selesai']);
        }

        // Kembalikan stok
        foreach ($transaksi->detailTransaksi as $detail) {
            $detail->alat->increment('stok_tersedia', $detail->jumlah);
        }

        return redirect()->route('admin.transaksi.show', $id)
            ->with('success', 'Transaksi selesai!');
    }
}