<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SiRental') | Rental Alat Pendakian</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 font-sans antialiased">

    {{-- NAVBAR --}}
    <header id="navbar" class="fixed top-0 left-0 right-0 z-50 bg-[#085041] transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-[72px] md:h-[86px] flex items-center justify-between">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center shrink-0">
                <img
                    src="{{ asset('images/logo-sirental.png') }}"
                    alt="SiRental"
                    class="h-[52px] md:h-[60px] w-auto object-contain"
                >
            </a>

            {{-- Desktop Nav Links --}}
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-white/80 hover:text-white text-sm font-medium transition-colors">
                    Beranda
                </a>

                <a href="{{ route('home') }}#katalog" class="text-white/80 hover:text-white text-sm font-medium transition-colors">
                    Katalog Alat
                </a>

                <a href="{{ route('home') }}#harga" class="text-white/80 hover:text-white text-sm font-medium transition-colors">
                    Daftar Harga
                </a>

                <a href="{{ route('home') }}#tentang" class="text-white/80 hover:text-white text-sm font-medium transition-colors">
                    Tentang Kami
                </a>
            </nav>

            {{-- Desktop Auth Buttons --}}
<div class="hidden md:flex items-center gap-3">
    @auth('web')
        <div class="relative">
            <button id="profileMenuButton" type="button" class="flex items-center gap-2 focus:outline-none">
                <img
                    src="{{ auth('web')->user()->avatar_url }}"
                    alt="{{ auth('web')->user()->nama_lengkap }}"
                    class="w-9 h-9 rounded-full object-cover border-2 border-white/30"
                >
            </button>

            <div id="profileMenuDropdown" class="hidden absolute right-0 mt-3 w-52 bg-white rounded-xl shadow-xl py-2 z-50 text-gray-700 border border-gray-100">
                <div class="px-4 py-2.5 border-b border-gray-100">
                    <p class="text-sm font-semibold text-[#00372c] truncate">
                        {{ auth('web')->user()->nama_lengkap }}
                    </p>

                    <p class="text-xs text-gray-400 truncate">
                        {{ auth('web')->user()->email }}
                    </p>
                </div>

                <a
                    href="{{ route('customer.profil') }}"
                    class="block px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors"
                >
                    Profil Saya
                </a>

                <a
                    href="{{ route('customer.transaksi.index') }}"
                    class="block px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors"
                >
                    Transaksi Saya
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button
                        type="submit"
                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors text-red-600"
                    >
                        Logout
                    </button>
                </form>
            </div>
        </div>
    @else
        <a href="{{ route('login') }}" class="text-white/80 hover:text-white text-sm font-medium transition-colors">
            Masuk
        </a>
    @endauth
</div>

            {{-- Mobile Menu Button --}}
            <button
                id="mobileMenuButton"
                type="button"
                class="md:hidden w-10 h-10 rounded-xl border border-white/15 text-white flex items-center justify-center hover:bg-white/10 transition-colors"
                aria-label="Buka menu"
            >
                <svg id="mobileMenuIconOpen" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M4 7h16"/>
                    <path d="M4 12h16"/>
                    <path d="M4 17h16"/>
                </svg>

                <svg id="mobileMenuIconClose" class="hidden" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path d="M18 6L6 18"/>
                    <path d="M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobileMenu" class="md:hidden hidden bg-[#085041] border-t border-white/10">
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('home') }}" class="mobile-menu-link block text-white/85 hover:text-white hover:bg-white/10 text-sm font-medium px-4 py-3 rounded-xl transition-colors">
                    Beranda
                </a>

                <a href="{{ route('home') }}#katalog" class="mobile-menu-link block text-white/85 hover:text-white hover:bg-white/10 text-sm font-medium px-4 py-3 rounded-xl transition-colors">
                    Katalog Alat
                </a>

                <a href="{{ route('home') }}#harga" class="mobile-menu-link block text-white/85 hover:text-white hover:bg-white/10 text-sm font-medium px-4 py-3 rounded-xl transition-colors">
                    Daftar Harga
                </a>

                <a href="{{ route('home') }}#tentang" class="mobile-menu-link block text-white/85 hover:text-white hover:bg-white/10 text-sm font-medium px-4 py-3 rounded-xl transition-colors">
                    Tentang Kami
                </a>

                <div class="pt-3 mt-3 border-t border-white/10">
                    @auth('web')
                        <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-white/10 mb-2">
                            <img
                                src="{{ auth('web')->user()->avatar_url }}"
                                alt="{{ auth('web')->user()->nama_lengkap }}"
                                class="w-10 h-10 rounded-full object-cover border-2 border-white/30"
                            >

                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-white truncate">
                                    {{ auth('web')->user()->nama_lengkap }}
                                </p>

                                <p class="text-xs text-white/60 truncate">
                                    {{ auth('web')->user()->email }}
                                </p>
                            </div>
                        </div>

                        <a href="{{ route('customer.profil') }}" class="mobile-menu-link block text-white/85 hover:text-white hover:bg-white/10 text-sm font-medium px-4 py-3 rounded-xl transition-colors">
                            Profil Saya
                        </a>

                        <a href="{{ route('customer.transaksi.index') }}" class="mobile-menu-link block text-white/85 hover:text-white hover:bg-white/10 text-sm font-medium px-4 py-3 rounded-xl transition-colors">
                            Transaksi Saya
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="w-full text-left text-red-200 hover:text-white hover:bg-red-500/20 text-sm font-medium px-4 py-3 rounded-xl transition-colors"
                            >
                                Logout
                            </button>
                        </form>
                    @else
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('login') }}" class="text-center text-white border border-white/20 hover:bg-white/10 text-sm font-semibold px-4 py-3 rounded-xl transition-colors">
                                Masuk
                            </a>

                            <a href="{{ route('register') }}" class="text-center bg-[#68dbae] hover:bg-[#55c99c] text-[#00372c] text-sm font-semibold px-4 py-3 rounded-xl transition-all">
                                Daftar
                            </a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-[#00372c] text-white">
        <div class="max-w-7xl mx-auto px-6 py-14 md:py-16 grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-12">

            {{-- Brand --}}
            <div class="text-center md:text-left md:-ml-12 lg:-ml-16">
                <a href="{{ route('home') }}" class="inline-flex items-center justify-center md:justify-start">
                    <img
                        src="{{ asset('images/logo-sirental.png') }}"
                        alt="SiRental"
                        class="w-[200px] h-auto object-contain brightness-110 contrast-125 drop-shadow-sm"
                    >
                </a>

                <p class="mt-3 text-white/90 text-sm leading-relaxed max-w-sm mx-auto md:mx-0">
                    Penyedia jasa sewa perlengkapan gunung modern dengan fokus pada kualitas alat dan kemudahan akses bagi para petualang Indonesia.
                </p>

                <div class="flex justify-center md:justify-start gap-4 mt-5">
                    <a href="{{ route('home') }}" class="text-white/80 hover:text-[#68dbae] transition-colors" aria-label="Website">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M2 12h20"/>
                            <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                        </svg>
                    </a>

                    <a href="mailto:sirental.ofc@gmail.com" class="text-white/80 hover:text-[#68dbae] transition-colors" aria-label="Email">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </a>

                    <a href="tel:081231793810" class="text-white/80 hover:text-[#68dbae] transition-colors" aria-label="Telepon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.63 3.45 2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.94a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Navigasi --}}
            <div class="space-y-4 text-center md:text-left">
                <h4 class="font-semibold text-base">
                    Navigasi
                </h4>

                <ul class="space-y-3 text-sm text-white/60">
                    <li>
                        <a href="{{ route('home') }}" class="hover:text-[#68dbae] transition-colors">
                            Beranda
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('home') }}#katalog" class="hover:text-[#68dbae] transition-colors">
                            Katalog Alat
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('home') }}#harga" class="hover:text-[#68dbae] transition-colors">
                            Daftar Harga
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('home') }}#tentang" class="hover:text-[#68dbae] transition-colors">
                            Tentang Kami
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('terms') }}" class="hover:text-[#68dbae] transition-colors">
                            Syarat & Ketentuan
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('privacy') }}" class="hover:text-[#68dbae] transition-colors">
                            Kebijakan Privasi
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Kontak & Jam Operasional --}}
            <div class="space-y-4">
                <h4 class="font-semibold text-base text-center md:text-left">
                    Hubungi Kami
                </h4>

                <div class="text-sm text-white/60 space-y-3 max-w-md mx-auto md:mx-0">
                    <div class="flex items-start gap-2">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="mt-0.5 shrink-0">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>

                        <p>
                            Jl. Tentara Genie Pelajar No.26, Petemon, Kec. Sawahan, Surabaya, Jawa Timur 60252
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>

                        <a href="mailto:sirental.ofc@gmail.com" class="hover:text-[#68dbae] transition-colors break-all">
                            sirental.ofc@gmail.com
                        </a>
                    </div>

                    <div class="flex items-center gap-2">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.63 3.45 2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.94a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>

                        <a href="tel:081231793810" class="hover:text-[#68dbae] transition-colors">
                            0812-3179-3810
                        </a>
                    </div>

                    <div class="flex items-start gap-2 pt-1">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="mt-0.5 shrink-0">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>

                        <div>
                            <p>Senin – Sabtu: 08.00 – 20.00 WIB</p>
                            <p>Minggu: 10.00 – 18.00 WIB</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="border-t border-white/10 py-6">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-2 text-xs text-white/40 text-center md:text-left">
                <p>© 2026 SiRental. All rights reserved.</p>
                <p>Designed for professional adventurers.</p>
            </div>
        </div>
    </footer>

    {{-- Scripts --}}
    <script>
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');

            if (!navbar) return;

            if (window.scrollY > 50) {
                navbar.classList.add('shadow-lg', 'backdrop-blur-md');
                navbar.style.backgroundColor = 'rgba(8, 80, 65, 0.95)';
            } else {
                navbar.classList.remove('shadow-lg', 'backdrop-blur-md');
                navbar.style.backgroundColor = '#085041';
            }
        });

        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));

                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        const profileBtn = document.getElementById('profileMenuButton');
        const profileDropdown = document.getElementById('profileMenuDropdown');

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!profileDropdown.contains(e.target) && !profileBtn.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
        }

        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuIconOpen = document.getElementById('mobileMenuIconOpen');
        const mobileMenuIconClose = document.getElementById('mobileMenuIconClose');

        function closeMobileMenu() {
            if (!mobileMenu) return;

            mobileMenu.classList.add('hidden');
            mobileMenuIconOpen.classList.remove('hidden');
            mobileMenuIconClose.classList.add('hidden');
        }

        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
                mobileMenuIconOpen.classList.toggle('hidden');
                mobileMenuIconClose.classList.toggle('hidden');
            });

            document.querySelectorAll('.mobile-menu-link').forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    closeMobileMenu();
                }
            });
        }
    </script>

    @stack('scripts')

</body>
</html>