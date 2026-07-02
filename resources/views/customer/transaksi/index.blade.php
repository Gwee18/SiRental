@extends('layouts.app')

@section('title', 'Transaksi Saya')

@section('content')
<section class="pt-32 pb-24 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-6">

        <div class="mb-10">
            <span class="inline-block bg-[#e8f5f0] text-[#085041] text-xs font-bold tracking-widest uppercase px-3 py-1.5 rounded-full mb-4">
                Riwayat Rental
            </span>

            <h1 class="text-3xl font-bold text-[#00372c] mb-2">
                Transaksi Saya
            </h1>

            <p class="text-gray-500 text-sm">
                Pantau status pengajuan, pembayaran, dan pengembalian alat rental kamu.
            </p>
        </div>

        @if($transaksi->isEmpty())
            <div class="bg-white rounded-3xl border border-gray-100 p-16 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-[#e8f5f0] flex items-center justify-center mx-auto mb-5">
                    <svg width="34" height="34" fill="none" stroke="#085041" stroke-width="1.7" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>

                <h2 class="text-xl font-bold text-[#00372c] mb-2">
                    Belum Ada Transaksi
                </h2>

                <p class="text-gray-500 text-sm mb-7">
                    Kamu belum pernah melakukan pengajuan sewa alat pendakian.
                </p>

                <a href="{{ route('rental.index') }}" class="inline-flex items-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                    Mulai Rental
                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M5 12h14"/>
                        <path d="M12 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @else
            <div class="space-y-5">
                @foreach($transaksi as $item)
                    @php
                        $statusText = match($item->status) {
                            'menunggu' => 'Menunggu Konfirmasi',
                            'disetujui', 'aktif' => 'Sedang Disewa',
                            'ditolak' => 'Ditolak',
                            'selesai' => 'Selesai',
                            default => ucfirst($item->status),
                        };

                        $statusStyle = match($item->status) {
                            'menunggu' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                            'disetujui', 'aktif' => 'bg-[#e8f5f0] text-[#085041] border-[#bcebd8]',
                            'ditolak' => 'bg-red-50 text-red-600 border-red-100',
                            'selesai' => 'bg-gray-100 text-gray-600 border-gray-200',
                            default => 'bg-gray-100 text-gray-600 border-gray-200',
                        };

                        $statusIcon = match($item->status) {
                            'menunggu' => '<path d="M12 6v6l4 2"/><circle cx="12" cy="12" r="9"/>',
                            'disetujui', 'aktif' => '<path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/>',
                            'ditolak' => '<circle cx="12" cy="12" r="9"/><path d="M15 9l-6 6"/><path d="M9 9l6 6"/>',
                            'selesai' => '<path d="M20 6L9 17l-5-5"/>',
                            default => '<circle cx="12" cy="12" r="9"/>',
                        };

                        $detailPertama = $item->detailTransaksi->first();
                        $totalBayar = $item->total_harga + $item->total_denda;
                    @endphp

                    <a href="{{ route('customer.transaksi.show', $item->id) }}" class="block bg-white rounded-3xl border border-gray-100 hover:shadow-lg hover:-translate-y-0.5 transition-all p-6">
                        <div class="flex flex-wrap items-start justify-between gap-5">

                            <div class="flex gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-[#e8f5f0] flex items-center justify-center shrink-0">
                                    <svg width="24" height="24" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                        <polyline points="3.29 7 12 12 20.71 7"/>
                                        <line x1="12" y1="22" x2="12" y2="12"/>
                                    </svg>
                                </div>

                                <div>
                                    <p class="text-xs text-gray-400 mb-1">
                                        {{ $item->kode_transaksi }} &middot; {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                                    </p>

                                    @if($detailPertama)
                                        <h2 class="font-bold text-[#00372c] text-base">
                                            {{ $detailPertama->alat->nama_alat ?? 'Alat tidak ditemukan' }}

                                            @if($item->detailTransaksi->count() > 1)
                                                <span class="text-gray-400 font-normal text-sm">
                                                    + {{ $item->detailTransaksi->count() - 1 }} alat lainnya
                                                </span>
                                            @endif
                                        </h2>

                                        <p class="text-gray-500 text-xs mt-1">
                                            Lama sewa: {{ $detailPertama->lama_sewa }} hari
                                        </p>
                                    @else
                                        <h2 class="font-bold text-[#00372c] text-base">
                                            Detail alat tidak tersedia
                                        </h2>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right">
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-xl border {{ $statusStyle }}">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        {!! $statusIcon !!}
                                    </svg>
                                    {{ $statusText }}
                                </span>

                                <p class="text-[#085041] font-bold text-base mt-3">
                                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                </p>

                                @if($item->total_denda > 0)
                                    <p class="text-red-500 text-xs mt-0.5">
                                        Termasuk denda Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </div>
</section>
@endsection