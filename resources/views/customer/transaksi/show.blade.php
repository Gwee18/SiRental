@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<section class="pt-32 pb-24 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-6">

        <a href="{{ route('customer.transaksi.index') }}" class="inline-flex items-center gap-1 text-[#085041] hover:text-[#00372c] text-sm font-semibold mb-6 transition-colors">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke Transaksi Saya
        </a>

        @php
            $statusStyle = match($transaksi->status) {
                'menunggu' => 'bg-yellow-50 text-yellow-600',
                'disetujui', 'aktif' => 'bg-[#e8f5f0] text-[#085041]',
                'ditolak' => 'bg-red-50 text-red-500',
                'selesai' => 'bg-gray-100 text-gray-500',
                default => 'bg-gray-100 text-gray-500',
            };
        @endphp

        <div class="bg-white rounded-2xl border border-gray-100 p-8">

            <div class="flex flex-wrap items-start justify-between gap-4 mb-8 pb-6 border-b border-gray-100">
                <div>
                    <h1 class="text-2xl font-bold text-[#00372c] mb-1">{{ $transaksi->kode_transaksi }}</h1>
                    <p class="text-gray-400 text-sm">Dipesan {{ $transaksi->tanggal_pesan->translatedFormat('d F Y') }}</p>
                </div>
                <span class="inline-block text-xs font-semibold px-3 py-1.5 rounded-lg {{ $statusStyle }}">
                    {{ ucfirst($transaksi->status) }}
                </span>
            </div>

            {{-- Periode Sewa --}}
            @if($transaksi->tanggal_mulai && $transaksi->tanggal_selesai)
            <div class="flex items-center gap-3 mb-8 bg-[#e8f5f0] rounded-xl px-4 py-3.5">
                <svg width="20" height="20" fill="none" stroke="#085041" stroke-width="2" viewBox="0 0 24 24" class="shrink-0">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                <p class="text-[#00372c] text-sm font-medium">
                    {{ $transaksi->tanggal_mulai->translatedFormat('d M Y') }} &mdash; {{ $transaksi->tanggal_selesai->translatedFormat('d M Y') }}
                </p>
            </div>
            @endif

            <h2 class="font-semibold text-[#00372c] text-sm uppercase tracking-wide mb-4">Alat yang Disewa</h2>
            <div class="space-y-3 mb-8">
                @forelse($transaksi->detailTransaksi as $detail)
                    <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                        <div>
                            <p class="font-medium text-[#00372c] text-sm">{{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}</p>
                            <p class="text-gray-400 text-xs">
                                {{ $detail->jumlah }} unit &middot; {{ $detail->lama_sewa }} hari &middot; Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}/hari
                            </p>
                        </div>
                        <p class="text-[#085041] font-semibold text-sm">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                    </div>
                @empty
                    <p class="text-gray-400 text-sm">Tidak ada detail alat untuk transaksi ini.</p>
                @endforelse
            </div>

            @if($transaksi->catatan)
            <div class="mb-8 bg-gray-50 rounded-xl px-4 py-3.5">
                <p class="text-xs text-gray-400 uppercase font-semibold mb-1">Catatan</p>
                <p class="text-gray-600 text-sm">{{ $transaksi->catatan }}</p>
            </div>
            @endif

            <div class="space-y-2 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between">
                    <span class="text-gray-500 text-sm">Subtotal Sewa</span>
                    <span class="text-[#00372c] text-sm font-medium">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                </div>
                @if($transaksi->total_denda > 0)
                <div class="flex items-center justify-between">
                    <span class="text-red-500 text-sm">Denda Keterlambatan</span>
                    <span class="text-red-500 text-sm font-medium">Rp {{ number_format($transaksi->total_denda, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <span class="font-semibold text-[#00372c] text-sm">Total Pembayaran</span>
                    <span class="font-bold text-[#085041] text-lg">Rp {{ number_format($transaksi->total_harga + $transaksi->total_denda, 0, ',', '.') }}</span>
                </div>
            </div>

        </div>
    </div>
</section>
@endsection