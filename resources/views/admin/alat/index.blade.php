@extends('layouts.admin')

@section('title', 'Barang')
@section('page-title', 'Katalog Alat')

@section('header-actions')
    <a href="{{ route('admin.alat.create') }}" class="flex items-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all">
        <span class="material-symbols-outlined text-base">add</span>
        Tambah Alat Baru
    </a>
@endsection

@section('content')

    @if(session('success'))
        <div class="mb-6 bg-[#e8f5f0] text-[#085041] text-sm font-medium px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if($alat->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <span class="material-symbols-outlined text-5xl text-gray-300 mb-4 block">inventory_2</span>
            <p class="text-gray-500 text-sm mb-6">Belum ada alat yang terdaftar.</p>
            <a href="{{ route('admin.alat.create') }}" class="inline-block bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                Tambah Alat Pertama
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($alat as $item)
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden hover:shadow-md transition-all flex flex-col">

                <div class="h-44 bg-[#e8f5f0] flex items-center justify-center relative">
                    @if($item->foto_alat)
                        <img src="{{ Storage::url($item->foto_alat) }}" alt="{{ $item->nama_alat }}" class="w-full h-full object-cover">
                    @else
                        <span class="material-symbols-outlined text-5xl text-[#085041] opacity-30">inventory_2</span>
                    @endif
                    <span class="absolute top-3 left-3 bg-white/90 backdrop-blur text-[#085041] text-xs font-semibold px-3 py-1 rounded-full">
                        {{ $item->kategori }}
                    </span>
                </div>

                <div class="p-5 flex-1 flex flex-col">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="font-bold text-[#00372c] text-base leading-snug">{{ $item->nama_alat }}</h3>
                        <div class="flex gap-1 shrink-0">
                            <a href="{{ route('admin.alat.edit', $item->id) }}" class="p-1.5 text-gray-400 hover:text-[#085041] hover:bg-[#e8f5f0] rounded-lg transition-colors">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </a>
                            <form method="POST" action="{{ route('admin.alat.destroy', $item->id) }}" onsubmit="return confirm('Yakin ingin menghapus {{ $item->nama_alat }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="text-[#085041] font-bold text-base mb-4">
                        Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}
                        <span class="text-gray-400 font-normal text-xs">/ hari</span>
                    </p>

                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-semibold tracking-wider">Tersedia</p>
                            <p class="font-bold text-[#00372c] text-sm">{{ $item->stok_tersedia }} / {{ $item->stok_total }}</p>
                        </div>
                        @if($item->stok_tersedia > 0)
                            <span class="inline-flex items-center gap-1 text-[#085041] bg-[#e8f5f0] text-xs font-semibold px-2.5 py-1 rounded-full">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                Tersedia
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-red-500 bg-red-50 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <span class="material-symbols-outlined text-sm">error</span>
                                Habis
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

@endsection