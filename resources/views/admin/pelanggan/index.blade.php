@extends('layouts.admin')

@section('title', 'Pelanggan')
@section('page-title', 'Data Pelanggan')

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

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-[#00372c] text-base">
                Semua Pelanggan
            </h2>

            <span class="text-xs text-gray-400 font-semibold">
                {{ $pelanggan->count() }} pelanggan terdaftar
            </span>
        </div>

        @if($pelanggan->isEmpty())
            <div class="px-6 py-16 text-center text-gray-400 text-sm">
                <svg width="44" height="44" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" class="mx-auto mb-3 opacity-30">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>

                Belum ada pelanggan terdaftar.
            </div>
        @else
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                            Pelanggan
                        </th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                            Kontak
                        </th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                            Metode Daftar
                        </th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-center">
                            Transaksi
                        </th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider">
                            Terdaftar
                        </th>
                        <th class="px-6 py-3.5 font-semibold text-xs text-gray-400 uppercase tracking-wider text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach($pelanggan as $p)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img
                                        src="{{ $p->avatar_url }}"
                                        alt="{{ $p->nama_lengkap }}"
                                        class="w-9 h-9 rounded-full object-cover border border-gray-100"
                                    >

                                    <div>
                                        <p class="font-semibold text-[#00372c]">
                                            {{ $p->nama_lengkap }}
                                        </p>

                                        <p class="text-gray-400 text-xs">
                                            {{ $p->email }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $p->no_telp ?? '-' }}
                            </td>

                            <td class="px-6 py-4">
                                @if($p->google_id)
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
                            </td>

                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold {{ $p->transaksi_count > 0 ? 'text-[#085041]' : 'text-gray-400' }}">
                                    {{ $p->transaksi_count }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-400 text-xs">
                                {{ $p->created_at->translatedFormat('d M Y') }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a
                                        href="{{ route('admin.pelanggan.show', $p->id) }}"
                                        class="p-1.5 text-gray-400 hover:text-[#085041] hover:bg-[#e8f5f0] rounded-lg transition-colors"
                                        title="Lihat Detail"
                                    >
                                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                            <circle cx="12" cy="12" r="3"/>
                                        </svg>
                                    </a>

                                    @if($p->transaksi_count === 0)
                                        <form
                                            method="POST"
                                            action="{{ route('admin.pelanggan.destroy', $p->id) }}"
                                            onsubmit="return confirm('Pelanggan ini belum memiliki riwayat transaksi. Hapus permanen {{ $p->nama_lengkap }}?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Hapus permanen"
                                                aria-label="Hapus {{ $p->nama_lengkap }}"
                                            >
                                                <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                                    <path d="M3 6h18"/>
                                                    <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6"/>
                                                    <path d="M14 11v6"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @else
                                        <button
                                            type="button"
                                            disabled
                                            class="p-1.5 text-gray-300 cursor-not-allowed rounded-lg"
                                            title="Tidak dapat dihapus karena memiliki riwayat transaksi"
                                            aria-label="{{ $p->nama_lengkap }} tidak dapat dihapus karena memiliki riwayat transaksi"
                                        >
                                            <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                                                <rect x="5" y="10" width="14" height="10" rx="2"/>
                                                <path d="M8 10V7a4 4 0 0 1 8 0v3"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

@endsection
