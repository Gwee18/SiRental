<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Denda;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class TransaksiController extends Controller
{
    private const JAM_TOLERANSI = 2;

    private const PERSENTASE_DENDA = 1.00;

    public function index()
    {
        $transaksi = Transaksi::with([
            'customer',
            'detailTransaksi',
        ])
            ->withSum('detailTransaksi as jumlah_item', 'jumlah')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.transaksi.index', compact('transaksi'));
    }

    public function show($id)
    {
        $transaksi = Transaksi::with([
            'customer',
            'detailTransaksi.alat',
            'denda',
        ])
            ->findOrFail($id);

        return view('admin.transaksi.detail', compact('transaksi'));
    }

    public function approve($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $transaksi = Transaksi::with('detailTransaksi')
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($transaksi->status !== 'menunggu') {
                    throw new RuntimeException(
                        'Pesanan ini tidak bisa dikonfirmasi karena statusnya bukan menunggu.'
                    );
                }

                if ($transaksi->detailTransaksi->isEmpty()) {
                    throw new RuntimeException(
                        'Pesanan tidak memiliki barang yang dapat dikonfirmasi.'
                    );
                }

                $alatTerkunci = [];

                foreach ($transaksi->detailTransaksi as $detail) {
                    $alat = Alat::query()
                        ->lockForUpdate()
                        ->find($detail->alat_id);

                    if (! $alat) {
                        throw new RuntimeException(
                            'Ada alat yang tidak ditemukan pada transaksi ini.'
                        );
                    }

                    if (! $alat->is_active) {
                        throw new RuntimeException(
                            'Pesanan tidak dapat dikonfirmasi karena '.
                            $alat->nama_alat.
                            ' sedang dinonaktifkan.'
                        );
                    }

                    if ($alat->stok_tersedia < $detail->jumlah) {
                        throw new RuntimeException(
                            'Stok '.$alat->nama_alat
                            .' tidak mencukupi untuk dikonfirmasi.'
                        );
                    }

                    $alatTerkunci[$detail->id] = $alat;
                }

                $lamaSewa = max(
                    (int) ($transaksi->detailTransaksi->first()->lama_sewa ?? 1),
                    1
                );

                $tanggalMulai = now();
                $tanggalSelesai = $tanggalMulai
                    ->copy()
                    ->addHours($lamaSewa * 24);

                foreach ($transaksi->detailTransaksi as $detail) {
                    $alat = $alatTerkunci[$detail->id];

                    $alat->update([
                        'stok_tersedia' => $alat->stok_tersedia - $detail->jumlah,
                    ]);
                }

                $transaksi->update([
                    'status' => 'aktif',
                    'status_pembayaran' => Transaksi::PEMBAYARAN_SEWA_LUNAS,
                    'total_dibayar' => (int) $transaksi->total_harga,
                    'dibayar_pada' => $tanggalMulai,
                    'denda_dibayar_pada' => null,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,
                ]);
            });
        } catch (RuntimeException $exception) {
            return redirect()
                ->route('admin.transaksi.show', $id)
                ->with('error', $exception->getMessage());
        }

        return redirect()
            ->route('admin.transaksi.show', $id)
            ->with(
                'success',
                'Pembayaran sewa berhasil dicatat, pesanan dikonfirmasi, dan stok alat sudah dikurangi.'
            );
    }

    public function tolak($id)
    {
        $transaksi = Transaksi::findOrFail($id);

        if ($transaksi->status !== 'menunggu') {
            return redirect()
                ->route('admin.transaksi.show', $id)
                ->with(
                    'error',
                    'Pesanan hanya bisa ditolak saat status masih menunggu.'
                );
        }

        $transaksi->update([
            'status' => 'ditolak',
            'status_pembayaran' => Transaksi::PEMBAYARAN_BELUM_BAYAR,
            'total_dibayar' => 0,
            'dibayar_pada' => null,
            'denda_dibayar_pada' => null,
        ]);

        return redirect()
            ->route('admin.transaksi.show', $id)
            ->with('success', 'Pesanan berhasil ditolak.');
    }

    public function selesai(Request $request, $id)
    {
        $source = $request->input('source');

        $checkedBarang = collect(
            $request->input('barang_dikembalikan', [])
        )
            ->map(fn ($detailId) => (int) $detailId)
            ->sort()
            ->values()
            ->toArray();

        $pembayaranDendaDikonfirmasi = $request->boolean(
            'konfirmasi_pembayaran_denda'
        );

        try {
            DB::transaction(function () use (
                $id,
                $source,
                $checkedBarang,
                $pembayaranDendaDikonfirmasi
            ) {
                $transaksi = Transaksi::with([
                    'detailTransaksi',
                    'denda',
                ])
                    ->lockForUpdate()
                    ->findOrFail($id);

                if ($transaksi->status !== 'aktif') {
                    throw new RuntimeException(
                        'Transaksi hanya bisa diselesaikan jika statusnya aktif.'
                    );
                }

                if (
                    $transaksi->status_pembayaran
                    !== Transaksi::PEMBAYARAN_SEWA_LUNAS
                    || (int) $transaksi->total_dibayar < (int) $transaksi->total_harga
                ) {
                    throw new RuntimeException(
                        'Pembayaran sewa belum tercatat lunas. Periksa data pembayaran sebelum menyelesaikan transaksi.'
                    );
                }

                if ($source === 'pengembalian') {
                    $semuaBarang = $transaksi->detailTransaksi
                        ->pluck('id')
                        ->map(fn ($detailId) => (int) $detailId)
                        ->sort()
                        ->values()
                        ->toArray();

                    if ($checkedBarang !== $semuaBarang) {
                        throw new RuntimeException(
                            'Semua barang harus dicentang sebelum transaksi diselesaikan.'
                        );
                    }
                }

                $perhitunganDenda = $this->hitungDenda($transaksi);

                $hariTerlambat = $perhitunganDenda['hariTerlambat'];
                $dendaPerHari = $perhitunganDenda['dendaPerHari'];
                $totalDenda = $perhitunganDenda['estimasiDenda'];

                if ($totalDenda > 0) {
                    if (
                        $source !== 'pengembalian'
                        || ! $pembayaranDendaDikonfirmasi
                    ) {
                        throw new RuntimeException(
                            'Konfirmasi bahwa pembayaran denda sudah diterima sebelum transaksi diselesaikan.'
                        );
                    }

                    Denda::updateOrCreate(
                        [
                            'transaksi_id' => $transaksi->id,
                        ],
                        [
                            'hari_terlambat' => $hariTerlambat,
                            'denda_per_hari' => $dendaPerHari,
                            'total_denda' => $totalDenda,
                            'keterangan' => "Terlambat {$hariTerlambat} periode setelah masa toleransi",
                        ]
                    );
                } elseif ($transaksi->denda) {
                    $transaksi->denda->delete();
                }

                foreach ($transaksi->detailTransaksi as $detail) {
                    $alat = Alat::query()
                        ->lockForUpdate()
                        ->find($detail->alat_id);

                    if ($alat) {
                        $alat->update([
                            'stok_tersedia' => $alat->stok_tersedia + $detail->jumlah,
                        ]);
                    }
                }

                $waktuSelesai = now();
                $totalPembayaran = (int) $transaksi->total_harga + $totalDenda;

                $transaksi->update([
                    'status' => 'selesai',
                    'status_pembayaran' => Transaksi::PEMBAYARAN_LUNAS,
                    'total_denda' => $totalDenda,
                    'total_dibayar' => $totalPembayaran,
                    'dibayar_pada' => $transaksi->dibayar_pada ?: $waktuSelesai,
                    'denda_dibayar_pada' => $totalDenda > 0
                        ? $waktuSelesai
                        : null,
                ]);
            });
        } catch (RuntimeException $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $exception->getMessage());
        }

        if ($source === 'pengembalian') {
            return redirect()
                ->route('admin.pengembalian.index')
                ->with(
                    'success',
                    'Pengembalian berhasil diverifikasi, pembayaran sudah dilunasi, transaksi selesai, dan stok barang dikembalikan.'
                );
        }

        return redirect()
            ->route('admin.transaksi.show', $id)
            ->with(
                'success',
                'Transaksi berhasil diselesaikan dan seluruh pembayaran sudah tercatat lunas.'
            );
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

        $transaksi = Transaksi::where(
            'kode_transaksi',
            $kode
        )->first();

        if (! $transaksi) {
            return redirect()
                ->back()
                ->withErrors([
                    'kode_transaksi' => 'Transaksi dengan kode tersebut tidak ditemukan.',
                ])
                ->withInput();
        }

        if ($transaksi->status === 'menunggu') {
            return redirect()
                ->back()
                ->withErrors([
                    'kode_transaksi' => 'Transaksi ini belum aktif. Pengembalian hanya bisa dilakukan setelah transaksi dikonfirmasi admin.',
                ])
                ->withInput();
        }

        if ($transaksi->status === 'ditolak') {
            return redirect()
                ->back()
                ->withErrors([
                    'kode_transaksi' => 'Transaksi ini sudah ditolak dan tidak bisa diproses pengembaliannya.',
                ])
                ->withInput();
        }

        if ($transaksi->status === 'selesai') {
            return redirect()
                ->back()
                ->withErrors([
                    'kode_transaksi' => 'Transaksi ini sudah selesai.',
                ])
                ->withInput();
        }

        return redirect()->route(
            'admin.pengembalian.detail',
            $transaksi->kode_transaksi
        );
    }

    public function detailPengembalian($kode)
    {
        $transaksi = Transaksi::with([
            'customer',
            'detailTransaksi.alat',
            'denda',
        ])
            ->where('kode_transaksi', strtoupper($kode))
            ->firstOrFail();

        if ($transaksi->status !== 'aktif') {
            return redirect()
                ->route('admin.pengembalian.index')
                ->with(
                    'error',
                    'Pengembalian hanya bisa diproses untuk transaksi yang sedang aktif.'
                );
        }

        if (
            ! $transaksi->tanggal_mulai
            || ! $transaksi->tanggal_selesai
        ) {
            return redirect()
                ->route('admin.pengembalian.index')
                ->with(
                    'error',
                    'Waktu mulai atau batas pengembalian transaksi belum tersedia.'
                );
        }

        $sekarang = now();

        $batasKembali = Carbon::parse(
            $transaksi->tanggal_selesai
        );

        $batasToleransi = $batasKembali
            ->copy()
            ->addHours(self::JAM_TOLERANSI);

        $statusWaktu = 'sewa';
        $countdownTarget = $batasKembali;

        if (
            $sekarang->greaterThanOrEqualTo($batasKembali)
            && $sekarang->lessThanOrEqualTo($batasToleransi)
        ) {
            $statusWaktu = 'toleransi';
            $countdownTarget = $batasToleransi;
        } elseif ($sekarang->greaterThan($batasToleransi)) {
            $statusWaktu = 'terlambat';
            $countdownTarget = null;
        }

        $perhitunganDenda = $this->hitungDenda($transaksi);

        return view(
            'admin.pengembalian.detail',
            [
                'transaksi' => $transaksi,
                'statusWaktu' => $statusWaktu,
                'countdownTarget' => $countdownTarget,
                'batasKembali' => $batasKembali,
                'batasToleransi' => $batasToleransi,
                'jamToleransi' => self::JAM_TOLERANSI,
                'totalSewaHarian' => $perhitunganDenda['totalSewaHarian'],
                'dendaPerHari' => $perhitunganDenda['dendaPerHari'],
                'hariTerlambat' => $perhitunganDenda['hariTerlambat'],
                'menitTerlambat' => $perhitunganDenda['menitTerlambat'],
                'estimasiDenda' => $perhitunganDenda['estimasiDenda'],
            ]
        );
    }

    private function hitungDenda(Transaksi $transaksi): array
    {
        $totalSewaHarian = $transaksi->detailTransaksi
            ->sum(function ($detail) {
                return (float) $detail->harga_satuan
                    * (int) $detail->jumlah;
            });

        $dendaPerHari = (int) round(
            $totalSewaHarian * self::PERSENTASE_DENDA
        );

        $hariTerlambat = 0;
        $menitTerlambat = 0;
        $estimasiDenda = 0;

        if (! $transaksi->tanggal_selesai) {
            return [
                'totalSewaHarian' => $totalSewaHarian,
                'dendaPerHari' => $dendaPerHari,
                'hariTerlambat' => $hariTerlambat,
                'menitTerlambat' => $menitTerlambat,
                'estimasiDenda' => $estimasiDenda,
            ];
        }

        $batasKembali = Carbon::parse(
            $transaksi->tanggal_selesai
        );

        $batasToleransi = $batasKembali
            ->copy()
            ->addHours(self::JAM_TOLERANSI);

        $sekarang = now();

        if ($sekarang->greaterThan($batasToleransi)) {
            $detikTerlambat = (int) floor(
                $batasToleransi->diffInSeconds($sekarang)
            );

            $menitTerlambat = (int) floor(
                $detikTerlambat / 60
            );

            $hariTerlambat = max(
                1,
                (int) ceil($detikTerlambat / 86400)
            );

            $estimasiDenda = $hariTerlambat
                * $dendaPerHari;
        }

        return [
            'totalSewaHarian' => $totalSewaHarian,
            'dendaPerHari' => $dendaPerHari,
            'hariTerlambat' => $hariTerlambat,
            'menitTerlambat' => $menitTerlambat,
            'estimasiDenda' => $estimasiDenda,
        ];
    }
}
