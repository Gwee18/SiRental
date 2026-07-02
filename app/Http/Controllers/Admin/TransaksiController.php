<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Denda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        $transaksi = Transaksi::with('detailTransaksi.alat')->findOrFail($id);

        if ($transaksi->status !== 'menunggu') {
            return redirect()->route('admin.transaksi.show', $id)
                ->with('error', 'Pesanan ini tidak bisa dikonfirmasi karena statusnya bukan menunggu.');
        }

        foreach ($transaksi->detailTransaksi as $detail) {
            if (!$detail->alat) {
                return redirect()->route('admin.transaksi.show', $id)
                    ->with('error', 'Ada alat yang tidak ditemukan pada transaksi ini.');
            }

            if ($detail->alat->stok_tersedia < $detail->jumlah) {
                return redirect()->route('admin.transaksi.show', $id)
                    ->with('error', 'Stok ' . $detail->alat->nama_alat . ' tidak mencukupi untuk dikonfirmasi.');
            }
        }

        DB::transaction(function () use ($transaksi) {
            $lamaSewa = $transaksi->detailTransaksi->first()->lama_sewa ?? 1;

            foreach ($transaksi->detailTransaksi as $detail) {
                $detail->alat->decrement('stok_tersedia', $detail->jumlah);
            }

            $transaksi->update([
                'status'          => 'aktif',
                'tanggal_mulai'   => now()->toDateString(),
                'tanggal_selesai' => now()->addDays($lamaSewa)->toDateString(),
            ]);
        });

        return redirect()->route('admin.transaksi.show', $id)
            ->with('success', 'Pesanan berhasil dikonfirmasi dan stok alat sudah dikurangi.');
    }

    public function tolak($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status !== 'menunggu') {
            return redirect()->route('admin.transaksi.show', $id)
                ->with('error', 'Pesanan hanya bisa ditolak saat status masih menunggu.');
        }

        $transaksi->update([
            'status' => 'ditolak',
        ]);

        return redirect()->route('admin.transaksi.show', $id)
            ->with('success', 'Pesanan berhasil ditolak.');
    }

    public function selesai(Request $request, $id)
    {
        $transaksi = Transaksi::with('detailTransaksi.alat', 'denda')->findOrFail($id);

        if ($transaksi->status !== 'aktif') {
            return redirect()->back()
                ->with('error', 'Transaksi hanya bisa diselesaikan jika statusnya aktif.');
        }

        if ($request->input('source') === 'pengembalian') {
            $checkedBarang = collect($request->input('barang_dikembalikan', []))
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values()
                ->toArray();

            $semuaBarang = $transaksi->detailTransaksi
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->sort()
                ->values()
                ->toArray();

            if ($checkedBarang !== $semuaBarang) {
                return redirect()->back()
                    ->with('error', 'Semua barang harus dicentang sebelum transaksi diselesaikan.');
            }
        }

        DB::transaction(function () use ($transaksi) {
            $totalDenda = 0;

            if ($transaksi->tanggal_selesai) {
                $tanggalSelesai = Carbon::parse($transaksi->tanggal_selesai)->startOfDay();
                $hariIni = now()->startOfDay();

                if ($hariIni->greaterThan($tanggalSelesai)) {
                    $hariTerlambat = $tanggalSelesai->diffInDays($hariIni);
                    $dendaPerHari = $transaksi->total_harga * 0.1;
                    $totalDenda = $hariTerlambat * $dendaPerHari;

                    Denda::create([
                        'transaksi_id'   => $transaksi->id,
                        'hari_terlambat' => $hariTerlambat,
                        'denda_per_hari' => $dendaPerHari,
                        'total_denda'    => $totalDenda,
                        'keterangan'     => "Terlambat {$hariTerlambat} hari",
                    ]);
                }
            }

            foreach ($transaksi->detailTransaksi as $detail) {
                if ($detail->alat) {
                    $detail->alat->increment('stok_tersedia', $detail->jumlah);
                }
            }

            $transaksi->update([
                'status'      => 'selesai',
                'total_denda' => $totalDenda,
            ]);
        });

        if ($request->input('source') === 'pengembalian') {
            return redirect()->route('admin.pengembalian.index')
                ->with('success', 'Pengembalian berhasil diverifikasi. Transaksi sudah selesai dan stok barang sudah dikembalikan.');
        }

        return redirect()->route('admin.transaksi.show', $id)
            ->with('success', 'Transaksi berhasil diselesaikan dan stok alat sudah dikembalikan.');
    }

    public function pengembalianIndex()
    {
        return view('admin.pengembalian.index');
    }

    public function cariPengembalian(Request $request)
    {
        $request->validate([
            'kode_transaksi' => 'required|string|max:50',
        ]);

        $kode = strtoupper(trim($request->kode_transaksi));

        $transaksi = Transaksi::where('kode_transaksi', $kode)->first();

        if (!$transaksi) {
            return redirect()->back()
                ->withErrors(['kode_transaksi' => 'Transaksi dengan kode tersebut tidak ditemukan.'])
                ->withInput();
        }

        if ($transaksi->status === 'menunggu') {
            return redirect()->back()
                ->withErrors(['kode_transaksi' => 'Transaksi ini belum aktif. Pengembalian hanya bisa dilakukan setelah transaksi dikonfirmasi admin.'])
                ->withInput();
        }

        if ($transaksi->status === 'ditolak') {
            return redirect()->back()
                ->withErrors(['kode_transaksi' => 'Transaksi ini sudah ditolak dan tidak bisa diproses pengembaliannya.'])
                ->withInput();
        }

        if ($transaksi->status === 'selesai') {
            return redirect()->back()
                ->withErrors(['kode_transaksi' => 'Transaksi ini sudah selesai.'])
                ->withInput();
        }

        return redirect()->route('admin.pengembalian.detail', $transaksi->kode_transaksi);
    }

    public function detailPengembalian($kode)
    {
        $transaksi = Transaksi::with('customer', 'detailTransaksi.alat', 'denda')
            ->where('kode_transaksi', strtoupper($kode))
            ->firstOrFail();

        if ($transaksi->status !== 'aktif') {
            return redirect()->route('admin.pengembalian.index')
                ->with('error', 'Pengembalian hanya bisa diproses untuk transaksi yang sedang aktif.');
        }

        $hariTerlambat = 0;
        $estimasiDenda = 0;
        $dendaPerHari = $transaksi->total_harga * 0.1;

        if ($transaksi->tanggal_selesai) {
            $tanggalSelesai = Carbon::parse($transaksi->tanggal_selesai)->startOfDay();
            $hariIni = now()->startOfDay();

            if ($hariIni->greaterThan($tanggalSelesai)) {
                $hariTerlambat = $tanggalSelesai->diffInDays($hariIni);
                $estimasiDenda = $hariTerlambat * $dendaPerHari;
            }
        }

        return view('admin.pengembalian.detail', compact(
            'transaksi',
            'hariTerlambat',
            'estimasiDenda',
            'dendaPerHari'
        ));
    }
}