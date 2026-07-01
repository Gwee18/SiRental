@extends('layouts.admin')

@section('title', 'Detail Pelanggan')
@section('page-title', 'Detail Pelanggan')

@section('content')

    <div class="max-w-3xl">

        <a href="{{ route('admin.pelanggan.index') }}" class="inline-flex items-center gap-1 text-[#085041] hover:text-[#00372c] text-sm font-semibold mb-6 transition-colors">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Data Pelanggan
        </a>

        {{-- Profil Pelanggan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-4">
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                <img src="{{ $pelanggan->avatar_url }}" alt="{{ $pelanggan->nama_lengkap }}" class="w-16 h-16 rounded-full object-cover border-2 border-[#e8f5f0]">
                <div>
                    <h2 class="text-xl font-bold text-[#00372c]">{{ $pelanggan->nama_lengkap }}</h2>
                    <p class="text-gray-400 text-sm">{{ $pelanggan->email }}</p>
                    <div class="mt-1">
                        @if($pelanggan->google_id)
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600">
                                <span class="material-symbols-outlined text-sm">account_circle</span>
                                Daftar via Google
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">
                                <span class="material-symbols-outlined text-sm">email</span>
                                Daftar via Email
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-400 text-xs mb-1">No. Telepon</p>
                    <p class="font-semibold text-[#00372c]">{{ $pelanggan->no_telp ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Alamat</p>
                    <p class="font-semibold text-[#00372c]">{{ $pelanggan->alamat ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Bergabung Sejak</p>
                    <p class="font-semibold text-[#00372c]">{{ $pelanggan->created_at->translatedFormat('d F Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-xs mb-1">Total Transaksi</p>
                    <p class="font-semibold text-[#00372c]">{{ $pelanggan->transaksi->count() }} transaksi</p>
                </div>
            </div>

            @if($pelanggan->foto_ktp)
            <div class="mt-6 pt-6 border-t border-gray-100">
                <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-3">Foto KTP</p>
                <img src="{{ Storage::url($pelanggan->foto_ktp) }}" alt="KTP {{ $pelanggan->nama_lengkap }}" class="w-64 h-auto rounded-xl border border-gray-100">
            </div>
            @endif
        </div>

        {{-- Riwayat Transaksi --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="font-bold text-[#00372c] text-base">Riwayat Transaksi</h3>
            </div>
            @if($pelanggan->transaksi->isEmpty())
                <div class="px-6 py-12 text-center text-gray-400 text-sm">Belum ada transaksi.</div>
            @else
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 font-semibold text-xs text-gray-400 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-3 font-semibold text-xs text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">Total</th>
                            <th class="px-6 py-3 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($pelanggan->transaksi as $trx)
                        @php
                            $statusStyle = match($trx->status) {
                                'menunggu' => 'bg-yellow-50 text-yellow-600',
                                'disetujui', 'aktif' => 'bg-[#e8f5f0] text-[#085041]',
                                'ditolak' => 'bg-red-50 text-red-500',
                                'selesai' => 'bg-gray-100 text-gray-500',
                                default => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-3.5 font-semibold text-[#00372c]">
                                <a href="{{ route('admin.transaksi.show', $trx->id) }}" class="hover:underline">{{ $trx->kode_transaksi }}</a>
                            </td>
                            <td class="px-6 py-3.5">
                                <span class="inline-flex text-xs font-semibold px-3 py-1 rounded-full {{ $statusStyle }}">{{ ucfirst($trx->status) }}</span>
                            </td>
                            <td class="px-6 py-3.5 text-right font-semibold text-[#00372c]">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-3.5 text-right text-gray-400 text-xs">{{ $trx->created_at->translatedFormat('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

    </div>

@endsection