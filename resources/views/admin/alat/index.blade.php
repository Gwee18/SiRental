@extends('layouts.admin')

@section('title', 'Barang')
@section('page-title', 'Katalog Alat')

@section('header-actions')
    <a
        href="{{ route('admin.alat.create') }}"
        class="flex items-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all"
    >
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <path d="M12 5v14"/>
            <path d="M5 12h14"/>
        </svg>
        Tambah Alat Baru
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="mb-6 bg-[#e8f5f0] text-[#085041] text-sm font-medium px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-50 text-red-600 text-sm font-medium px-4 py-3 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    @if($alat->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <svg width="56" height="56" fill="none" stroke="#d1d5db" stroke-width="1.5" viewBox="0 0 24 24" class="mx-auto mb-4">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <path d="M3.3 7L12 12l8.7-5"/>
                <path d="M12 22V12"/>
            </svg>

            <p class="text-gray-500 text-sm mb-6">
                Belum ada alat yang terdaftar.
            </p>

            <a
                href="{{ route('admin.alat.create') }}"
                class="inline-flex items-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm"
            >
                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M12 5v14"/>
                    <path d="M5 12h14"/>
                </svg>
                Tambah Alat Pertama
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($alat as $item)
                @php
                    $jumlahSedangDisewa = max(
                        0,
                        $item->stok_total - $item->stok_tersedia
                    );
                @endphp

                <div class="bg-white rounded-2xl border {{ $item->is_active ? 'border-gray-100' : 'border-gray-200' }} overflow-hidden hover:shadow-md transition-all flex flex-col">

                    <div class="h-44 bg-[#e8f5f0] flex items-center justify-center relative overflow-hidden">
                        @if($item->foto_alat)
                            <img
                                src="{{ Storage::url($item->foto_alat) }}"
                                alt="{{ $item->nama_alat }}"
                                class="w-full h-full object-cover {{ $item->is_active ? '' : 'grayscale opacity-60' }}"
                            >
                        @else
                            <svg width="54" height="54" fill="none" stroke="#085041" stroke-width="1.5" viewBox="0 0 24 24" class="opacity-30">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                <path d="M3.3 7L12 12l8.7-5"/>
                                <path d="M12 22V12"/>
                            </svg>
                        @endif

                        <span class="absolute top-3 left-3 bg-white/90 backdrop-blur text-[#085041] text-xs font-semibold px-3 py-1 rounded-full">
                            {{ $item->kategori }}
                        </span>

                        @unless($item->is_active)
                            <span class="absolute top-3 right-3 bg-gray-700 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                Nonaktif
                            </span>
                        @endunless
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="font-bold text-[#00372c] text-base leading-snug">
                                {{ $item->nama_alat }}
                            </h3>

                            <div class="flex items-center gap-1 shrink-0">
                                <a
                                    href="{{ route('admin.alat.edit', $item->id) }}"
                                    class="p-1.5 text-gray-400 hover:text-[#085041] hover:bg-[#e8f5f0] rounded-lg transition-colors"
                                    title="Edit"
                                    aria-label="Edit {{ $item->nama_alat }}"
                                >
                                    <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                        <path d="M12 20h9"/>
                                        <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                                    </svg>
                                </a>

                                <form
                                    action="{{ route('admin.alat.toggle-status', $item->id) }}"
                                    method="POST"
                                    class="inline-flex"
                                    onsubmit="return confirm('{{ $item->is_active ? 'Nonaktifkan alat ini dari katalog customer?' : 'Aktifkan alat ini kembali?' }}')"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="p-1.5 {{ $item->is_active ? 'text-gray-400 hover:text-amber-600 hover:bg-amber-50' : 'text-gray-400 hover:text-[#085041] hover:bg-[#e8f5f0]' }} rounded-lg transition-colors"
                                        title="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        aria-label="{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }} {{ $item->nama_alat }}"
                                    >
                                        @if($item->is_active)
                                            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9"/>
                                                <path d="M8 8l8 8"/>
                                            </svg>
                                        @else
                                            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                                <path d="M20 6L9 17l-5-5"/>
                                            </svg>
                                        @endif
                                    </button>
                                </form>

                                @if($item->detail_transaksi_count === 0)
                                    <form
                                        action="{{ route('admin.alat.destroy', $item->id) }}"
                                        method="POST"
                                        class="inline-flex"
                                        onsubmit="return confirm('Alat ini belum memiliki riwayat transaksi. Hapus permanen alat ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Hapus permanen"
                                            aria-label="Hapus {{ $item->nama_alat }}"
                                        >
                                            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                                <path d="M3 6h18"/>
                                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                <path d="M10 11v6"/>
                                                <path d="M14 11v6"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>

                        <p class="text-[#085041] font-bold text-base mb-4">
                            Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}
                            <span class="text-gray-400 font-normal text-xs">/ hari</span>
                        </p>

                        <div class="mt-auto pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">
                                        Tersedia
                                    </p>

                                    <p class="font-bold text-[#00372c] text-sm">
                                        {{ $item->stok_tersedia }} / {{ $item->stok_total }}
                                    </p>

                                    @if($jumlahSedangDisewa > 0)
                                        <p class="text-[11px] text-gray-400 mt-1">
                                            {{ $jumlahSedangDisewa }} sedang disewa
                                        </p>
                                    @endif
                                </div>

                                @if(!$item->is_active)
                                    <span class="inline-flex items-center text-gray-600 bg-gray-100 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        Nonaktif
                                    </span>
                                @elseif($item->stok_tersedia > 0)
                                    <span class="inline-flex items-center gap-1.5 text-[#085041] bg-[#e8f5f0] text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                            <path d="M20 6L9 17l-5-5"/>
                                        </svg>
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-red-500 bg-red-50 text-xs font-semibold px-2.5 py-1 rounded-full">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="9"/>
                                            <path d="M12 7v6"/>
                                            <path d="M12 17h.01"/>
                                        </svg>
                                        Habis
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

@endsection
