@extends('layouts.app')

@section('title', 'Transaksi Saya')

@section('content')
<section class="pt-28 md:pt-32 pb-20 md:pb-24 bg-[#f6f8f7] min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">

        {{-- HEADER --}}
        <div class="mb-8 md:mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-[#00372c] mb-3 tracking-tight">
                Transaksi Saya
            </h1>

            <p class="text-gray-500 text-sm md:text-base max-w-2xl leading-relaxed">
                Pantau status pengajuan, pembayaran, dan pengembalian alat rental kamu dalam satu halaman.
            </p>
        </div>

        @if($transaksi->isEmpty())
            <div class="py-16 md:py-24">
                <div class="max-w-md mx-auto text-center px-4">

                    <div class="flex justify-center mb-5">
                        <svg width="44" height="44" fill="none" stroke="#9ca3af" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <path d="M9 13h6"/>
                            <path d="M9 17h4"/>
                        </svg>
                    </div>

                    <h2 class="text-2xl md:text-3xl font-bold text-gray-500 mb-3">
                        Belum Ada Transaksi
                    </h2>

                    <p class="text-gray-400 text-sm leading-relaxed mb-7">
                        Kamu belum pernah mengajukan rental alat pendakian.
                    </p>

                    <a
                        href="{{ route('rental.index') }}"
                        class="inline-flex items-center justify-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm min-w-[170px]"
                    >
                        Mulai Rental

                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 12h14"/>
                            <path d="M12 5l7 7-7 7"/>
                        </svg>
                    </a>

                </div>
            </div>
        @else
            <div class="space-y-3">

                {{-- DESKTOP HEADER --}}
                <div class="hidden md:grid grid-cols-[minmax(0,1.35fr)_110px_190px_145px_90px] gap-5 px-5 py-3 bg-white/70 border border-gray-100 rounded-2xl text-xs font-bold uppercase tracking-wider text-gray-400">
                    <div>Transaksi</div>
                    <div class="text-center">Lama</div>
                    <div>Status</div>
                    <div>Total</div>
                    <div>Aksi</div>
                </div>

                @foreach($transaksi as $item)
                    @php
                        $detailPertama = $item->detailTransaksi->first();
                        $lamaSewa = $detailPertama->lama_sewa ?? '-';

                        $totalTagihan = (int) $item->total_harga
                            + (int) $item->total_denda;

                        $totalDibayar = (int) $item->total_dibayar;
                        $sisaTagihan = max($totalTagihan - $totalDibayar, 0);

                        $statusText = match($item->status) {
                            'menunggu' => 'Menunggu Konfirmasi',
                            'disetujui', 'aktif' => 'Sedang Disewa',
                            'ditolak' => 'Ditolak',
                            'selesai' => 'Selesai',
                            default => ucfirst($item->status),
                        };

                        $statusTextColor = match($item->status) {
                            'menunggu' => 'text-yellow-700',
                            'disetujui', 'aktif' => 'text-[#085041]',
                            'ditolak' => 'text-red-600',
                            'selesai' => 'text-gray-500',
                            default => 'text-gray-500',
                        };

                        if ($item->status === 'ditolak') {
                            $totalTagihan = 0;
                            $totalDibayar = 0;
                            $sisaTagihan = 0;

                            $statusPembayaranText = 'Tidak Ada Tagihan';
                            $statusPembayaranColor = 'text-gray-400';
                        } else {
                            $statusPembayaranText = match($item->status_pembayaran) {
                                'belum_bayar' => 'Belum Dibayar',
                                'sewa_lunas' => 'Biaya Sewa Lunas',
                                'lunas' => 'Lunas',
                                default => 'Belum Dibayar',
                            };

                            $statusPembayaranColor = match($item->status_pembayaran) {
                                'belum_bayar' => 'text-amber-600',
                                'sewa_lunas', 'lunas' => 'text-[#085041]',
                                default => 'text-gray-500',
                            };
                        }
                    @endphp

                    <a
                        href="{{ route('customer.transaksi.show', $item->id) }}"
                        class="block bg-white rounded-2xl border border-gray-100 hover:shadow-md hover:-translate-y-0.5 transition-all p-4 md:p-0 overflow-hidden"
                    >

                        {{-- MOBILE --}}
                        <div class="md:hidden space-y-3">
                            <div>
                                <p class="text-[11px] text-gray-400 leading-relaxed mb-1">
                                    <span class="font-semibold text-gray-500">
                                        {{ $item->kode_transaksi }}
                                    </span>
                                    <br>
                                    {{ $item->created_at->translatedFormat('d M Y, H:i') }} WIB
                                </p>

                                <p class="text-gray-500 text-sm">
                                    Lama sewa: {{ $lamaSewa }} {{ is_numeric($lamaSewa) ? 'hari' : '' }}
                                </p>
                            </div>

                            <div class="border-t border-gray-100 pt-3 flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold {{ $statusTextColor }}">
                                        {{ $statusText }}
                                    </p>

                                    <p class="text-xs font-semibold {{ $statusPembayaranColor }} mt-1">
                                        {{ $statusPembayaranText }}
                                    </p>

                                    <div class="mt-3">
                                        <p class="text-[#085041] font-bold text-2xl leading-none">
                                            Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                        </p>

                                        @if($item->total_denda > 0)
                                            <p class="text-red-500 text-xs mt-1">
                                                Denda Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                                            </p>
                                        @endif

                                        @if($totalDibayar > 0)
                                            <p class="text-[#085041] text-xs mt-1">
                                                Dibayar Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                                            </p>
                                        @endif

                                        @if($sisaTagihan > 0)
                                            <p class="text-amber-600 text-xs mt-1">
                                                Sisa Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="shrink-0 self-end">
                                    <span class="inline-flex items-center gap-1 text-[#085041] text-xs font-semibold">
                                        Lihat Detail

                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M5 12h14"/>
                                            <path d="M12 5l7 7-7 7"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- DESKTOP --}}
                        <div class="hidden md:grid grid-cols-[minmax(0,1.35fr)_110px_190px_145px_90px] gap-5 items-center px-5 py-4">
                            <div class="min-w-0">
                                <h2 class="text-[#00372c] font-bold text-base truncate">
                                    {{ $item->kode_transaksi }}
                                </h2>

                                <p class="text-gray-400 text-xs mt-1">
                                    {{ $item->created_at->translatedFormat('d M Y, H:i') }} WIB
                                </p>
                            </div>

                            <div class="text-center">
                                <p class="text-sm font-semibold text-[#00372c]">
                                    {{ $lamaSewa }} {{ is_numeric($lamaSewa) ? 'hari' : '' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-sm font-bold {{ $statusTextColor }}">
                                    {{ $statusText }}
                                </p>

                                <p class="text-xs font-semibold {{ $statusPembayaranColor }} mt-1">
                                    {{ $statusPembayaranText }}
                                </p>
                            </div>

                            <div>
                                <p class="text-[#085041] font-bold text-xl leading-none whitespace-nowrap">
                                    Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                </p>

                                @if($item->total_denda > 0)
                                    <p class="text-red-500 text-xs mt-1">
                                        Denda Rp {{ number_format($item->total_denda, 0, ',', '.') }}
                                    </p>
                                @endif

                                @if($totalDibayar > 0)
                                    <p class="text-[#085041] text-xs mt-1">
                                        Dibayar Rp {{ number_format($totalDibayar, 0, ',', '.') }}
                                    </p>
                                @endif

                                @if($sisaTagihan > 0)
                                    <p class="text-amber-600 text-xs mt-1">
                                        Sisa Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>

                            <div>
                                <span class="inline-flex items-center gap-1 text-[#085041] text-sm font-semibold whitespace-nowrap">
                                    Detail

                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M5 12h14"/>
                                        <path d="M12 5l7 7-7 7"/>
                                    </svg>
                                </span>
                            </div>
                        </div>

                    </a>
                @endforeach
            </div>
        @endif

    </div>
</section>
@endsection
