@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')

@php
    $detailPertama = $transaksi->detailTransaksi->first();
    $lamaSewa = max((int) ($detailPertama->lama_sewa ?? 1), 1);

    $isAktif = in_array($transaksi->status, [
        'disetujui',
        'aktif',
    ]);

    $jamToleransi = 2;

    /*
    |--------------------------------------------------------------------------
    | Perhitungan biaya sewa harian
    |--------------------------------------------------------------------------
    */

    $totalSewaHarian = $transaksi->detailTransaksi->sum(function ($detail) {
        return (float) $detail->harga_satuan * (int) $detail->jumlah;
    });

    /*
    |--------------------------------------------------------------------------
    | Denda 100% dari harga sewa harian
    |--------------------------------------------------------------------------
    */

    $dendaPerPeriode = (int) round($totalSewaHarian);

    /*
    |--------------------------------------------------------------------------
    | Perhitungan waktu
    |--------------------------------------------------------------------------
    */

    $batasKembali = null;
    $batasToleransi = null;
    $countdownTarget = null;
    $statusWaktu = null;

    $menitTerlambat = 0;
    $periodeDenda = 0;
    $estimasiDenda = 0;

    if ($isAktif && $transaksi->tanggal_selesai) {
        $sekarang = now();

        $batasKembali = \Carbon\Carbon::parse(
            $transaksi->tanggal_selesai
        );

        $batasToleransi = $batasKembali
            ->copy()
            ->addHours($jamToleransi);

        if ($sekarang->lessThan($batasKembali)) {
            $statusWaktu = 'sewa';
            $countdownTarget = $batasKembali;
        } elseif ($sekarang->lessThanOrEqualTo($batasToleransi)) {
            $statusWaktu = 'toleransi';
            $countdownTarget = $batasToleransi;
        } else {
            $statusWaktu = 'terlambat';

            $detikTerlambat = (int) floor(
                $batasToleransi->diffInSeconds($sekarang)
            );

            $menitTerlambat = (int) floor(
                $detikTerlambat / 60
            );

            $periodeDenda = max(
                1,
                (int) ceil($detikTerlambat / 86400)
            );

            $estimasiDenda = $periodeDenda
                * $dendaPerPeriode;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Format keterlambatan
    |--------------------------------------------------------------------------
    */

    $telatHari = intdiv($menitTerlambat, 1440);
    $sisaMenitSetelahHari = $menitTerlambat % 1440;
    $telatJam = intdiv($sisaMenitSetelahHari, 60);
    $telatMenit = $sisaMenitSetelahHari % 60;

    $durasiTerlambat = [];

    if ($telatHari > 0) {
        $durasiTerlambat[] = $telatHari . ' hari';
    }

    if ($telatJam > 0) {
        $durasiTerlambat[] = $telatJam . ' jam';
    }

    if ($telatMenit > 0) {
        $durasiTerlambat[] = $telatMenit . ' menit';
    }

    if (empty($durasiTerlambat) && $statusWaktu === 'terlambat') {
        $durasiTerlambat[] = 'kurang dari 1 menit';
    }

    $teksDurasiTerlambat = implode(' ', $durasiTerlambat);

    /*
    |--------------------------------------------------------------------------
    | Status transaksi
    |--------------------------------------------------------------------------
    */

    $statusText = match($transaksi->status) {
        'menunggu' => 'Menunggu Pembayaran',
        'disetujui', 'aktif' => 'Sedang Disewa',
        'ditolak' => 'Ditolak',
        'selesai' => 'Selesai',
        default => ucfirst($transaksi->status),
    };

    $statusTextColor = match($transaksi->status) {
        'menunggu' => 'text-yellow-700',
        'disetujui', 'aktif' => 'text-[#085041]',
        'ditolak' => 'text-red-600',
        'selesai' => 'text-gray-500',
        default => 'text-gray-500',
    };

    /*
    |--------------------------------------------------------------------------
    | Periode sewa
    |--------------------------------------------------------------------------
    */

    $periodeSewa = '-';

    if ($transaksi->tanggal_mulai && $transaksi->tanggal_selesai) {
        $periodeSewa =
            \Carbon\Carbon::parse($transaksi->tanggal_mulai)
                ->translatedFormat('d M Y, H:i')
            . ' - '
            . \Carbon\Carbon::parse($transaksi->tanggal_selesai)
                ->translatedFormat('d M Y, H:i');
    }

    /*
    |--------------------------------------------------------------------------
    | Ringkasan pembayaran
    |--------------------------------------------------------------------------
    */

    if ($transaksi->status === 'selesai') {
        $dendaDitampilkan = (int) $transaksi->total_denda;
    } elseif ($isAktif) {
        $dendaDitampilkan = $estimasiDenda;
    } else {
        $dendaDitampilkan = 0;
    }

    $labelDenda = $transaksi->status === 'selesai'
        ? 'Denda'
        : 'Estimasi Denda';

    $statusPembayaran = $transaksi->status_pembayaran
        ?? 'belum_bayar';

    if ($transaksi->status === 'ditolak') {
        $statusPembayaranText = 'Tidak Ada Tagihan';
        $statusPembayaranBadge = 'bg-slate-50 text-slate-500 border-slate-200';
    } else {
        $statusPembayaranText = match($statusPembayaran) {
            'belum_bayar' => 'Belum Dibayar',
            'sewa_lunas' => 'Biaya Sewa Lunas',
            'lunas' => 'Lunas',
            default => 'Belum Dibayar',
        };

        $statusPembayaranBadge = match($statusPembayaran) {
            'belum_bayar' => 'bg-amber-50 text-amber-700 border-amber-200',
            'sewa_lunas', 'lunas' => 'bg-[#e8f5f0] text-[#085041] border-[#bcebd8]',
            default => 'bg-slate-50 text-slate-500 border-slate-200',
        };
    }

    $totalBiayaSaatIni = $transaksi->status === 'ditolak'
        ? 0
        : (int) $transaksi->total_harga + $dendaDitampilkan;

    $totalSudahDibayar = $transaksi->status === 'ditolak'
        ? 0
        : (int) $transaksi->total_dibayar;

    $sisaTagihan = max(
        $totalBiayaSaatIni - $totalSudahDibayar,
        0
    );

    $tagihanPengembalian = $isAktif
        && $statusWaktu === 'terlambat'
            ? $sisaTagihan
            : 0;

    $labelWaktu = match($statusWaktu) {
        'sewa' => 'Sisa Waktu',
        'toleransi' => 'Masa Toleransi',
        'terlambat' => 'Keterlambatan',
        default => 'Sisa Waktu',
    };

    $warnaWaktu = match($statusWaktu) {
        'sewa' => 'text-[#085041]',
        'toleransi' => 'text-amber-600',
        'terlambat' => 'text-red-600',
        default => 'text-gray-400',
    };
@endphp

<section class="pt-28 md:pt-32 pb-20 md:pb-24 bg-[#f6f8f7] min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">

        <a
            href="{{ route('customer.transaksi.index') }}"
            class="inline-flex items-center gap-2 text-[#085041] hover:text-[#00372c] text-sm font-semibold mb-6 transition-colors"
        >
            <svg
                width="16"
                height="16"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                viewBox="0 0 24 24"
            >
                <path d="M15 18l-6-6 6-6"/>
            </svg>

            Kembali ke Transaksi Saya
        </a>

        {{-- HEADER --}}
        <div class="mb-8 md:mb-10">

            <div class="mb-7">
                <h1 class="text-3xl md:text-4xl font-bold text-[#00372c] tracking-tight">
                    Detail Transaksi
                </h1>

                <p class="text-gray-400 text-sm mt-2 font-medium">
                    {{ $transaksi->kode_transaksi }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-[190px_410px_220px] gap-y-6 lg:gap-x-8 w-full lg:w-fit">

    {{-- STATUS --}}
    <div>
        <p class="text-[11px] uppercase tracking-[0.18em] font-bold text-slate-400 mb-2">
            Status
        </p>

        <p class="text-xl md:text-[22px] font-bold {{ $statusTextColor }} leading-tight whitespace-nowrap">
            {{ $statusText }}
        </p>
    </div>

    {{-- PERIODE SEWA --}}
    <div>
        <p class="text-[11px] uppercase tracking-[0.18em] font-bold text-slate-400 mb-2">
            Periode Sewa
        </p>

        <p class="text-xl md:text-[22px] font-bold text-[#1f2937] leading-tight whitespace-nowrap">
            {{ $periodeSewa }}
        </p>
    </div>

    {{-- WAKTU --}}
    <div>
        <p class="text-[11px] uppercase tracking-[0.18em] font-bold text-slate-400 mb-2">
            {{ $labelWaktu }}
        </p>

        @if($isAktif && in_array($statusWaktu, ['sewa', 'toleransi']))
            <p
                id="countdown"
                class="text-xl md:text-[22px] font-bold {{ $warnaWaktu }} leading-tight tabular-nums whitespace-nowrap"
            >
                00:00:00
            </p>
        @elseif($isAktif && $statusWaktu === 'terlambat')
            <p class="text-xl md:text-[22px] font-bold text-red-600 leading-tight whitespace-nowrap">
                {{ $teksDurasiTerlambat }}
            </p>
        @else
            <p class="text-xl md:text-[22px] font-bold text-gray-300 leading-tight">
                -
            </p>
        @endif
    </div>

</div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_390px] gap-6 items-start">

            {{-- ALAT YANG DISEWA --}}
            <div class="order-1 xl:col-start-1 xl:row-start-1 space-y-3">

                <div class="flex items-center justify-between gap-4">
                    <h2 class="text-lg font-bold text-[#00372c]">
                        Alat yang Disewa
                    </h2>

                    <p class="text-sm font-semibold text-slate-500">
                        {{ $transaksi->detailTransaksi->sum('jumlah') }} item
                    </p>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-4 md:p-5 shadow-sm">

                    {{-- HEADER DESKTOP --}}
                    <div class="hidden md:grid grid-cols-[54px_minmax(0,1fr)_120px_150px] gap-4 px-3 py-3 bg-slate-50 rounded-xl text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
                        <div>Foto</div>
                        <div>Nama Barang</div>
                        <div>Jumlah</div>
                        <div class="text-right">Subtotal</div>
                    </div>

                    <div class="space-y-3">
                        @forelse($transaksi->detailTransaksi as $detail)

                            <div class="border border-slate-100 rounded-xl p-3 hover:border-slate-200 transition-colors">

                                {{-- MOBILE --}}
                                <div class="md:hidden">

                                    <div class="flex gap-3">

                                        <div class="w-16 h-16 rounded-xl bg-slate-50 overflow-hidden flex items-center justify-center border border-slate-200 shrink-0">

                                            @if($detail->foto_barang)
                                                <img
                                                    src="{{ asset('storage/' . $detail->foto_barang) }}"
                                                    alt="{{ $detail->alat->nama_alat ?? 'Foto barang' }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @elseif($detail->alat && $detail->alat->foto_alat)
                                                <img
                                                    src="{{ asset('storage/' . $detail->alat->foto_alat) }}"
                                                    alt="{{ $detail->alat->nama_alat ?? 'Foto alat' }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @else
                                                <svg
                                                    width="22"
                                                    height="22"
                                                    fill="none"
                                                    stroke="#94a3b8"
                                                    stroke-width="2"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4A2 2 0 0 0 21 16z"/>
                                                    <path d="M3.3 7L12 12l8.7-5M12 22V12"/>
                                                </svg>
                                            @endif

                                        </div>

                                        <div class="min-w-0 flex-1">

                                            <h3 class="text-sm font-bold text-[#00372c] leading-snug">
                                                {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                            </h3>

                                            <p class="text-xs text-slate-500 mt-1.5">
                                                {{ $detail->jumlah }} unit
                                                &middot;
                                                {{ $detail->lama_sewa }} hari
                                                &middot;
                                                Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}/hari
                                            </p>

                                            <p class="text-[#085041] font-bold text-base mt-3">
                                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                            </p>

                                        </div>
                                    </div>
                                </div>

                                {{-- DESKTOP --}}
                                <div class="hidden md:grid grid-cols-[54px_minmax(0,1fr)_120px_150px] gap-4 items-center">

                                    <div class="w-14 h-14 rounded-lg bg-slate-50 overflow-hidden flex items-center justify-center border border-slate-200">

                                        @if($detail->foto_barang)
                                            <img
                                                src="{{ asset('storage/' . $detail->foto_barang) }}"
                                                alt="{{ $detail->alat->nama_alat ?? 'Foto barang' }}"
                                                class="w-full h-full object-cover"
                                            >
                                        @elseif($detail->alat && $detail->alat->foto_alat)
                                            <img
                                                src="{{ asset('storage/' . $detail->alat->foto_alat) }}"
                                                alt="{{ $detail->alat->nama_alat ?? 'Foto alat' }}"
                                                class="w-full h-full object-cover"
                                            >
                                        @else
                                            <svg
                                                width="20"
                                                height="20"
                                                fill="none"
                                                stroke="#94a3b8"
                                                stroke-width="2"
                                                viewBox="0 0 24 24"
                                            >
                                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4A2 2 0 0 0 21 16z"/>
                                                <path d="M3.3 7L12 12l8.7-5M12 22V12"/>
                                            </svg>
                                        @endif

                                    </div>

                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-[#00372c] mb-1 truncate">
                                            {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                        </h3>

                                        <p class="text-xs text-slate-500">
                                            Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}/hari
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-sm font-semibold text-slate-600">
                                            {{ $detail->jumlah }} unit
                                        </p>

                                        <p class="text-xs text-slate-400 mt-1">
                                            {{ $detail->lama_sewa }} hari
                                        </p>
                                    </div>

                                    <div class="text-right">
                                        <p class="text-sm font-bold text-[#085041] whitespace-nowrap">
                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>

                                </div>

                            </div>

                        @empty
                            <div class="bg-slate-50 border border-dashed border-slate-200 rounded-xl p-6 text-center">

                                <svg
                                    class="w-12 h-12 mx-auto mb-2 text-slate-300"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.5"
                                    viewBox="0 0 24 24"
                                >
                                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>

                                <p class="text-sm text-slate-400">
                                    Tidak ada detail alat
                                </p>

                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

            {{-- RINGKASAN PEMBAYARAN --}}
            <div class="order-2 xl:col-start-2 xl:row-start-1 xl:row-span-2">

                <div class="xl:sticky xl:top-28">

                    <div class="bg-white border border-[#e5ebe7] rounded-2xl overflow-hidden shadow-sm">

                        <div class="bg-[#085041] px-5 py-4">
                            <h2 class="text-lg font-bold text-white">
                                Ringkasan Pembayaran
                            </h2>
                        </div>

                        <div class="p-5">

                            <div class="space-y-3.5 text-sm">

                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-slate-600">
                                        Harga Sewa
                                    </span>

                                    <span class="font-bold text-[#1f2937]">
                                        Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-slate-600">
                                        Biaya Layanan
                                    </span>

                                    <span class="font-bold text-emerald-600">
                                        Gratis
                                    </span>
                                </div>

                                @if(
                                    ($isAktif && in_array($statusWaktu, ['toleransi', 'terlambat'])) ||
                                    ($transaksi->status === 'selesai' && $transaksi->total_denda > 0)
                                )
                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-slate-600">
                                            {{ $labelDenda }}
                                        </span>

                                        <span class="font-bold {{ $dendaDitampilkan > 0 ? 'text-red-600' : 'text-[#085041]' }}">
                                            Rp {{ number_format($dendaDitampilkan, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between gap-4 pt-3 border-t border-dashed border-slate-200">
                                    <span class="text-slate-600">
                                        Metode
                                    </span>

                                    <span class="font-semibold text-[#085041]">
                                        Cash
                                    </span>
                                </div>

                                <div class="pt-4 border-t border-slate-200">
                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-slate-600">
                                            Status Pembayaran
                                        </span>

                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full border text-xs font-bold {{ $statusPembayaranBadge }}">
                                            {{ $statusPembayaranText }}
                                        </span>
                                    </div>

                                    @if($transaksi->dibayar_pada)
                                        <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                                            Biaya sewa diterima pada
                                            {{ $transaksi->dibayar_pada->translatedFormat('d M Y, H:i') }} WIB.
                                        </p>
                                    @endif

                                    @if($transaksi->denda_dibayar_pada)
                                        <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                                            Denda diterima pada
                                            {{ $transaksi->denda_dibayar_pada->translatedFormat('d M Y, H:i') }} WIB.
                                        </p>
                                    @endif
                                </div>

                                <div class="pt-4 border-t border-slate-200 space-y-3">
                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-slate-600">
                                            Total Biaya Saat Ini
                                        </span>

                                        <span class="font-bold text-[#1f2937]">
                                            Rp {{ number_format($totalBiayaSaatIni, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between gap-4">
                                        <span class="text-slate-600">
                                            Total Sudah Dibayar
                                        </span>

                                        <span class="font-bold text-[#085041]">
                                            Rp {{ number_format($totalSudahDibayar, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between gap-4">
                                        <span class="font-semibold text-slate-700">
                                            Sisa Tagihan
                                        </span>

                                        <span class="text-xl font-bold {{ $sisaTagihan > 0 ? 'text-red-600' : 'text-[#085041]' }}">
                                            Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                @if($tagihanPengembalian > 0)
                                    <div class="pt-4 border-t border-slate-200">
                                        <p class="text-xs font-bold text-red-600 uppercase tracking-wider mb-1">
                                            Tagihan Saat Pengembalian
                                        </p>

                                        <p class="text-xl font-bold text-red-600">
                                            Rp {{ number_format($tagihanPengembalian, 0, ',', '.') }}
                                        </p>

                                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                            Nominal berasal dari estimasi denda dan masih dapat bertambah selama barang belum dikembalikan.
                                        </p>
                                    </div>
                                @elseif($isAktif && $statusWaktu === 'toleransi')
                                    <p class="text-xs text-amber-600 leading-relaxed pt-1">
                                        Belum ada denda selama masa toleransi masih berlangsung.
                                    </p>
                                @endif

                            </div>

                        </div>
                    </div>

                </div>
            </div>

            {{-- INFORMASI PENGEMBALIAN TRANSAKSI AKTIF --}}
            @if($isAktif)

                <div class="order-3 xl:col-start-1 xl:row-start-2">

                    {{-- MASA SEWA --}}
                    @if($statusWaktu === 'sewa')

                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

                            <div class="flex items-start gap-3">

                                <div class="w-10 h-10 rounded-xl bg-[#e8f5f0] text-[#085041] flex items-center justify-center shrink-0">

                                    <svg
                                        width="20"
                                        height="20"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="M12 7v5l3 2"/>
                                    </svg>

                                </div>

                                <div>
                                    <h3 class="text-lg font-bold text-[#00372c]">
                                        Masa Sewa Berlangsung
                                    </h3>

                                    <p class="text-sm leading-relaxed text-slate-600 mt-1">
                                        Barang sedang dalam masa sewa. Kembalikan barang paling lambat pada waktu berikut.
                                    </p>

                                    <p class="text-sm font-bold text-[#085041] mt-3">
                                        {{ $batasKembali->translatedFormat('d F Y, H:i') }} WIB
                                    </p>
                                </div>

                            </div>
                        </div>

                    @endif

                    {{-- MASA TOLERANSI --}}
                    @if($statusWaktu === 'toleransi')

                        <div class="bg-white border border-amber-200 rounded-2xl p-5 shadow-sm">

                            <div class="flex items-start gap-3">

                                <div class="w-10 h-10 rounded-xl border border-amber-200 text-amber-600 flex items-center justify-center shrink-0">

                                    <svg
                                        width="20"
                                        height="20"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="M12 7v5"/>
                                        <path d="M12 16h.01"/>
                                    </svg>

                                </div>

                                <div>
                                    <h3 class="text-lg font-bold text-amber-700">
                                        Masa Toleransi {{ $jamToleransi }} Jam
                                    </h3>

                                    <p class="text-sm leading-relaxed text-slate-600 mt-1">
                                        Waktu sewa sudah habis. Segera kembalikan barang sebelum masa toleransi berakhir agar tidak dikenakan denda.
                                    </p>

                                    <p class="text-sm font-bold text-amber-700 mt-3">
                                        Toleransi berakhir pada
                                        {{ $batasToleransi->translatedFormat('d F Y, H:i') }}
                                        WIB
                                    </p>

                                    <p class="text-xs text-slate-500 mt-2">
                                        Selama masa toleransi berlangsung, estimasi denda masih Rp0.
                                    </p>
                                </div>

                            </div>
                        </div>

                    @endif

                   {{-- TERLAMBAT --}}
@if($statusWaktu === 'terlambat')

    <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

        <div>
            <h3 class="text-lg font-bold text-red-600">
                Pengembalian Terlambat
            </h3>

            <p class="text-sm leading-relaxed text-slate-600 mt-1">
                Masa toleransi sudah berakhir. Segera kembalikan barang agar denda tidak bertambah.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-5">

                <div class="border-t border-slate-200 pt-3">
                    <p class="text-xs text-slate-400 mb-1">
                        Durasi Terlambat
                    </p>

                    <p class="text-sm font-bold text-red-600">
                        {{ $teksDurasiTerlambat }}
                    </p>
                </div>

                <div class="border-t border-slate-200 pt-3">
                    <p class="text-xs text-slate-400 mb-1">
                        Tarif Denda
                    </p>

                    <p class="text-sm font-bold text-red-600">
                        Rp {{ number_format($dendaPerPeriode, 0, ',', '.') }} / 24 jam
                    </p>
                </div>

                <div class="border-t border-slate-200 pt-3">
                    <p class="text-xs text-slate-400 mb-1">
                        Estimasi Denda
                    </p>

                    <p class="text-sm font-bold text-red-600">
                        Rp {{ number_format($estimasiDenda, 0, ',', '.') }}
                    </p>
                </div>

            </div>

            <p class="text-xs text-slate-500 mt-4 leading-relaxed">
                Denda sebesar 100% dari total harga sewa harian dikenakan untuk setiap periode keterlambatan 24 jam yang telah dimulai.
            </p>
        </div>

    </div>

@endif

                </div>

            @endif

            {{-- INFORMASI STATUS LAINNYA --}}
            @if(in_array($transaksi->status, ['menunggu', 'ditolak', 'selesai']))

                <div class="order-3 xl:col-start-1 xl:row-start-2">

                    @if($transaksi->status === 'menunggu')

                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

                            <h3 class="text-lg font-bold text-[#00372c] mb-2">
                                Menunggu Pembayaran dan Konfirmasi
                            </h3>

                            <p class="text-sm leading-relaxed text-slate-600">
                                Datang ke kasir untuk membayar biaya sewa secara cash dan mengambil barang. Setelah pembayaran dikonfirmasi admin, status pembayaran berubah menjadi Biaya Sewa Lunas dan masa sewa mulai dihitung.
                            </p>

                        </div>

                    @endif

                    @if($transaksi->status === 'ditolak')

                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

                            <p class="text-xs uppercase tracking-[0.16em] font-bold text-slate-400 mb-3">
                                Status Transaksi
                            </p>

                            <h3 class="text-lg font-bold text-red-600 mb-2">
                                Transaksi Ditolak
                            </h3>

                            <p class="text-sm leading-relaxed text-slate-600">
                                Silakan buat pengajuan baru atau hubungi admin untuk informasi lebih lanjut.
                            </p>

                        </div>

                    @endif

                    @if($transaksi->status === 'selesai')

                        <div class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm">

                            <p class="text-xs uppercase tracking-[0.16em] font-bold text-slate-400 mb-3">
                                Status Transaksi
                            </p>

                            <h3 class="text-lg font-bold text-[#00372c] mb-2">
                                Transaksi Selesai
                            </h3>

                            <p class="text-sm leading-relaxed text-slate-600">
                                Barang sudah dikembalikan dan transaksi telah diselesaikan.
                                @if($statusPembayaran === 'lunas')
                                    Seluruh pembayaran juga sudah tercatat lunas.
                                @endif
                            </p>

                        </div>

                    @endif

                </div>

            @endif

        </div>
    </div>
</section>

@if($isAktif && $countdownTarget)
    <script>
        const countdownElement = document.getElementById('countdown');

        const targetDate = new Date(
            @json($countdownTarget->toIso8601String())
        ).getTime();

        function pad(number) {
            return String(number).padStart(2, '0');
        }

        function updateCountdown() {
            if (!countdownElement) {
                return false;
            }

            const distance = targetDate - Date.now();

            if (distance <= 0) {
                countdownElement.textContent = '00:00:00';

                setTimeout(function () {
                    window.location.reload();
                }, 1000);

                return false;
            }

            const totalSeconds = Math.floor(distance / 1000);
            const totalHours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            countdownElement.textContent =
                pad(totalHours)
                + ':'
                + pad(minutes)
                + ':'
                + pad(seconds);

            return true;
        }

        updateCountdown();

        const countdownInterval = setInterval(function () {
            if (!updateCountdown()) {
                clearInterval(countdownInterval);
            }
        }, 1000);
    </script>
@endif

@if($isAktif && $statusWaktu === 'terlambat')
    <script>
        setTimeout(function () {
            window.location.reload();
        }, 60000);
    </script>
@endif

@endsection