@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Overview')

@section('content')

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2.5 bg-[#e8f5f0] rounded-xl">
                    <span class="material-symbols-outlined text-[#085041]">group</span>
                </div>
            </div>
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Pelanggan</p>
            <p class="text-3xl font-bold text-[#00372c]">{{ $totalCustomer }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2.5 bg-[#e8f5f0] rounded-xl">
                    <span class="material-symbols-outlined text-[#085041]">inventory_2</span>
                </div>
            </div>
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Total Alat</p>
            <p class="text-3xl font-bold text-[#00372c]">{{ $totalAlat }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2.5 bg-yellow-50 rounded-xl">
                    <span class="material-symbols-outlined text-yellow-600">pending_actions</span>
                </div>
            </div>
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Menunggu Konfirmasi</p>
            <p class="text-3xl font-bold text-[#00372c]">{{ $transaksiMenunggu }}</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2.5 bg-[#e8f5f0] rounded-xl">
                    <span class="material-symbols-outlined text-[#085041]">fact_check</span>
                </div>
            </div>
            <p class="text-xs text-gray-400 font-semibold uppercase tracking-wider mb-1">Sewa Aktif</p>
            <p class="text-3xl font-bold text-[#00372c]">{{ $transaksiAktif }}</p>
        </div>

    </div>

    {{-- PENDAPATAN & DENDA --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
        <div class="bg-[#085041] rounded-2xl p-6 text-white flex items-center justify-between">
            <div>
                <span class="text-white/70 text-xs font-semibold uppercase tracking-wider">Total Pendapatan (Selesai)</span>
                <p class="text-3xl font-bold mt-2">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-3 bg-white/10 rounded-xl">
                <span class="material-symbols-outlined text-2xl">payments</span>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <span class="text-gray-400 text-xs font-semibold uppercase tracking-wider">Total Denda Terkumpul</span>
                <p class="text-3xl font-bold text-red-500 mt-2">Rp {{ number_format($totalDenda ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="p-3 bg-red-50 rounded-xl">
                <span class="material-symbols-outlined text-2xl text-red-500">report</span>
            </div>
        </div>
    </div>

    {{-- TRANSAKSI TERBARU --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-[#00372c] text-base">Transaksi Terbaru</h2>
            <a href="{{ route('admin.transaksi.index') }}" class="text-[#085041] hover:text-[#00372c] text-sm font-semibold transition-colors">Lihat Semua</a>
        </div>

        @if($transaksiTerbaru->isEmpty())
            <div class="px-6 py-16 text-center text-gray-400 text-sm">Belum ada transaksi.</div>
        @else
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">Total</th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transaksiTerbaru as $trx)
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
                        <td class="px-6 py-4 font-semibold text-[#00372c] text-sm">{{ $trx->kode_transaksi }}</td>
                        <td class="px-6 py-4 text-gray-600 text-sm">{{ $trx->customer->nama_lengkap ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex text-xs font-semibold px-3 py-1 rounded-full {{ $statusStyle }}">
                                {{ ucfirst($trx->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-semibold text-[#00372c] text-sm">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-right text-gray-400 text-xs">{{ $trx->created_at->translatedFormat('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection