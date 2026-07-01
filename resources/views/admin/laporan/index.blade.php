@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan Transaksi')

@section('header-actions')
    <a href="{{ route('admin.laporan.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
        class="flex items-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all">
        <span class="material-symbols-outlined text-base">picture_as_pdf</span>
        Export PDF
    </a>
@endsection

@section('content')

    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex items-end gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Bulan</label>
                <select name="bulan" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#085041] focus:border-transparent">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(now()->year, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Tahun</label>
                <select name="tahun" class="w-28 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#085041] focus:border-transparent">
                    @foreach(range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-[#085041] hover:bg-[#00372c] text-white text-sm font-semibold rounded-xl transition-all">
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-2">Total Transaksi Selesai</p>
            <p class="text-3xl font-bold text-[#00372c]">{{ $transaksi->count() }}</p>
        </div>
        <div class="bg-[#085041] rounded-2xl shadow-sm p-5 text-white">
            <p class="text-xs text-white/70 uppercase font-semibold tracking-wider mb-2">Total Pendapatan</p>
            <p class="text-3xl font-bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-2">Total Denda</p>
            <p class="text-3xl font-bold text-red-500">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Tabel Laporan --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-bold text-[#00372c] text-base">
                Transaksi Selesai — {{ \Carbon\Carbon::createFromDate($tahun, (int)$bulan, 1)->translatedFormat('F') }} {{ $tahun }}
            </h2>
        </div>

        @if($transaksi->isEmpty())
            <div class="px-6 py-16 text-center text-gray-400 text-sm">
                <span class="material-symbols-outlined text-4xl block mb-3 opacity-30">receipt_long</span>
                Tidak ada transaksi selesai di periode ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Pelanggan</th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">Total Sewa</th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">Denda</th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">Grand Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaksi as $trx)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-semibold text-[#085041]">
                                <a href="{{ route('admin.transaksi.show', $trx->id) }}" class="hover:underline">{{ $trx->kode_transaksi }}</a>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $trx->customer->nama_lengkap ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-400 text-xs">{{ $trx->created_at->translatedFormat('d M Y') }}</td>
                            <td class="px-6 py-4 text-right font-semibold text-[#00372c]">Rp {{ number_format($trx->total_harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-red-500">
                                {{ $trx->total_denda > 0 ? 'Rp ' . number_format($trx->total_denda, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-[#085041]">
                                Rp {{ number_format($trx->total_harga + $trx->total_denda, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 border-t border-gray-200">
                            <td colspan="3" class="px-6 py-4 font-bold text-[#00372c] text-sm">Total</td>
                            <td class="px-6 py-4 text-right font-bold text-[#00372c]">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-bold text-red-500">Rp {{ number_format($totalDenda, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right font-bold text-[#085041]">Rp {{ number_format($totalPendapatan + $totalDenda, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

@endsection