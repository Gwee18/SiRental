@extends('layouts.admin')

@section('title', 'Verifikasi Pengembalian')
@section('page-title', 'Verifikasi Pengembalian')

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

    <div class="max-w-4xl">

        <div class="mb-8">
            <p class="text-xs uppercase tracking-[0.22em] text-gray-400 font-bold mb-3">
                Pengembalian Barang
            </p>
            <h2 class="text-3xl font-bold text-[#00372c] mb-2">
                Cari Kode Transaksi
            </h2>
            <p class="text-gray-500 text-sm max-w-2xl leading-6">
                Masukkan kode pengembalian yang ditunjukkan customer untuk memulai proses pengecekan barang.
            </p>
        </div>

        <div class="bg-white rounded-[28px] border border-gray-100 shadow-sm p-8">
            <form method="POST" action="{{ route('admin.pengembalian.cari') }}">
                @csrf

                <label class="block text-sm font-bold text-[#00372c] mb-3">
                    Kode Transaksi
                </label>

                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="relative flex-1">
                        <div class="absolute left-5 top-1/2 -translate-y-1/2 text-[#085041]">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <path d="M14 2v6h6"/>
                                <path d="M8 13h8"/>
                                <path d="M8 17h5"/>
                            </svg>
                        </div>

                        <input
                            type="text"
                            name="kode_transaksi"
                            value="{{ old('kode_transaksi') }}"
                            placeholder="Contoh: SR-DSQBXL2P"
                            class="w-full h-[58px] rounded-2xl border border-gray-200 pl-14 pr-5 text-[#00372c] text-lg font-bold tracking-wide focus:border-[#085041] focus:ring-4 focus:ring-[#e8f5f0] outline-none transition-all uppercase"
                            autocomplete="off"
                            autofocus
                        >
                    </div>

                    <button type="submit" class="h-[58px] inline-flex items-center justify-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white font-bold px-7 rounded-2xl transition-all text-sm">
                        <svg width="19" height="19" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="M21 21l-4.35-4.35"/>
                        </svg>
                        Cari Transaksi
                    </button>
                </div>

                @error('kode_transaksi')
                    <p class="text-red-500 text-sm mt-3">
                        {{ $message }}
                    </p>
                @enderror
            </form>
        </div>

    </div>

@endsection
