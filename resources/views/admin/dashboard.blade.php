@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <svg width="24" height="24" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24" class="mb-5">
                <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>

            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">
                Pelanggan
            </p>

            <p class="text-3xl font-bold text-[#00372c]">
                {{ $totalCustomer }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <svg width="24" height="24" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24" class="mb-5">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                <path d="M3.3 7L12 12l8.7-5"/>
                <path d="M12 22V12"/>
            </svg>

            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">
                Total Alat
            </p>

            <p class="text-3xl font-bold text-[#00372c]">
                {{ $totalAlat }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <svg width="24" height="24" fill="none" stroke="#ca8a04" stroke-width="1.8" viewBox="0 0 24 24" class="mb-5">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v5l3 2"/>
            </svg>

            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">
                Menunggu Konfirmasi
            </p>

            <p class="text-3xl font-bold text-[#00372c]">
                {{ $transaksiMenunggu }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <svg width="24" height="24" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24" class="mb-5">
                <path d="M9 11l3 3L22 4"/>
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>

            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">
                Sewa Aktif
            </p>

            <p class="text-3xl font-bold text-[#00372c]">
                {{ $transaksiAktif }}
            </p>
        </div>

    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">

        <div class="bg-[#085041] rounded-2xl p-6 text-white">
            <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" class="text-white/80 mb-5">
                <rect x="3" y="6" width="18" height="12" rx="2"/>
                <circle cx="12" cy="12" r="2.5"/>
                <path d="M7 9h.01"/>
                <path d="M17 15h.01"/>
            </svg>

            <span class="text-white/70 text-xs font-semibold uppercase tracking-wider">
                Total Pendapatan Diterima
            </span>

            <p class="text-3xl font-bold mt-2">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </p>

            <p class="text-white/60 text-xs mt-2">
                Berdasarkan pembayaran yang sudah tercatat.
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <svg width="32" height="32" fill="none" stroke="#ef4444" stroke-width="1.8" viewBox="0 0 24 24" class="mb-5">
                <circle cx="12" cy="12" r="9"/>
                <path d="M12 7v6"/>
                <path d="M12 17h.01"/>
            </svg>

            <span class="text-gray-400 text-xs font-semibold uppercase tracking-wider">
                Total Denda Terkumpul
            </span>

            <p class="text-3xl font-bold text-red-500 mt-2">
                Rp {{ number_format($totalDenda, 0, ',', '.') }}
            </p>

            <p class="text-gray-400 text-xs mt-2">
                Sudah termasuk dalam total pendapatan.
            </p>
        </div>

    </div>

    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4">
            <h2 class="font-bold text-[#00372c] text-base">
                Transaksi Terbaru
            </h2>

            <a href="{{ route('admin.transaksi.index') }}" class="text-[#085041] hover:text-[#00372c] text-sm font-semibold transition-colors whitespace-nowrap">
                Lihat Semua
            </a>
        </div>

        @if($transaksiTerbaru->isEmpty())
            <div class="px-6 py-16 text-center text-gray-400 text-sm">
                <svg width="42" height="42" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="mx-auto mb-3 opacity-30">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <path d="M14 2v6h6"/>
                    <path d="M8 13h8"/>
                    <path d="M8 17h5"/>
                </svg>

                Belum ada transaksi.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                                Kode
                            </th>

                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                                Pelanggan
                            </th>

                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                                Status
                            </th>

                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">
                                Dibayar
                            </th>

                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">
                                Tanggal
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaksiTerbaru as $trx)
                            @php
                                $statusTextColor = match($trx->status) {
                                    'menunggu' => 'text-yellow-600',
                                    'disetujui', 'aktif' => 'text-[#085041]',
                                    'ditolak' => 'text-red-500',
                                    'selesai' => 'text-gray-500',
                                    default => 'text-gray-500',
                                };

                                $statusText = match($trx->status) {
                                    'menunggu' => 'Menunggu',
                                    'disetujui' => 'Disetujui',
                                    'aktif' => 'Aktif',
                                    'ditolak' => 'Ditolak',
                                    'selesai' => 'Selesai',
                                    default => ucfirst($trx->status),
                                };

                                $pembayaranText = match($trx->status_pembayaran) {
                                    'belum_bayar' => 'Belum Dibayar',
                                    'sewa_lunas' => 'Sewa Lunas',
                                    'lunas' => 'Lunas',
                                    default => '-',
                                };

                                $pembayaranColor = match($trx->status_pembayaran) {
                                    'belum_bayar' => 'text-yellow-600',
                                    'sewa_lunas', 'lunas' => 'text-[#085041]',
                                    default => 'text-gray-400',
                                };
                            @endphp

                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-[#00372c] text-sm">
                                    {{ $trx->kode_transaksi }}
                                </td>

                                <td class="px-6 py-4 text-gray-600 text-sm">
                                    {{ $trx->customer->nama_lengkap ?? '-' }}
                                </td>

                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold {{ $statusTextColor }}">
                                        {{ $statusText }}
                                    </p>

                                    <p class="text-xs mt-1 {{ $pembayaranColor }}">
                                        {{ $trx->status === 'ditolak' ? 'Tidak Ada Tagihan' : $pembayaranText }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 text-right font-semibold text-[#00372c] text-sm whitespace-nowrap">
                                    Rp {{ number_format($trx->status === 'ditolak' ? 0 : $trx->total_dibayar, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-right text-gray-400 text-xs whitespace-nowrap">
                                    {{ $trx->created_at->translatedFormat('d M Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

@endsection
