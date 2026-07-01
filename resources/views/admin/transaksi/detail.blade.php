@extends('layouts.admin')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')

    @if(session('success'))
        <div class="mb-6 bg-[#e8f5f0] text-[#085041] text-sm font-medium px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-3xl">

        <a href="{{ route('admin.transaksi.index') }}" class="inline-flex items-center gap-1 text-[#085041] hover:text-[#00372c] text-sm font-semibold mb-6 transition-colors">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Daftar Transaksi
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

        {{-- Header --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4">
            <div class="flex flex-wrap items-start justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
                <div>
                    <h2 class="text-xl font-bold text-[#00372c] mb-1">{{ $transaksi->kode_transaksi }}</h2>
                    <p class="text-gray-400 text-sm">Dipesan {{ $transaksi->created_at->translatedFormat('d F Y, H:i') }}</p>
                </div>
                <span class="inline-flex text-xs font-semibold px-3 py-1.5 rounded-full {{ $statusStyle }}">
                    {{ ucfirst($transaksi->status) }}
                </span>
            </div>

            {{-- Info Pelanggan --}}
            <div class="mb-6">
                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-3">Data Pelanggan</p>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-400 text-xs">Nama</p>
                        <p class="font-semibold text-[#00372c]">{{ $transaksi->customer->nama_lengkap ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs">Email</p>
                        <p class="font-semibold text-[#00372c]">{{ $transaksi->customer->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs">No. Telepon</p>
                        <p class="font-semibold text-[#00372c]">{{ $transaksi->customer->no_telp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-xs">Alamat</p>
                        <p class="font-semibold text-[#00372c]">{{ $transaksi->customer->alamat ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Periode Sewa --}}
            @if($transaksi->tanggal_mulai && $transaksi->tanggal_selesai)
            <div class="bg-[#e8f5f0] rounded-xl px-4 py-3 flex items-center gap-3 mb-6">
                <span class="material-symbols-outlined text-[#085041]">calendar_today</span>
                <p class="text-[#00372c] text-sm font-medium">
                    {{ \Carbon\Carbon::parse($transaksi->tanggal_mulai)->translatedFormat('d M Y') }}
                    &mdash;
                    {{ \Carbon\Carbon::parse($transaksi->tanggal_selesai)->translatedFormat('d M Y') }}
                </p>
            </div>
            @endif

            {{-- Detail Alat --}}
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-3">Alat yang Disewa</p>
            <div class="space-y-3 mb-6">
                @foreach($transaksi->detailTransaksi as $detail)
                <div class="flex items-center justify-between bg-gray-50 rounded-xl px-4 py-3">
                    <div class="flex items-center gap-3">
                        @if($detail->alat->foto_alat ?? false)
                            <img src="{{ Storage::url($detail->alat->foto_alat) }}" class="w-12 h-12 rounded-lg object-cover">
                        @else
                            <div class="w-12 h-12 rounded-lg bg-[#e8f5f0] flex items-center justify-center">
                                <span class="material-symbols-outlined text-[#085041] opacity-50">inventory_2</span>
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-[#00372c] text-sm">{{ $detail->alat->nama_alat ?? '-' }}</p>
                            <p class="text-gray-400 text-xs">{{ $detail->jumlah }} unit &middot; {{ $detail->lama_sewa }} hari &middot; Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}/hari</p>
                        </div>
                    </div>
                    <p class="font-bold text-[#085041] text-sm">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</p>
                </div>
                @endforeach
            </div>

            {{-- Foto Barang Customer --}}
            @if($transaksi->detailTransaksi->first()->foto_barang ?? false)
            <div class="mb-6">
                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-3">Foto Barang (dari Customer)</p>
                <img src="{{ Storage::url($transaksi->detailTransaksi->first()->foto_barang) }}" class="w-40 h-40 object-cover rounded-xl border border-gray-100">
            </div>
            @endif

            {{-- Catatan --}}
            @if($transaksi->catatan)
            <div class="bg-gray-50 rounded-xl px-4 py-3 mb-6">
                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-1">Catatan</p>
                <p class="text-gray-600 text-sm">{{ $transaksi->catatan }}</p>
            </div>
            @endif

            {{-- Total --}}
            <div class="space-y-2 pt-4 border-t border-gray-100">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Total Sewa</span>
                    <span class="font-semibold text-[#00372c]">Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</span>
                </div>
                @if($transaksi->total_denda > 0)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-red-500">Denda Keterlambatan</span>
                    <span class="font-semibold text-red-500">Rp {{ number_format($transaksi->total_denda, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                    <span class="font-bold text-[#00372c] text-sm">Total Pembayaran</span>
                    <span class="font-bold text-[#085041] text-lg">Rp {{ number_format($transaksi->total_harga + $transaksi->total_denda, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- TOMBOL AKSI --}}
        @if($transaksi->status === 'menunggu')
        <div class="flex gap-3">
            <form method="POST" action="{{ route('admin.transaksi.approve', $transaksi->id) }}">
                @csrf
                <button type="submit" class="flex items-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                    <span class="material-symbols-outlined text-base">check_circle</span>
                    Setujui Pesanan
                </button>
            </form>
            <form method="POST" action="{{ route('admin.transaksi.tolak', $transaksi->id) }}" onsubmit="return confirm('Yakin ingin menolak pesanan ini? Stok akan dikembalikan.')">
                @csrf
                <button type="submit" class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                    <span class="material-symbols-outlined text-base">cancel</span>
                    Tolak Pesanan
                </button>
            </form>
        </div>
        @elseif($transaksi->status === 'aktif')
        <form method="POST" action="{{ route('admin.transaksi.selesai', $transaksi->id) }}" onsubmit="return confirm('Tandai transaksi ini sebagai selesai? Sistem akan menghitung denda jika ada keterlambatan.')">
            @csrf
            <button type="submit" class="flex items-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                <span class="material-symbols-outlined text-base">task_alt</span>
                Tandai Selesai & Hitung Denda
            </button>
        </form>
        @endif

    </div>

@endsection