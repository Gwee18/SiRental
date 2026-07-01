@extends('layouts.app')

@section('title', 'Transaksi Saya')

@section('content')
<section class="pt-32 pb-24 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-6">

        <div class="mb-10">
            <h1 class="text-3xl font-bold text-[#00372c] mb-2">Transaksi Saya</h1>
            <p class="text-gray-500 text-sm">Riwayat dan status sewa alat pendakian kamu</p>
        </div>

        @if($transaksi->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
                <svg width="48" height="48" fill="none" stroke="#085041" stroke-width="1.2" viewBox="0 0 24 24" class="mx-auto mb-4 opacity-40">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <p class="text-gray-500 text-sm mb-6">Kamu belum pernah melakukan transaksi sewa.</p>
                <a href="{{ route('rental.index') }}" class="inline-block bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                    Mulai Rental
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($transaksi as $item)
                @php
                    $statusStyle = match($item->status) {
                        'menunggu' => 'bg-yellow-50 text-yellow-600',
                        'disetujui', 'aktif' => 'bg-[#e8f5f0] text-[#085041]',
                        'ditolak' => 'bg-red-50 text-red-500',
                        'selesai' => 'bg-gray-100 text-gray-500',
                        default => 'bg-gray-100 text-gray-500',
                    };
                @endphp
                <a href="{{ route('customer.transaksi.show', $item->id) }}" class="block bg-white rounded-2xl border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all p-6">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">
                                {{ $item->kode_transaksi }} &middot; {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                            </p>

                            @if($item->detailTransaksi->isNotEmpty())
                                <p class="font-semibold text-[#00372c] text-base">
                                    {{ $item->detailTransaksi->first()->alat->nama_alat ?? 'Alat' }}
                                    @if($item->detailTransaksi->count() > 1)
                                        <span class="text-gray-400 font-normal text-sm">+ {{ $item->detailTransaksi->count() - 1 }} alat lainnya</span>
                                    @endif
                                </p>
                            @else
                                <p class="font-semibold text-[#00372c] text-base">Detail alat tidak tersedia</p>
                            @endif

                            <p class="text-gray-400 text-xs mt-1">
                                Lama sewa: {{ $item->detailTransaksi->first()->lama_sewa ?? '-' }} hari
                            </p>
                        </div>

                        <div class="text-right">
                            <span class="inline-block text-xs font-semibold px-3 py-1.5 rounded-lg {{ $statusStyle }}">
                                {{ ucfirst($item->status) }}
                            </span>
                            <p class="text-[#085041] font-bold text-sm mt-2">
                                Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                            </p>
                            @if($item->total_denda > 0)
                                <p class="text-red-500 text-xs mt-0.5">
                                    + denda Rp {{ number_format($item->total_denda, 0, ',', '.') }}
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