@extends('layouts.admin')

@section('title', 'Detail Pengembalian')
@section('page-title', 'Detail Pengembalian')

@section('content')

    @if(session('error'))
        <div class="mb-6 bg-red-50 text-red-600 text-sm font-medium px-4 py-3 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    @php
        $totalBiayaTransaksi = (int) $transaksi->total_harga + (int) $estimasiDenda;
        $biayaSudahDibayar = (int) $transaksi->total_dibayar;
        $tagihanPengembalian = max(0, $totalBiayaTransaksi - $biayaSudahDibayar);
        $wajibKonfirmasiPembayaranDenda = $tagihanPengembalian > 0;
        $totalItem = $transaksi->detailTransaksi->sum('jumlah');

        $statusPembayaranColor = match($transaksi->status_pembayaran) {
            'belum_bayar' => 'text-yellow-600',
            'sewa_lunas', 'lunas' => 'text-[#085041]',
            default => 'text-gray-500',
        };

        $telatHari = intdiv($menitTerlambat, 1440);
        $sisaMenitSetelahHari = $menitTerlambat % 1440;
        $telatJam = intdiv($sisaMenitSetelahHari, 60);
        $telatMenit = $sisaMenitSetelahHari % 60;
    @endphp

    <div class="w-full">

        <a
            href="{{ route('admin.pengembalian.index') }}"
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

            Kembali ke Verifikasi Pengembalian
        </a>

        <form
            method="POST"
            action="{{ route('admin.transaksi.selesai', $transaksi->id) }}"
            id="formPengembalian"
        >
            @csrf

            <input
                type="hidden"
                name="source"
                value="pengembalian"
            >

            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_400px] 2xl:grid-cols-[minmax(0,1fr)_430px] gap-6 items-start">

                <div class="min-w-0">
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

                        <div class="flex flex-wrap items-start justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
                            <div>
                                <p class="text-sm font-bold text-[#085041] mb-3">
                                    Transaksi Aktif
                                </p>

                                <h2 class="text-2xl font-bold text-[#00372c]">
                                    {{ $transaksi->kode_transaksi }}
                                </h2>

                                <p class="text-gray-400 text-sm mt-1">
                                    Dikonfirmasi pada
                                    {{ \Carbon\Carbon::parse($transaksi->tanggal_mulai)->translatedFormat('d F Y, H:i') }}
                                    WIB
                                </p>
                            </div>

                            <p class="text-sm font-bold text-[#085041]">
                                Sedang Disewa
                            </p>
                        </div>

                        <div class="mb-8">
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-4">
                                Data Pengembalian
                            </p>

                            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5 text-sm">

                                <div>
                                    <p class="text-gray-400 text-xs mb-1">
                                        Customer
                                    </p>

                                    <p class="font-semibold text-[#00372c]">
                                        {{ $transaksi->customer->nama_lengkap ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-gray-400 text-xs mb-1">
                                        No. Telepon
                                    </p>

                                    <p class="font-semibold text-[#00372c]">
                                        {{ $transaksi->customer->no_telp ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-gray-400 text-xs mb-1">
                                        Mulai Sewa
                                    </p>

                                    <p class="font-semibold text-[#00372c]">
                                        {{ \Carbon\Carbon::parse($transaksi->tanggal_mulai)->translatedFormat('d M Y, H:i') }}
                                        WIB
                                    </p>
                                </div>

                                <div>
                                    <p class="text-gray-400 text-xs mb-1">
                                        Batas Kembali
                                    </p>

                                    <p class="font-semibold text-[#00372c]">
                                        {{ $batasKembali->translatedFormat('d M Y, H:i') }}
                                        WIB
                                    </p>
                                </div>

                                <div>
                                    <p class="text-gray-400 text-xs mb-1">
                                        Batas Toleransi
                                    </p>

                                    <p class="font-semibold text-[#00372c]">
                                        {{ $batasToleransi->translatedFormat('d M Y, H:i') }}
                                        WIB
                                    </p>
                                </div>

                            </div>
                        </div>

                        <div>
                            <div class="flex items-end justify-between gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-1">
                                        Checklist Barang Dikembalikan
                                    </p>

                                    <p class="text-gray-500 text-sm">
                                        Centang semua barang yang sudah diterima dan diperiksa.
                                    </p>
                                </div>

                                <span class="hidden md:inline-flex text-xs font-semibold text-gray-400">
                                    {{ $totalItem }} item
                                </span>
                            </div>

                            <div class="hidden md:grid grid-cols-[64px_72px_minmax(0,1fr)_140px_120px_64px] gap-4 px-4 py-3 bg-gray-50 rounded-xl text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">
                                <div>No</div>
                                <div>Foto</div>
                                <div>Nama Barang</div>
                                <div>Jumlah</div>
                                <div>Subtotal</div>
                                <div>Cek</div>
                            </div>

                            <div class="space-y-3">
                                @foreach($transaksi->detailTransaksi as $index => $detail)
                                    <label class="block cursor-pointer">
                                        <div class="return-item border border-gray-100 bg-white rounded-2xl md:rounded-xl overflow-hidden transition-all">

                                            <input
                                                type="checkbox"
                                                name="barang_dikembalikan[]"
                                                value="{{ $detail->id }}"
                                                class="return-check hidden"
                                            >

                                            <div class="md:hidden p-4 space-y-4">

                                                <div class="flex items-start justify-between gap-4">
                                                    <div>
                                                        <p class="text-xs text-gray-400">
                                                            No
                                                        </p>

                                                        <p class="font-semibold text-[#00372c]">
                                                            {{ $index + 1 }}
                                                        </p>
                                                    </div>

                                                    <div class="text-right">
                                                        <p class="text-xs text-gray-400">
                                                            Subtotal
                                                        </p>

                                                        <p class="font-bold text-[#085041]">
                                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="flex gap-4">

                                                    <div class="w-16 h-16 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden shrink-0">
                                                        @if($detail->foto_barang)
                                                            <img
                                                                src="{{ asset('storage/' . $detail->foto_barang) }}"
                                                                class="w-full h-full object-cover"
                                                                alt="Foto barang"
                                                            >
                                                        @elseif($detail->alat && $detail->alat->foto_alat)
                                                            <img
                                                                src="{{ asset('storage/' . $detail->alat->foto_alat) }}"
                                                                class="w-full h-full object-cover"
                                                                alt="Foto alat"
                                                            >
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center">
                                                                <svg
                                                                    width="24"
                                                                    height="24"
                                                                    fill="none"
                                                                    stroke="#94a3b8"
                                                                    stroke-width="1.8"
                                                                    viewBox="0 0 24 24"
                                                                >
                                                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                                                    <path d="M3.3 7L12 12l8.7-5"/>
                                                                    <path d="M12 22V12"/>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="flex-1 min-w-0">
                                                        <p class="font-semibold text-[#00372c]">
                                                            {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                                        </p>

                                                        <p class="text-sm text-gray-400 mt-1">
                                                            {{ $detail->jumlah }} unit
                                                            &middot;
                                                            {{ $detail->lama_sewa }} hari
                                                            &middot;
                                                            Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}/hari
                                                        </p>
                                                    </div>

                                                </div>

                                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                                    <span class="text-sm font-semibold text-gray-500">
                                                        Barang sudah kembali
                                                    </span>

                                                    <div class="check-ui w-7 h-7 rounded-lg border-2 border-gray-300 bg-white flex items-center justify-center text-white transition-all">
                                                        <svg
                                                            width="16"
                                                            height="16"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="3"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path d="M20 6L9 17l-5-5"/>
                                                        </svg>
                                                    </div>
                                                </div>

                                            </div>

                                            <div class="hidden md:grid grid-cols-[64px_72px_minmax(0,1fr)_140px_120px_64px] gap-4 items-center px-4 py-4">

                                                <div class="font-semibold text-[#00372c]">
                                                    {{ $index + 1 }}
                                                </div>

                                                <div>
                                                    <div class="w-14 h-14 rounded-xl bg-gray-50 border border-gray-100 overflow-hidden">
                                                        @if($detail->foto_barang)
                                                            <img
                                                                src="{{ asset('storage/' . $detail->foto_barang) }}"
                                                                class="w-full h-full object-cover"
                                                                alt="Foto barang"
                                                            >
                                                        @elseif($detail->alat && $detail->alat->foto_alat)
                                                            <img
                                                                src="{{ asset('storage/' . $detail->alat->foto_alat) }}"
                                                                class="w-full h-full object-cover"
                                                                alt="Foto alat"
                                                            >
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center">
                                                                <svg
                                                                    width="22"
                                                                    height="22"
                                                                    fill="none"
                                                                    stroke="#94a3b8"
                                                                    stroke-width="1.8"
                                                                    viewBox="0 0 24 24"
                                                                >
                                                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                                                    <path d="M3.3 7L12 12l8.7-5"/>
                                                                    <path d="M12 22V12"/>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="min-w-0">
                                                    <p class="font-semibold text-[#00372c] truncate">
                                                        {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                                    </p>

                                                    <p class="text-sm text-gray-400 mt-1">
                                                        Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}/hari
                                                    </p>
                                                </div>

                                                <div class="text-sm text-gray-500">
                                                    {{ $detail->jumlah }} unit

                                                    <span class="text-gray-300">
                                                        &middot;
                                                    </span>

                                                    {{ $detail->lama_sewa }} hari
                                                </div>

                                                <div class="font-bold text-[#085041]">
                                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                                </div>

                                                <div>
                                                    <div class="check-ui w-7 h-7 rounded-lg border-2 border-gray-300 bg-white flex items-center justify-center text-white transition-all">
                                                        <svg
                                                            width="16"
                                                            height="16"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="3"
                                                            viewBox="0 0 24 24"
                                                        >
                                                            <path d="M20 6L9 17l-5-5"/>
                                                        </svg>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                <div class="min-w-0 space-y-5">

                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

                        <div class="p-5 border-b border-gray-100">
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider">
                                Ringkasan Pengembalian
                            </p>
                        </div>

                        <div class="p-5 border-b border-gray-100">
                            <p class="text-xs font-semibold text-[#00372c] uppercase tracking-wider mb-4">
                                Biaya Sewa
                            </p>

                            <div class="space-y-3">
                                <div class="flex items-center justify-between gap-4 text-sm">
                                    <span class="text-gray-500">
                                        Total Sewa
                                    </span>

                                    <span class="font-semibold text-[#00372c]">
                                        Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-4 text-sm">
                                    <span class="text-gray-500">
                                        Status Pembayaran
                                    </span>

                                    <span class="font-semibold {{ $statusPembayaranColor }}">
                                        {{ $transaksi->status_pembayaran_label }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-4 text-sm">
                                    <span class="text-gray-500">
                                        Total Sudah Dibayar
                                    </span>

                                    <span class="font-semibold text-[#00372c]">
                                        Rp {{ number_format($biayaSudahDibayar, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-xs text-gray-400 leading-relaxed mt-3">
                                Pembayaran sewa dicatat ketika admin mengonfirmasi pesanan.
                            </p>
                        </div>

                        <div class="p-5 border-b border-gray-100">
                            <p class="text-xs font-semibold text-[#00372c] uppercase tracking-wider mb-4">
                                Status Pengembalian
                            </p>

                            @if($statusWaktu === 'sewa')
                                <div class="bg-[#e8f5f0] border border-[#bcebd8] rounded-xl p-4">
                                    <p class="text-[#085041] font-bold text-sm mb-1">
                                        Masa Sewa Berlangsung
                                    </p>

                                    <p class="text-[#085041]/70 text-sm mb-3">
                                        Sisa waktu sebelum memasuki masa toleransi:
                                    </p>

                                    <p
                                        class="text-[#085041] text-2xl font-bold tabular-nums"
                                        data-countdown-target="{{ $countdownTarget->toIso8601String() }}"
                                    >
                                        Menghitung...
                                    </p>
                                </div>

                                <div class="space-y-3 mt-4">
                                    <div class="flex items-start justify-between gap-4 text-sm">
                                        <span class="text-gray-500">
                                            Batas Kembali
                                        </span>

                                        <span class="font-semibold text-[#00372c] text-right">
                                            {{ $batasKembali->translatedFormat('d M Y, H:i') }} WIB
                                        </span>
                                    </div>

                                    <div class="flex items-start justify-between gap-4 text-sm">
                                        <span class="text-gray-500">
                                            Batas Toleransi
                                        </span>

                                        <span class="font-semibold text-[#00372c] text-right">
                                            {{ $batasToleransi->translatedFormat('d M Y, H:i') }} WIB
                                        </span>
                                    </div>
                                </div>

                            @elseif($statusWaktu === 'toleransi')
                                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                    <p class="text-amber-700 font-bold text-sm mb-1">
                                        Masa Toleransi {{ $jamToleransi }} Jam
                                    </p>

                                    <p class="text-amber-700/70 text-sm mb-3">
                                        Waktu sewa sudah habis, tetapi customer belum dikenakan denda.
                                    </p>

                                    <p
                                        class="text-amber-700 text-2xl font-bold tabular-nums"
                                        data-countdown-target="{{ $countdownTarget->toIso8601String() }}"
                                    >
                                        Menghitung...
                                    </p>

                                    <p class="text-amber-700/70 text-xs mt-2">
                                        Denda mulai dihitung setelah masa toleransi berakhir.
                                    </p>
                                </div>

                                <div class="flex items-start justify-between gap-4 text-sm mt-4">
                                    <span class="text-gray-500">
                                        Batas Toleransi
                                    </span>

                                    <span class="font-semibold text-[#00372c] text-right">
                                        {{ $batasToleransi->translatedFormat('d M Y, H:i') }} WIB
                                    </span>
                                </div>

                            @else
                                <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                                    <p class="text-red-600 font-bold text-sm mb-2">
                                        Terlambat Setelah Toleransi
                                    </p>

                                    <p class="text-red-500 text-sm">
                                        Durasi keterlambatan:

                                        <span class="font-semibold">
                                            @if($telatHari > 0)
                                                {{ $telatHari }} hari
                                            @endif

                                            @if($telatJam > 0)
                                                {{ $telatJam }} jam
                                            @endif

                                            @if($telatMenit > 0 || $menitTerlambat === 0)
                                                {{ $telatMenit }} menit
                                            @endif
                                        </span>
                                    </p>
                                </div>

                                <div class="space-y-3 mt-4">

                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <span class="text-gray-500">
                                            Tarif Denda
                                        </span>

                                        <span class="font-semibold text-[#00372c] text-right">
                                            Rp {{ number_format($dendaPerHari, 0, ',', '.') }} / 24 jam
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <span class="text-gray-500">
                                            Periode Denda
                                        </span>

                                        <span class="font-semibold text-[#00372c]">
                                            {{ $hariTerlambat }} periode
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between gap-4 text-sm">
                                        <span class="text-gray-500">
                                            Total Denda
                                        </span>

                                        <span class="font-bold text-red-600">
                                            Rp {{ number_format($estimasiDenda, 0, ',', '.') }}
                                        </span>
                                    </div>

                                </div>

                                <p class="text-xs text-gray-400 leading-relaxed mt-4">
                                    Denda sebesar 100% dari harga sewa harian dikenakan untuk setiap periode 24 jam keterlambatan yang telah dimulai.
                                </p>
                            @endif
                        </div>

                        <div class="p-5">
                            <p class="text-xs font-semibold text-[#00372c] uppercase tracking-wider mb-4">
                                Ringkasan Pembayaran
                            </p>

                            <div class="space-y-3">

                                <div class="flex items-center justify-between gap-4 text-sm">
                                    <span class="text-gray-500">
                                        Total Biaya Transaksi
                                    </span>

                                    <span class="font-semibold text-[#00372c]">
                                        Rp {{ number_format($totalBiayaTransaksi, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-4 text-sm">
                                    <span class="text-gray-500">
                                        Total Sudah Dibayar
                                    </span>

                                    <span class="font-semibold text-[#085041]">
                                        Rp {{ number_format($biayaSudahDibayar, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between gap-4 text-sm">
                                    <span class="text-gray-500">
                                        Sisa Tagihan
                                    </span>

                                    <span class="font-bold {{ $tagihanPengembalian > 0 ? 'text-red-600' : 'text-[#085041]' }}">
                                        Rp {{ number_format($tagihanPengembalian, 0, ',', '.') }}
                                    </span>
                                </div>

                            </div>

                            <div class="mt-5 pt-5 border-t border-gray-100">
                                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-2">
                                    Tagihan Saat Pengembalian
                                </p>

                                <p class="text-3xl font-bold {{ $tagihanPengembalian > 0 ? 'text-red-600' : 'text-[#085041]' }}">
                                    Rp {{ number_format($tagihanPengembalian, 0, ',', '.') }}
                                </p>

                                @if($tagihanPengembalian > 0)
                                    <p class="text-xs text-gray-400 mt-2 leading-relaxed">
                                        Tagihan ini berasal dari biaya keterlambatan yang harus dibayar saat barang dikembalikan.
                                    </p>
                                @else
                                    <p class="text-xs text-gray-400 mt-2 leading-relaxed">
                                        Tidak ada biaya tambahan yang perlu dibayar saat pengembalian.
                                    </p>
                                @endif
                            </div>
                        </div>

                    </div>

                    @if($wajibKonfirmasiPembayaranDenda)
                        <label class="block bg-white rounded-2xl border border-gray-100 shadow-sm p-4 cursor-pointer">
                            <div class="flex items-start gap-3">
                                <input
                                    type="checkbox"
                                    id="konfirmasiPembayaranDenda"
                                    name="konfirmasi_pembayaran_denda"
                                    value="1"
                                    class="mt-0.5 w-5 h-5 rounded border-gray-300 text-[#085041] focus:ring-[#085041]"
                                >

                                <div>
                                    <p class="text-sm font-semibold text-[#00372c]">
                                        Pembayaran denda sudah diterima
                                    </p>

                                    <p class="text-xs text-gray-500 leading-relaxed mt-1">
                                        Centang setelah customer membayar tagihan pengembalian sebesar
                                        <span class="font-semibold text-red-600">
                                            Rp {{ number_format($tagihanPengembalian, 0, ',', '.') }}
                                        </span>.
                                    </p>
                                </div>
                            </div>
                        </label>
                    @endif

                    <button
                        type="submit"
                        id="btnSelesai"
                        disabled
                        onclick="return confirm('Pastikan semua barang sudah diperiksa dan seluruh pembayaran sudah diterima. Selesaikan transaksi ini?')"
                        class="w-full inline-flex items-center justify-center gap-2 bg-gray-300 text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm cursor-not-allowed"
                    >
                        <svg
                            width="18"
                            height="18"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2.4"
                            viewBox="0 0 24 24"
                        >
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>

                        Selesaikan Transaksi
                    </button>

                    <p class="text-xs text-gray-400 leading-relaxed">
                        Tombol selesai akan aktif setelah semua barang dicentang
                        @if($wajibKonfirmasiPembayaranDenda)
                            dan pembayaran denda dikonfirmasi.
                        @else
                            .
                        @endif
                    </p>

                </div>

            </div>
        </form>
    </div>

    <script>
        const checkboxes = document.querySelectorAll('.return-check');
        const btnSelesai = document.getElementById('btnSelesai');
        const konfirmasiPembayaranDenda = document.getElementById(
            'konfirmasiPembayaranDenda'
        );

        const countdownElement = document.querySelector(
            '[data-countdown-target]'
        );

        function updateButton() {
            let semuaDicentang = checkboxes.length > 0;

            checkboxes.forEach(function (checkbox) {
                const item = checkbox.closest('.return-item');
                const checkUis = item.querySelectorAll('.check-ui');

                if (checkbox.checked) {
                    item.classList.remove('bg-white');

                    item.classList.add(
                        'bg-[#e8f5f0]',
                        'border-[#68dbae]'
                    );

                    checkUis.forEach(function (checkUi) {
                        checkUi.classList.remove(
                            'border-gray-300',
                            'bg-white'
                        );

                        checkUi.classList.add(
                            'border-[#085041]',
                            'bg-[#085041]'
                        );
                    });
                } else {
                    item.classList.add('bg-white');

                    item.classList.remove(
                        'bg-[#e8f5f0]',
                        'border-[#68dbae]'
                    );

                    checkUis.forEach(function (checkUi) {
                        checkUi.classList.add(
                            'border-gray-300',
                            'bg-white'
                        );

                        checkUi.classList.remove(
                            'border-[#085041]',
                            'bg-[#085041]'
                        );
                    });

                    semuaDicentang = false;
                }
            });

            const pembayaranDendaSiap =
                !konfirmasiPembayaranDenda
                || konfirmasiPembayaranDenda.checked;

            if (semuaDicentang && pembayaranDendaSiap) {
                btnSelesai.disabled = false;

                btnSelesai.classList.remove(
                    'bg-gray-300',
                    'cursor-not-allowed'
                );

                btnSelesai.classList.add(
                    'bg-[#085041]',
                    'hover:bg-[#00372c]',
                    'cursor-pointer'
                );
            } else {
                btnSelesai.disabled = true;

                btnSelesai.classList.add(
                    'bg-gray-300',
                    'cursor-not-allowed'
                );

                btnSelesai.classList.remove(
                    'bg-[#085041]',
                    'hover:bg-[#00372c]',
                    'cursor-pointer'
                );
            }
        }

        function padNumber(number) {
            return String(number).padStart(2, '0');
        }

        function startCountdown() {
            if (!countdownElement) {
                return;
            }

            const targetTimestamp = new Date(
                countdownElement.dataset.countdownTarget
            ).getTime();

            if (Number.isNaN(targetTimestamp)) {
                countdownElement.textContent = '-';

                return;
            }

            const updateCountdown = function () {
                const distance = targetTimestamp - Date.now();

                if (distance <= 0) {
                    countdownElement.textContent = '00:00:00';

                    setTimeout(function () {
                        window.location.reload();
                    }, 1000);

                    return false;
                }

                const totalSeconds = Math.floor(distance / 1000);
                const days = Math.floor(totalSeconds / 86400);

                const hours = Math.floor(
                    (totalSeconds % 86400) / 3600
                );

                const minutes = Math.floor(
                    (totalSeconds % 3600) / 60
                );

                const seconds = totalSeconds % 60;

                if (days > 0) {
                    countdownElement.textContent =
                        days +
                        ' hari ' +
                        padNumber(hours) +
                        ':' +
                        padNumber(minutes) +
                        ':' +
                        padNumber(seconds);
                } else {
                    countdownElement.textContent =
                        padNumber(hours) +
                        ':' +
                        padNumber(minutes) +
                        ':' +
                        padNumber(seconds);
                }

                return true;
            };

            if (!updateCountdown()) {
                return;
            }

            const interval = setInterval(function () {
                if (!updateCountdown()) {
                    clearInterval(interval);
                }
            }, 1000);
        }

        checkboxes.forEach(function (checkbox) {
            checkbox.addEventListener(
                'change',
                updateButton
            );
        });

        if (konfirmasiPembayaranDenda) {
            konfirmasiPembayaranDenda.addEventListener(
                'change',
                updateButton
            );
        }

        updateButton();
        startCountdown();
    </script>

@endsection
