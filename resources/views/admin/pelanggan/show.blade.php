@extends('layouts.admin')

@section('title', 'Detail Pelanggan')
@section('page-title', 'Detail Pelanggan')

@section('content')

    <div class="w-full max-w-none space-y-5">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4 pb-6 border-b border-gray-100">
                <img
                    src="{{ $pelanggan->avatar_url }}"
                    alt="{{ $pelanggan->nama_lengkap }}"
                    class="w-14 h-14 rounded-full object-cover border border-gray-100"
                >

                <div class="min-w-0">
                    <h2 class="text-xl font-bold text-[#00372c]">
                        {{ $pelanggan->nama_lengkap }}
                    </h2>

                    <p class="text-sm text-gray-400 break-all">
                        {{ $pelanggan->email }}
                    </p>

                    <div class="mt-2">
                        @if($pelanggan->google_id)
                            <span class="inline-flex items-center gap-2 text-xs font-semibold px-2.5 py-1 rounded-full bg-white border border-gray-200 text-gray-700">
                                <svg width="14" height="14" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="#FFC107" d="M43.6 20.1H42V20H24v8h11.3C33.7 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 13 4 4 13 4 24s9 20 20 20 20-9 20-20c0-1.3-.1-2.6-.4-3.9z"/>
                                    <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.7 15.1 18.9 12 24 12c3.1 0 5.8 1.2 7.9 3.1l5.7-5.7C34 6.1 29.3 4 24 4 16.3 4 9.6 8.3 6.3 14.7z"/>
                                    <path fill="#4CAF50" d="M24 44c5.2 0 9.9-2 13.4-5.2l-6.2-5.2C29.2 35.1 26.7 36 24 36c-5.2 0-9.6-3.3-11.3-7.9l-6.5 5C9.5 39.5 16.2 44 24 44z"/>
                                    <path fill="#1976D2" d="M43.6 20.1H42V20H24v8h11.3c-.8 2.3-2.3 4.2-4.2 5.6l6.2 5.2C36.9 39.1 44 34 44 24c0-1.3-.1-2.6-.4-3.9z"/>
                                </svg>
                                Google
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                                    <path d="M3 7l9 6 9-6"/>
                                </svg>
                                Email
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-x-10 gap-y-6 pt-6">
                <div>
                    <p class="text-xs text-gray-400 mb-1">No. Telepon</p>
                    <p class="text-sm font-semibold text-[#00372c]">{{ $pelanggan->no_telp ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-400 mb-1">Alamat</p>
                    <p class="text-sm font-semibold text-[#00372c]">{{ $pelanggan->alamat ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-400 mb-1">Bergabung Sejak</p>
                    <p class="text-sm font-semibold text-[#00372c]">{{ $pelanggan->created_at->translatedFormat('d M Y') }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-400 mb-1">Total Transaksi</p>
                    <p class="text-sm font-semibold text-[#00372c]">{{ $pelanggan->transaksi->count() }} transaksi</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="font-bold text-[#00372c] text-base">Riwayat Transaksi</h2>
            </div>

            @if($pelanggan->transaksi->isEmpty())
                <div class="px-6 py-14 text-center text-gray-400 text-sm">
                    Pelanggan belum memiliki riwayat transaksi.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">Total</th>
                                <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-right">Tanggal</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @foreach($pelanggan->transaksi as $transaksi)
                                @php
                                    $statusText = match($transaksi->status) {
                                        'menunggu' => 'Menunggu',
                                        'disetujui' => 'Disetujui',
                                        'aktif' => 'Aktif',
                                        'selesai' => 'Selesai',
                                        'ditolak' => 'Ditolak',
                                        default => ucfirst($transaksi->status),
                                    };

                                    $statusColor = match($transaksi->status) {
                                        'menunggu' => 'text-amber-600',
                                        'disetujui', 'aktif' => 'text-[#085041]',
                                        'selesai' => 'text-gray-500',
                                        'ditolak' => 'text-red-500',
                                        default => 'text-gray-500',
                                    };
                                @endphp

                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <a
                                            href="{{ route('admin.transaksi.show', $transaksi->id) }}"
                                            class="font-semibold text-[#085041] hover:text-[#00372c] transition-colors"
                                        >
                                            {{ $transaksi->kode_transaksi }}
                                        </a>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="text-xs font-semibold {{ $statusColor }}">
                                            {{ $statusText }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-right font-semibold text-[#00372c]">
                                        Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                    </td>

                                    <td class="px-6 py-4 text-right text-xs text-gray-400">
                                        {{ $transaksi->created_at->translatedFormat('d M Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

@endsection
