@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<section class="pt-32 pb-24 bg-[#f6f8f7] min-h-screen">
    <div class="max-w-6xl mx-auto px-6">

        <a href="{{ route('customer.transaksi.index') }}"
           class="inline-flex items-center gap-2 text-[#085041] hover:text-[#00372c] text-sm font-semibold mb-8 transition-colors">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
            Kembali ke Transaksi Saya
        </a>

        @php
            $statusText = match($transaksi->status) {
                'menunggu' => 'Menunggu Konfirmasi',
                'disetujui', 'aktif' => 'Sedang Disewa',
                'ditolak' => 'Ditolak',
                'selesai' => 'Selesai',
                default => ucfirst($transaksi->status),
            };

            $statusBadge = match($transaksi->status) {
                'menunggu' => 'bg-amber-50 text-amber-700 border border-amber-200',
                'disetujui', 'aktif' => 'bg-[#e8f5f0] text-[#085041] border border-[#bcebd8]',
                'ditolak' => 'bg-red-50 text-red-600 border border-red-200',
                'selesai' => 'bg-slate-100 text-slate-600 border border-slate-200',
                default => 'bg-slate-100 text-slate-600 border border-slate-200',
            };

            $statusIcon = match($transaksi->status) {
                'menunggu' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
                'disetujui', 'aktif' => '<circle cx="12" cy="12" r="9"/><path d="M8.5 12.5l2.2 2.2L15.5 10"/>',
                'ditolak' => '<circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6"/><path d="M9 9l6 6"/>',
                'selesai' => '<path d="M20 6L9 17l-5-5"/>',
                default => '<circle cx="12" cy="12" r="9"/>',
            };

            $grandTotal = $transaksi->total_harga + $transaksi->total_denda;
            $detailPertama = $transaksi->detailTransaksi->first();
            $lamaSewa = $detailPertama->lama_sewa ?? 1;
        @endphp

        <div class="mb-8">
            <h1 class="text-4xl font-bold text-[#1f2937] mb-8">
                Detail Transaksi
            </h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-6 lg:gap-y-0 lg:gap-x-8 max-w-4xl">

                <div class="lg:pr-6 lg:border-r lg:border-slate-200">
                    <p class="text-[11px] uppercase tracking-[0.18em] font-bold text-slate-400 mb-2">
                        Status
                    </p>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold {{ $statusBadge }}">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            {!! $statusIcon !!}
                        </svg>
                        {{ $statusText }}
                    </div>
                </div>

                <div class="lg:px-6 lg:border-r lg:border-slate-200">
                    <p class="text-[11px] uppercase tracking-[0.18em] font-bold text-slate-400 mb-2">
                        Tanggal Sewa
                    </p>
                    <p class="text-[20px] font-bold text-[#1f2937] leading-tight whitespace-nowrap">
                        @if($transaksi->tanggal_mulai && $transaksi->tanggal_selesai)
                            {{ $transaksi->tanggal_mulai->translatedFormat('d M') }}
                            -
                            {{ $transaksi->tanggal_selesai->translatedFormat('d M Y') }}
                        @else
                            -
                        @endif
                    </p>
                </div>

                <div class="lg:pl-6">
                    <p class="text-[11px] uppercase tracking-[0.18em] font-bold text-slate-400 mb-2">
                        Sisa Waktu
                    </p>
                    <p id="countdown" class="text-[32px] font-bold text-[#ef4444] leading-none whitespace-nowrap">
                        @if($transaksi->status === 'aktif' || $transaksi->status === 'disetujui')
                            00:00:00
                        @else
                            -
                        @endif
                    </p>
                </div>

            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

            {{-- KOLOM KIRI --}}
            <div class="xl:col-span-8 space-y-6">

                {{-- ALAT YANG DISEWA --}}
                <div class="bg-white border border-[#e5ebe7] rounded-2xl overflow-hidden shadow-sm">
                    <div class="flex items-center justify-between px-6 py-5 border-b border-[#edf1ef]">
                        <h2 class="text-[22px] font-semibold text-[#1f2937]">
                            Alat yang Disewa
                        </h2>
                        <span class="text-sm text-slate-500">
                            {{ $transaksi->detailTransaksi->count() }} item
                        </span>
                    </div>

                    <div class="p-5 space-y-4">
                        @forelse($transaksi->detailTransaksi as $detail)
                            <div class="border border-[#e8eeea] rounded-2xl p-5">
                                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                                    <div class="flex items-start gap-4">
                                        <div class="w-24 h-24 rounded-2xl bg-[#f3f6f4] overflow-hidden flex items-center justify-center shrink-0">
                                            @if($detail->foto_barang)
                                                <img
                                                    src="{{ asset('storage/' . $detail->foto_barang) }}"
                                                    alt="{{ $detail->alat->nama_alat ?? 'Foto Barang' }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @elseif($detail->alat && $detail->alat->foto_alat)
                                                <img
                                                    src="{{ asset('storage/' . $detail->alat->foto_alat) }}"
                                                    alt="{{ $detail->alat->nama_alat ?? 'Foto Barang' }}"
                                                    class="w-full h-full object-cover"
                                                >
                                            @else
                                                <svg width="34" height="34" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4A2 2 0 0 0 21 16z"/>
                                                    <path d="M3.3 7L12 12l8.7-5"/>
                                                    <path d="M12 22V12"/>
                                                </svg>
                                            @endif
                                        </div>

                                        <div class="pt-1">
                                            <h3 class="text-[26px] font-semibold text-[#1f2937] leading-tight mb-2">
                                                {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                            </h3>

                                            <div class="flex items-center gap-2 text-slate-500 text-[15px] mb-4">
                                                <svg width="16" height="16" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24">
                                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                                    <path d="M8 2v4"/>
                                                    <path d="M16 2v4"/>
                                                    <path d="M3 10h18"/>
                                                </svg>
                                                Durasi: {{ $detail->lama_sewa }} hari
                                            </div>

                                            <div class="flex flex-wrap gap-x-8 gap-y-2 text-[15px]">
                                                <div>
                                                    <span class="text-slate-500">Jumlah:</span>
                                                    <span class="font-semibold text-[#1f2937]"> {{ $detail->jumlah }} unit</span>
                                                </div>
                                                <div>
                                                    <span class="text-slate-500">Harga/Hari:</span>
                                                    <span class="font-semibold text-[#1f2937]"> Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="lg:text-right min-w-[170px]">
                                        <p class="text-xs uppercase tracking-[0.18em] font-bold text-slate-400 mb-2">
                                            Subtotal
                                        </p>
                                        <p class="text-[36px] font-bold text-[#1f2937] leading-none">
                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </p>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="rounded-2xl border border-dashed border-slate-200 p-10 text-center text-slate-400 text-sm">
                                Tidak ada detail alat untuk transaksi ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- KODE PENGEMBALIAN --}}
                @if($transaksi->status === 'aktif' || $transaksi->status === 'disetujui')
                    <div class="bg-white border border-[#e5ebe7] rounded-2xl p-6 shadow-sm">
                        <div class="max-w-xl">
                            <div class="flex items-center gap-2 text-slate-500 mb-3">
                                <svg width="16" height="16" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <path d="M14 2v6h6"/>
                                </svg>
                                <span class="text-xs uppercase tracking-[0.18em] font-bold">
                                    Kode Pengembalian
                                </span>
                            </div>

                            <p class="text-[28px] font-bold text-[#085041] mb-3">
                                {{ $transaksi->kode_transaksi }}
                            </p>

                            <p class="text-[15px] leading-7 text-slate-500">
                                Tunjukkan kode ini kepada admin saat mengembalikan barang.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- STATUS TAMBAHAN UNTUK MENUNGGU / DITOLAK / SELESAI --}}
                @if($transaksi->status === 'menunggu')
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/80 flex items-center justify-center shrink-0">
                                <svg width="22" height="22" fill="none" stroke="#b45309" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="M12 7v5l3 2"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-amber-800 mb-2">
                                    Menunggu Pembayaran Tunai
                                </h3>
                                <p class="text-[15px] leading-7 text-amber-700">
                                    Silakan datang ke kasir SiRental untuk melakukan pembayaran tunai dan mengambil barang. Setelah pembayaran diterima, admin akan mengonfirmasi transaksi ini.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($transaksi->status === 'ditolak')
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/80 flex items-center justify-center shrink-0">
                                <svg width="22" height="22" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="M15 9l-6 6"/>
                                    <path d="M9 9l6 6"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-red-700 mb-2">
                                    Transaksi Ditolak
                                </h3>
                                <p class="text-[15px] leading-7 text-red-600">
                                    Pengajuan rental ini ditolak oleh admin. Silakan buat pengajuan baru atau hubungi admin untuk informasi lebih lanjut.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($transaksi->status === 'selesai')
                    <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center shrink-0">
                                <svg width="22" height="22" fill="none" stroke="#475569" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-700 mb-2">
                                    Transaksi Selesai
                                </h3>
                                <p class="text-[15px] leading-7 text-slate-600">
                                    Barang sudah dikembalikan dan transaksi telah diselesaikan.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- KOLOM KANAN --}}
            <div class="xl:col-span-4">
                <div class="bg-white border border-[#e5ebe7] rounded-2xl overflow-hidden shadow-sm sticky top-28">
                    <div class="bg-[#004d40] px-6 py-5">
                        <h2 class="text-xl font-semibold text-white">
                            Ringkasan Pembayaran
                        </h2>
                    </div>

                    <div class="p-6">
                        <div class="space-y-5 text-[15px]">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-500">Harga Sewa</span>
                                <span class="font-semibold text-[#1f2937]">
                                    Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-500">Biaya Layanan</span>
                                <span class="font-semibold text-emerald-500">
                                    Gratis
                                </span>
                            </div>

                            @if($transaksi->total_denda > 0)
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-slate-500">Denda</span>
                                    <span class="font-semibold text-red-500">
                                        Rp {{ number_format($transaksi->total_denda, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif

                            <div class="pt-4 border-t border-dashed border-slate-300">
                                <div class="flex items-center justify-between gap-4 mb-3">
                                    <span class="text-slate-500">Metode Pembayaran</span>
                                    <span class="inline-flex items-center gap-2 font-semibold text-[#1f2937]">
                                        <svg width="18" height="18" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24">
                                            <rect x="3" y="6" width="18" height="12" rx="2"/>
                                            <circle cx="12" cy="12" r="2.5"/>
                                            <path d="M7 9h.01"/>
                                            <path d="M17 15h.01"/>
                                        </svg>
                                        Cash
                                    </span>
                                </div>
                            </div>

                            <div class="pt-5 border-t border-slate-200">
                                <p class="text-xs uppercase tracking-[0.18em] font-bold text-slate-400 mb-2">
                                    Total Pembayaran
                                </p>
                                <p class="text-[34px] font-bold text-[#085041] leading-none">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </p>

                                @if($transaksi->status === 'selesai')
                                    <div class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#e8f5f0] text-[#085041] text-sm font-semibold">
                                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M20 6L9 17l-5-5"/>
                                        </svg>
                                        Lunas
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@if($transaksi->status === 'aktif' || $transaksi->status === 'disetujui')
<script>
    const countdownElement = document.getElementById('countdown');

    const startDate = new Date("{{ $transaksi->updated_at->toIso8601String() }}").getTime();
    const durationHours = {{ (int) $lamaSewa * 24 }};
    const targetDate = startDate + (durationHours * 60 * 60 * 1000);

    function pad(number) {
        return String(number).padStart(2, '0');
    }

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance <= 0) {
            countdownElement.innerHTML = "00:00:00";
            return;
        }

        const totalSeconds = Math.floor(distance / 1000);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;

        countdownElement.innerHTML = pad(hours) + ":" + pad(minutes) + ":" + pad(seconds);
    }

    updateCountdown();
    setInterval(updateCountdown, 1000);
</script>
@endif
@endsection