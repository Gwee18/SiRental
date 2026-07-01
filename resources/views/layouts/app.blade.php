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
        <div class="max-w-7xl mx-auto px-6 h-[72px] flex items-center justify-between">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16 3L2 28H30L16 3Z" fill="#68dbae" stroke="#68dbae" stroke-width="1.5" stroke-linejoin="round"/>
                    <path d="M16 10L7 26H25L16 10Z" fill="#085041"/>
                    <circle cx="16" cy="22" r="2.5" fill="#68dbae"/>
                </svg>
                <span class="text-white font-bold text-xl tracking-tight">SiRental</span>
            </a>

            {{-- Nav Links --}}
            <nav class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Beranda</a>
                <a href="#katalog" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Katalog Alat</a>
                <a href="#harga" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Daftar Harga</a>
                <a href="#tentang" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Tentang Kami</a>
            </nav>

            {{-- Auth Buttons --}}
            <div class="flex items-center gap-3">
                @auth('web')
                    <div class="relative">
                        <button id="profileMenuButton" type="button" class="flex items-center gap-2 focus:outline-none">
                            <img src="{{ auth('web')->user()->avatar_url }}"
                                alt="{{ auth('web')->user()->nama_lengkap }}"
                                class="w-9 h-9 rounded-full object-cover border-2 border-white/30">
                        </button>

                        <div id="profileMenuDropdown" class="hidden absolute right-0 mt-3 w-52 bg-white rounded-xl shadow-xl py-2 z-50 text-gray-700 border border-gray-100">
                            <div class="px-4 py-2.5 border-b border-gray-100">
                                <p class="text-sm font-semibold text-[#00372c] truncate">{{ auth('web')->user()->nama_lengkap }}</p>
                                <p class="text-xs text-gray-400 truncate">{{ auth('web')->user()->email }}</p>
                            </div>

                            <a href="{{ route('customer.transaksi.index') }}"
                                class="block px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors">
                                Transaksi Saya
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 transition-colors text-red-600">
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="bg-[#68dbae] hover:bg-[#55c99c] text-[#00372c] text-sm font-semibold px-4 py-2 rounded-lg transition-all">
                        Daftar
                    </a>
                @endauth
            </div>

        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-[#00372c] text-white">
        <div class="max-w-7xl mx-auto px-6 py-16 grid grid-cols-1 md:grid-cols-3 gap-12">

            {{-- Brand --}}
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 3L2 28H30L16 3Z" fill="#68dbae" stroke="#68dbae" stroke-width="1.5" stroke-linejoin="round"/>
                        <path d="M16 10L7 26H25L16 10Z" fill="#00372c"/>
                        <circle cx="16" cy="22" r="2.5" fill="#68dbae"/>
                    </svg>
                    <span class="font-bold text-lg">SiRental</span>
                </div>
                <p class="text-white/60 text-sm leading-relaxed">
                    Penyedia jasa sewa perlengkapan gunung modern dengan fokus pada kualitas alat dan kemudahan akses bagi para petualang Indonesia.
                </p>
                <div class="flex gap-4">
                    <a href="#" class="text-white/60 hover:text-[#68dbae] transition-colors">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </a>
                    <a href="mailto:sirental.ofc@gmail.com" class="text-white/60 hover:text-[#68dbae] transition-colors">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    </a>
                    <a href="tel:081231793810" class="text-white/60 hover:text-[#68dbae] transition-colors">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.63 3.45 2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.94a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </a>
                </div>
            </div>

            {{-- Navigasi --}}
            <div class="space-y-4">
                <h4 class="font-semibold text-base">Navigasi</h4>
                <ul class="space-y-3 text-sm text-white/60">
                    <li><a href="#" class="hover:text-[#68dbae] transition-colors">Beranda</a></li>
                    <li><a href="#katalog" class="hover:text-[#68dbae] transition-colors">Katalog Alat</a></li>
                    <li><a href="#harga" class="hover:text-[#68dbae] transition-colors">Daftar Harga</a></li>
                    <li><a href="#tentang" class="hover:text-[#68dbae] transition-colors">Tentang Kami</a></li>
                    <li><a href="#" class="hover:text-[#68dbae] transition-colors">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="hover:text-[#68dbae] transition-colors">Kebijakan Privasi</a></li>
                </ul>
            </div>

            {{-- Kontak & Jam Operasional --}}
            <div class="space-y-4">
                <h4 class="font-semibold text-base">Hubungi Kami</h4>
                <div class="text-sm text-white/60 space-y-3">
                    <div class="flex items-start gap-2">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="mt-0.5 shrink-0"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        <p>Jl. Tentara Genie Pelajar No.26, Petemon, Kec. Sawahan, Surabaya, Jawa Timur 60252</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        <a href="mailto:sirental.ofc@gmail.com" class="hover:text-[#68dbae] transition-colors">sirental.ofc@gmail.com</a>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.63 3.45 2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.94a16 16 0 0 0 6.29 6.29l.95-.95a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <a href="tel:081231793810" class="hover:text-[#68dbae] transition-colors">0812-3179-3810</a>
                    </div>
                    <div class="flex items-start gap-2 pt-1">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" class="mt-0.5 shrink-0"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <div>
                            <p>Senin – Sabtu: 08.00 – 20.00 WIB</p>
                            <p>Minggu: 10.00 – 18.00 WIB</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="border-t border-white/10 py-6">
            <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center gap-2 text-xs text-white/40">
                <p>© 2026 SiRental. All rights reserved.</p>
                <p>Designed for professional adventurers.</p>
            </div>
        </div>
    </footer>

    {{-- Navbar scroll effect --}}
    <script>
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('shadow-lg', 'backdrop-blur-md');
                navbar.style.backgroundColor = 'rgba(8, 80, 65, 0.95)';
            } else {
                navbar.classList.remove('shadow-lg', 'backdrop-blur-md');
                navbar.style.backgroundColor = '#085041';
            }
        });

        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Dropdown profil
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
    </script>

    @stack('scripts')

</body>
</html>