<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | SiRental</title>

    {{-- Dipakai sementara supaya ikon lama di halaman admin lain tidak berubah jadi teks --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 20px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
            font-variation-settings:
                'FILL' 0,
                'wght' 400,
                'GRAD' 0,
                'opsz' 22;
            vertical-align: middle;
        }
    </style>
</head>

<body class="bg-[#f8f9fa] text-gray-900 font-sans antialiased">

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <aside class="w-64 bg-white border-r border-gray-100 flex flex-col shrink-0 sticky top-0 h-screen">
            <div class="h-[72px] flex items-center px-6 border-b border-gray-100">
                <svg width="28" height="28" viewBox="0 0 32 32" fill="none">
                    <path d="M16 3L2 28H30L16 3Z" fill="#68dbae" stroke="#68dbae" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="M16 10L7 26H25L16 10Z" fill="#00372c"/>
                    <circle cx="16" cy="22" r="2.5" fill="#68dbae"/>
                </svg>

                <span class="ml-2 font-bold text-lg text-[#00372c]">
                    SiRental
                </span>
            </div>

            <nav class="flex-1 px-3 py-6 space-y-1 text-sm">

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#e8f5f0] text-[#085041] font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                    Dashboard
                </a>

                <a href="{{ route('admin.alat.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.alat.*') ? 'bg-[#e8f5f0] text-[#085041] font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                        <path d="M3.3 7L12 12l8.7-5"/>
                        <path d="M12 22V12"/>
                    </svg>
                    Barang
                </a>

                <a href="{{ route('admin.transaksi.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.transaksi.*') ? 'bg-[#e8f5f0] text-[#085041] font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                    Konfirmasi Peminjaman
                </a>

                <a href="{{ route('admin.pengembalian.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.pengembalian.*') ? 'bg-[#e8f5f0] text-[#085041] font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M7 3h8l4 4v14H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                        <path d="M15 3v5h5"/>
                        <path d="M9 13h7"/>
                        <path d="M9 17h4"/>
                        <path d="M4 12l-3 3 3 3"/>
                        <path d="M1 15h6"/>
                    </svg>
                    Verifikasi Pengembalian
                </a>

                <a href="{{ route('admin.pelanggan.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.pelanggan.*') ? 'bg-[#e8f5f0] text-[#085041] font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Pelanggan
                </a>

                <a href="{{ route('admin.laporan.index') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.laporan.*') ? 'bg-[#e8f5f0] text-[#085041] font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path d="M4 19V5"/>
                        <path d="M4 19h16"/>
                        <path d="M8 16v-5"/>
                        <path d="M12 16V8"/>
                        <path d="M16 16v-7"/>
                    </svg>
                    Laporan
                </a>
            </nav>

            <div class="p-3 border-t border-gray-100">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf

                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500 hover:bg-gray-50 text-sm transition-colors">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <path d="M16 17l5-5-5-5"/>
                            <path d="M21 12H9"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="sticky top-0 z-30 h-[72px] bg-white/90 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-8 shrink-0">
                <div class="flex items-center gap-4">
                    <h1 class="font-bold text-[#00372c] text-xl">
                        @yield('page-title', 'Dashboard')
                    </h1>
                </div>

                <div class="flex items-center gap-4">
                    @hasSection('header-actions')
                        @yield('header-actions')
                    @endif

                    <div class="flex items-center gap-3 pl-4 border-l border-gray-100">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-[#00372c] leading-tight">
                                {{ auth('admin')->user()->nama ?? 'Admin SiRental' }}
                            </p>
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">
                                Administrator
                            </p>
                        </div>

                        <div class="w-9 h-9 rounded-full bg-[#085041] text-white flex items-center justify-center text-xs font-semibold">
                            {{ strtoupper(substr(auth('admin')->user()->nama ?? 'A', 0, 1)) }}
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-8 bg-[#f1f3f4]">
                @yield('content')
            </main>
        </div>

    </div>

</body>
</html>