@extends('layouts.admin')

@section('title', 'Konfirmasi Peminjaman')
@section('page-title', 'Konfirmasi Peminjaman')

@section('content')

    @if(session('success'))
        <div class="mb-6 bg-[#e8f5f0] text-[#085041] text-sm font-medium px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-bold text-[#00372c] text-base">
                Semua Transaksi
            </h2>
        </div>

        @if($transaksi->isEmpty())
            <div class="px-6 py-16 text-center text-gray-400 text-sm">
                <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="mx-auto mb-3 opacity-30">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <path d="M14 2v6h6"/>
                    <path d="M8 13h8"/>
                    <path d="M8 17h5"/>
                </svg>

                Belum ada transaksi.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                                Kode
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                                Pelanggan
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                                Alat
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">
                                Total
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-center">
                                Aksi
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaksi as $trx)
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
                                <td class="px-6 py-4 font-semibold text-[#00372c]">
                                    {{ $trx->kode_transaksi }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $trx->customer->nama_lengkap ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $trx->detailTransaksi->first()->alat->nama_alat ?? '-' }}

                                    @if($trx->detailTransaksi->count() > 1)
                                        <span class="text-gray-400 text-xs">
                                            +{{ $trx->detailTransaksi->count() - 1 }} lainnya
                                        </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-flex text-xs font-semibold px-3 py-1 rounded-full {{ $statusStyle }}">
                                        {{ ucfirst($trx->status) }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right font-semibold text-[#00372c]">
                                    Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.transaksi.show', $trx->id) }}"
                                       class="inline-flex items-center gap-1.5 text-[#085041] hover:text-[#00372c] text-xs font-semibold px-3 py-1.5 bg-[#e8f5f0] hover:bg-[#d8eee5] rounded-lg transition-colors">
                                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection