@extends('layouts.admin')

@section('title', 'Laporan')
@section('page-title', 'Laporan Transaksi')

@section('header-actions')
    <a
        href="{{ route('admin.laporan.pdf', ['bulan' => $bulan, 'tahun' => $tahun]) }}"
        class="flex items-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all"
    >
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <path d="M14 2v6h6"/>
            <path d="M8 13h8"/>
            <path d="M8 17h5"/>
        </svg>
        Export PDF
    </a>
@endsection

@section('content')

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
        <form
            method="GET"
            action="{{ route('admin.laporan.index') }}"
            class="flex flex-wrap items-end gap-4"
        >
            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    Bulan
                </label>

                <select name="bulan" class="px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#085041] focus:border-transparent">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $bulan === $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate($tahun, $m, 1)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                    Tahun
                </label>

                <select name="tahun" class="w-28 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#085041] focus:border-transparent">
                    @foreach(range(now()->year, now()->year - 3) as $y)
                        <option value="{{ $y }}" {{ $tahun === $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="px-5 py-2.5 bg-[#085041] hover:bg-[#00372c] text-white text-sm font-semibold rounded-xl transition-all">
                Tampilkan
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-2">
                Transaksi Lunas
            </p>

            <p class="text-3xl font-bold text-[#00372c]">
                {{ $transaksi->count() }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-2">
                Pendapatan Sewa
            </p>

            <p class="text-3xl font-bold text-[#00372c]">
                Rp {{ number_format($totalSewa, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-2">
                Denda Terkumpul
            </p>

            <p class="text-3xl font-bold text-red-500">
                Rp {{ number_format($totalDenda, 0, ',', '.') }}
            </p>
        </div>

        <div class="bg-[#085041] rounded-2xl shadow-sm p-5 text-white">
            <p class="text-xs text-white/70 uppercase font-semibold tracking-wider mb-2">
                Total Diterima
            </p>

            <p class="text-3xl font-bold">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-bold text-[#00372c] text-base">
                Transaksi Lunas — {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F') }} {{ $tahun }}
            </h2>

            <p class="text-xs text-gray-400 mt-1">
                Periode laporan mengikuti tanggal pelunasan transaksi.
            </p>
        </div>

        @if($transaksi->isEmpty())
            <div class="px-6 py-16 text-center text-gray-400 text-sm">
                <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="mx-auto mb-3 opacity-30">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <path d="M14 2v6h6"/>
                    <path d="M8 13h8"/>
                    <path d="M8 17h5"/>
                </svg>

                Tidak ada transaksi lunas pada periode ini.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                                Kode
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                                Pelanggan
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                                Tanggal Lunas
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">
                                Total Sewa
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">
                                Denda
                            </th>
                            <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">
                                Total Dibayar
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaksi as $trx)
                            @php
                                $tanggalLunas = $trx->denda_dibayar_pada
                                    ?? $trx->dibayar_pada
                                    ?? $trx->updated_at;
                            @endphp

                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-[#085041]">
                                    <a href="{{ route('admin.transaksi.show', $trx->id) }}" class="hover:underline">
                                        {{ $trx->kode_transaksi }}
                                    </a>
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $trx->customer->nama_lengkap ?? '-' }}
                                </td>

                                <td class="px-6 py-4 text-gray-400 text-xs whitespace-nowrap">
                                    {{ $tanggalLunas->translatedFormat('d M Y, H:i') }} WIB
                                </td>

                                <td class="px-6 py-4 text-right font-semibold text-[#00372c] whitespace-nowrap">
                                    Rp {{ number_format($trx->total_harga, 0, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-right text-red-500 whitespace-nowrap">
                                    {{ $trx->total_denda > 0 ? 'Rp ' . number_format($trx->total_denda, 0, ',', '.') : '-' }}
                                </td>

                                <td class="px-6 py-4 text-right font-bold text-[#085041] whitespace-nowrap">
                                    Rp {{ number_format($trx->total_dibayar, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    <tfoot>
                        <tr class="bg-gray-50 border-t border-gray-200">
                            <td colspan="3" class="px-6 py-4 font-bold text-[#00372c] text-sm">
                                Total
                            </td>

                            <td class="px-6 py-4 text-right font-bold text-[#00372c] whitespace-nowrap">
                                Rp {{ number_format($totalSewa, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-right font-bold text-red-500 whitespace-nowrap">
                                Rp {{ number_format($totalDenda, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-right font-bold text-[#085041] whitespace-nowrap">
                                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

@endsection
