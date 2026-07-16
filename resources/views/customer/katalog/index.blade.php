@extends('layouts.app')

@section('title', 'Katalog Alat')

@section('content')

<section class="pt-28 md:pt-32 pb-20 bg-[#f7f9f8] min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        <div class="mb-8 md:mb-10">
            <a
                href="{{ route('home') }}"
                class="inline-flex items-center gap-2 text-[#085041] hover:text-[#00372c] text-sm font-semibold mb-5 md:mb-6 transition-colors"
            >
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 18l-6-6 6-6"/>
                </svg>
                Kembali ke Beranda
            </a>

            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-[#00372c] tracking-tight">
                        Semua Katalog Alat
                    </h1>

                    <p class="text-gray-500 text-sm md:text-base mt-3 max-w-2xl">
                        Pilih perlengkapan pendakian yang tersedia untuk kebutuhan rental Anda.
                    </p>
                </div>

                <p class="text-sm font-semibold text-[#085041]">
                    {{ $alat->total() }} alat tersedia
                </p>
            </div>
        </div>

        @if($alat->isEmpty())
            <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center">
                <svg class="w-14 h-14 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>

                <h2 class="text-lg font-bold text-[#00372c] mb-2">
                    Belum ada alat tersedia
                </h2>

                <p class="text-gray-500 text-sm">
                    Katalog alat akan muncul setelah admin menambahkan stok barang.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach($alat as $item)

                    <div class="sm:hidden bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="flex gap-4 p-3">

                            <div class="w-[104px] h-[104px] rounded-xl bg-[#f2f6f4] overflow-hidden flex items-center justify-center shrink-0">
                                @if($item->foto_alat)
                                    <img
                                        src="{{ Storage::url($item->foto_alat) }}"
                                        alt="{{ $item->nama_alat }}"
                                        class="w-full h-full object-cover"
                                    >
                                @else
                                    <svg width="38" height="38" fill="none" stroke="#085041" stroke-width="1.3" viewBox="0 0 24 24" opacity="0.35">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                        <polyline points="9 22 9 12 15 12 15 22"/>
                                    </svg>
                                @endif
                            </div>

                            <div class="min-w-0 flex-1 flex flex-col justify-between py-1">
                                <div>
                                    <p class="text-[10px] font-bold text-[#085041] uppercase tracking-wider mb-1">
                                        {{ $item->kategori }}
                                    </p>

                                    <h2 class="font-bold text-[#00372c] text-sm leading-snug line-clamp-2">
                                        {{ $item->nama_alat }}
                                    </h2>

                                    <p class="text-[#085041] font-bold text-sm mt-2">
                                        Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}
                                        <span class="text-gray-400 font-normal text-xs">
                                            / hari
                                        </span>
                                    </p>
                                </div>

                                <div class="flex items-center justify-start mt-3">
                                    <p class="text-[11px] font-semibold {{ $item->stok_tersedia > 0 ? 'text-[#085041]' : 'text-red-500' }}">
                                        {{ $item->stok_tersedia > 0 ? 'Stok: ' . $item->stok_tersedia : 'Habis' }}
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="hidden sm:block bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-md hover:-translate-y-1 transition-all duration-200">
                        <div class="h-48 bg-[#e8f5f0] flex items-center justify-center">
                            @if($item->foto_alat)
                                <img
                                    src="{{ Storage::url($item->foto_alat) }}"
                                    alt="{{ $item->nama_alat }}"
                                    class="w-full h-full object-cover"
                                >
                            @else
                                <svg width="56" height="56" fill="none" stroke="#085041" stroke-width="1.2" viewBox="0 0 24 24" opacity="0.4">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            @endif
                        </div>

                        <div class="p-5 space-y-3">
                            <div>
                                <span class="text-xs text-[#085041] font-semibold uppercase tracking-wide">
                                    {{ $item->kategori }}
                                </span>

                                <h2 class="font-semibold text-[#00372c] text-base mt-0.5">
                                    {{ $item->nama_alat }}
                                </h2>
                            </div>

                            <p class="text-[#085041] font-bold text-base">
                                Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}
                                <span class="text-gray-400 font-normal text-sm">
                                    / hari
                                </span>
                            </p>

                            <div class="pt-1">
                                <p class="text-xs font-semibold {{ $item->stok_tersedia > 0 ? 'text-[#085041]' : 'text-red-500' }}">
                                    {{ $item->stok_tersedia > 0 ? 'Stok: ' . $item->stok_tersedia : 'Habis' }}
                                </p>
                            </div>
                        </div>
                    </div>

                @endforeach
            </div>

            <div class="mt-10">
                {{ $alat->links() }}
            </div>
        @endif

    </div>
</section>

@endsection
