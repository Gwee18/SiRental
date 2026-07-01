<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') | SiRental</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 22; vertical-align: middle; font-size: 20px; }
    </style>
</head>
<body class="bg-[#f8f9fa] text-gray-900 font-sans antialiased">

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <aside class="w-64 bg-white border-r border-gray-100 flex flex-col shrink-0 sticky top-0 h-screen">
            <div class="h-[72px] flex items-center px-6 border-b border-gray-100">
                <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 3L2 28H30L16 3Z" fill="#68dbae" stroke="#68dbae" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="M16 10L7 26H25L16 10Z" fill="#00372c"/>
                    <circle cx="16" cy="22" r="2.5" fill="#68dbae"/>
                </svg>
                <span class="ml-2 font-bold text-lg text-[#00372c]">SiRental</span>
            </div>

            <nav class="flex-1 px-3 py-6 space-y-1 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-[#e8f5f0] text-[#085041] font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined">dashboard</span>
                    Dashboard
                </a>
                <a href="{{ route('admin.alat.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.alat.*') ? 'bg-[#e8f5f0] text-[#085041] font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined">inventory_2</span>
                    Barang
                </a>
                <a href="{{ route('admin.transaksi.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.transaksi.*') ? 'bg-[#e8f5f0] text-[#085041] font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined">fact_check</span>
                    Konfirmasi Peminjaman
                </a>
                <a href="{{ route('admin.pelanggan.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.pelanggan.*') ? 'bg-[#e8f5f0] text-[#085041] font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined">group</span>
                    Pelanggan
                </a>
                <a href="{{ route('admin.laporan.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('admin.laporan.*') ? 'bg-[#e8f5f0] text-[#085041] font-semibold' : 'text-gray-500 hover:bg-gray-50' }}">
                    <span class="material-symbols-outlined">analytics</span>
                    Laporan
                </a>
            </nav>

            <div class="p-3 border-t border-gray-100">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500 hover:bg-gray-50 text-sm transition-colors">
                        <span class="material-symbols-outlined">logout</span>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="flex-1 flex flex-col min-w-0">
            <header class="sticky top-0 z-30 h-[72px] bg-white/90 backdrop-blur-md border-b border-gray-100 flex items-center justify-between px-8 shrink-0">
                <div class="flex items-center gap-4">
                    <h1 class="font-bold text-[#00372c] text-xl">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="flex items-center gap-4">
                    @hasSection('header-actions')
                        @yield('header-actions')
                    @endif
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-100">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-[#00372c] leading-tight">{{ auth('admin')->user()->nama ?? 'Admin' }}</p>
                            <p class="text-[10px] uppercase tracking-wider text-gray-400 font-bold">Administrator</p>
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