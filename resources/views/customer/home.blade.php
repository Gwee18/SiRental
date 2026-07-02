@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

    {{-- HERO SECTION --}}
    <section class="relative h-screen min-h-[640px] flex items-center overflow-hidden">

        {{-- Background Image --}}
        <div class="absolute inset-0 z-0">
            <img
                src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1920&q=80"
                alt="Pemandangan gunung Indonesia"
                class="w-full h-full object-cover"
            >
            <div class="absolute inset-0 bg-[#00372c]/60"></div>
        </div>

        {{-- Hero Content --}}
        <div class="relative z-10 max-w-7xl mx-auto px-6 w-full">
            <div class="max-w-2xl">
                <span class="inline-block bg-[#68dbae]/20 text-[#68dbae] text-xs font-semibold tracking-widest uppercase px-3 py-1.5 rounded-full mb-6 border border-[#68dbae]/30">
                    Rental Alat Pendakian Surabaya
                </span>

                <h1 class="text-5xl md:text-6xl font-bold text-white leading-tight mb-6">
                    Siap mendaki? <br>
                    Sewa alat di <span class="text-[#68dbae]">SiRental</span>
                </h1>

                <p class="text-white/80 text-lg leading-relaxed mb-10">
                    Perlengkapan mendaki gunung berkualitas, bersih, dan terawat untuk petualangan Anda.
                    Kami memastikan setiap alat siap tempur untuk puncak impian Anda.
                </p>

                <div class="flex flex-wrap gap-4">
                    @auth('web')
                        <a href="{{ route('rental.index') }}" class="bg-[#68dbae] hover:bg-[#55c99c] text-[#00372c] font-semibold px-8 py-3.5 rounded-xl transition-all duration-200 text-sm">
                            Rental Sekarang
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="bg-[#68dbae] hover:bg-[#55c99c] text-[#00372c] font-semibold px-8 py-3.5 rounded-xl transition-all duration-200 text-sm">
                            Mulai Rental
                        </a>
                    @endauth

                    <a href="#katalog" class="border border-white/40 hover:border-white text-white font-semibold px-8 py-3.5 rounded-xl transition-all duration-200 text-sm">
                        Lihat Katalog Alat
                    </a>
                </div>
            </div>
        </div>

        {{-- Scroll indicator --}}
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-10 animate-bounce">
            <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24" class="opacity-60">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </div>

    </section>

    {{-- CARA SEWA --}}
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-[#00372c] mb-3">Langkah Mudah Menyewa</h2>
                <p class="text-gray-500 text-base">Petualangan dimulai dari proses yang sederhana</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

                {{-- Step 1 --}}
                <div class="text-center space-y-4">
                    <svg width="34" height="34" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24" class="mx-auto">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>

                    <div class="text-xs font-bold text-[#68dbae] uppercase tracking-widest">01</div>
                    <h3 class="font-semibold text-[#00372c] text-base">Daftar & Masuk</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Buat akun atau masuk terlebih dahulu sebelum mengajukan penyewaan alat.
                    </p>
                </div>

                {{-- Step 2 --}}
                <div class="text-center space-y-4">
                    <svg width="34" height="34" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24" class="mx-auto">
                        <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                        <polyline points="16.5 9.4 7.55 4.24"/>
                        <polyline points="3.29 7 12 12 20.71 7"/>
                        <line x1="12" y1="22" x2="12" y2="12"/>
                        <circle cx="18.5" cy="15.5" r="2.5"/>
                        <path d="M20.27 17.27L22 19"/>
                    </svg>

                    <div class="text-xs font-bold text-[#68dbae] uppercase tracking-widest">02</div>
                    <h3 class="font-semibold text-[#00372c] text-base">Pilih Alat</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Pilih alat yang ingin disewa dan tentukan jumlah serta lama sewa.
                    </p>
                </div>

                {{-- Step 3 --}}
                <div class="text-center space-y-4">
                    <svg width="34" height="34" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24" class="mx-auto">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                        <polyline points="10 9 9 9 8 9"/>
                    </svg>

                    <div class="text-xs font-bold text-[#68dbae] uppercase tracking-widest">03</div>
                    <h3 class="font-semibold text-[#00372c] text-base">Isi Data Diri</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Lengkapi data peminjam, alamat, nomor telepon, dan upload foto KTP.
                    </p>
                </div>

                {{-- Step 4 --}}
                <div class="text-center space-y-4">
                    <svg width="34" height="34" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24" class="mx-auto">
                        <polyline points="9 11 12 14 22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>

                    <div class="text-xs font-bold text-[#68dbae] uppercase tracking-widest">04</div>
                    <h3 class="font-semibold text-[#00372c] text-base">Tunggu Konfirmasi</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Admin akan memverifikasi pengajuan setelah pembayaran cash dilakukan di tempat.
                    </p>
                </div>

            </div>
        </div>
    </section>

    {{-- KATALOG ALAT --}}
    <section id="katalog" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">

            <div class="flex items-end justify-between mb-16">
                <div>
                    <h2 class="text-3xl font-bold text-[#00372c] mb-3">Katalog Perlengkapan</h2>
                    <p class="text-gray-500 text-base">Hanya alat dengan standar profesional pendakian</p>
                </div>

                <a href="#harga" class="text-[#085041] hover:text-[#00372c] text-sm font-semibold flex items-center gap-1 transition-colors">
                    Lihat Semua
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($alat as $item)
                    <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-md hover:-translate-y-1 transition-all duration-200">

                        {{-- Gambar / Placeholder --}}
                        <div class="h-48 bg-[#e8f5f0] flex items-center justify-center">
                            @if($item->foto_alat)
                                <img src="{{ Storage::url($item->foto_alat) }}" alt="{{ $item->nama_alat }}" class="w-full h-full object-cover">
                            @else
                                <svg width="56" height="56" fill="none" stroke="#085041" stroke-width="1.2" viewBox="0 0 24 24" opacity="0.4">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Info --}}
                        <div class="p-5 space-y-3">
                            <div>
                                <span class="text-xs text-[#085041] font-semibold uppercase tracking-wide">
                                    {{ $item->kategori }}
                                </span>
                                <h4 class="font-semibold text-[#00372c] text-base mt-0.5">
                                    {{ $item->nama_alat }}
                                </h4>
                            </div>

                            <p class="text-[#085041] font-bold text-base">
                                Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}
                                <span class="text-gray-400 font-normal text-sm">/ hari</span>
                            </p>

                            <div class="flex items-center justify-between pt-1">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg
                                    {{ $item->stok_tersedia > 0 ? 'bg-[#e8f5f0] text-[#085041]' : 'bg-red-50 text-red-500' }}">
                                    {{ $item->stok_tersedia > 0 ? 'Stok: ' . $item->stok_tersedia : 'Habis' }}
                                </span>

                                @if($item->stok_tersedia > 0)
                                    @auth('web')
                                        <a href="{{ route('rental.index') }}" class="text-[#085041] hover:text-[#00372c] transition-colors">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <circle cx="9" cy="21" r="1"/>
                                                <circle cx="20" cy="21" r="1"/>
                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}" class="text-[#085041] hover:text-[#00372c] transition-colors">
                                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <circle cx="9" cy="21" r="1"/>
                                                <circle cx="20" cy="21" r="1"/>
                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                                            </svg>
                                        </a>
                                    @endauth
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 text-center py-16 text-gray-400">
                        <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" class="mx-auto mb-4 opacity-40">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        </svg>
                        <p class="text-sm">Belum ada alat tersedia</p>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    {{-- TABEL HARGA --}}
    <section id="harga" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">

            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-[#00372c] mb-3">Daftar Harga Sewa</h2>
                <p class="text-gray-500 text-base">Harga transparan tanpa biaya tersembunyi</p>
            </div>

            <div class="space-y-8 overflow-x-auto">
                @foreach($alatByKategori as $kategori => $items)
                    <div class="min-w-[700px]">

                        {{-- Header Kategori --}}
                        <div class="bg-[#085041] text-white px-6 py-3 rounded-t-xl flex items-center gap-2">
                            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l2-1.14"/>
                            </svg>
                            <span class="font-semibold text-sm">Kategori: {{ $kategori }}</span>
                        </div>

                        {{-- Tabel --}}
                        <table class="w-full bg-white border border-gray-100 rounded-b-xl overflow-hidden">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wide">
                                    <th class="text-left px-6 py-3.5">Nama Alat</th>
                                    <th class="px-6 py-3.5 text-center">1 Hari</th>
                                    <th class="px-6 py-3.5 text-center">2 Hari</th>
                                    <th class="px-6 py-3.5 text-center">3 Hari</th>
                                    <th class="px-6 py-3.5 text-center">4 Hari</th>
                                    <th class="px-6 py-3.5 text-center">5 Hari</th>
                                </tr>
                            </thead>

                            <tbody class="text-sm text-gray-700">
                                @foreach($items as $i => $item)
                                    <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors {{ $i % 2 == 0 ? 'bg-white' : 'bg-gray-50/50' }}">
                                        <td class="px-6 py-4 font-medium text-[#00372c]">
                                            {{ $item->nama_alat }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            Rp {{ number_format($item->harga_per_hari * 1, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            Rp {{ number_format($item->harga_per_hari * 2, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            Rp {{ number_format($item->harga_per_hari * 3, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            Rp {{ number_format($item->harga_per_hari * 4, 0, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            Rp {{ number_format($item->harga_per_hari * 5, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                @endforeach
            </div>

        </div>
    </section>

    {{-- TENTANG KAMI --}}
    <section id="tentang" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">

                {{-- Gambar --}}
                <div class="relative">
                    <img
                        src="https://images.unsplash.com/photo-1551632811-561732d1e306?w=800&q=80"
                        alt="Pendakian gunung"
                        class="w-full h-[420px] object-cover rounded-2xl"
                    >
                </div>

                {{-- Konten --}}
                <div class="space-y-6">
                    <div>
                        <span class="text-xs font-bold text-[#68dbae] uppercase tracking-widest">Tentang Kami</span>
                        <h2 class="text-3xl font-bold text-[#00372c] mt-2 mb-4">
                            Mitra Terpercaya Para Pendaki Indonesia
                        </h2>
                        <p class="text-gray-500 leading-relaxed text-base">
                            SiRental hadir sebagai solusi bagi para pendaki yang ingin menikmati alam tanpa harus membeli
                            perlengkapan mahal. Kami menyediakan alat pendakian berkualitas tinggi yang terawat dan siap digunakan.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <svg width="22" height="22" fill="none" stroke="#085041" stroke-width="2" viewBox="0 0 24 24" class="shrink-0 mt-0.5">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>

                            <div>
                                <h4 class="font-semibold text-[#00372c] text-sm">Alat Terawat & Berkualitas</h4>
                                <p class="text-gray-500 text-sm mt-0.5">
                                    Setiap alat dicek dan dibersihkan sebelum dipinjamkan.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <svg width="22" height="22" fill="none" stroke="#085041" stroke-width="2" viewBox="0 0 24 24" class="shrink-0 mt-0.5">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>

                            <div>
                                <h4 class="font-semibold text-[#00372c] text-sm">Harga Transparan</h4>
                                <p class="text-gray-500 text-sm mt-0.5">
                                    Tidak ada biaya tersembunyi, semua harga tertera jelas.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <svg width="22" height="22" fill="none" stroke="#085041" stroke-width="2" viewBox="0 0 24 24" class="shrink-0 mt-0.5">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>

                            <div>
                                <h4 class="font-semibold text-[#00372c] text-sm">Proses Mudah & Cepat</h4>
                                <p class="text-gray-500 text-sm mt-0.5">
                                    Daftar, pilih alat, isi data diri, lalu tunggu konfirmasi admin.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-4 pt-2">
                        @auth('web')
                            <a href="{{ route('rental.index') }}" class="bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                                Mulai Rental
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                                Mulai Rental
                            </a>
                        @endauth

                        <a href="https://wa.me/6281231793810" target="_blank" class="border border-[#085041] text-[#085041] hover:bg-[#085041] hover:text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm flex items-center gap-2">
                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                            </svg>
                            Hubungi WhatsApp
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- CTA SECTION - hanya tampil untuk pengunjung yang belum login --}}
    @guest('web')
        <section class="py-20 bg-[#085041]">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-white mb-4">Siap untuk petualangan berikutnya?</h2>

                <p class="text-white/70 text-base mb-8 max-w-xl mx-auto">
                    Daftar sekarang dan nikmati kemudahan rental alat pendakian berkualitas langsung dari genggaman Anda.
                </p>

                <div class="flex flex-wrap justify-center gap-4">
                    <a href="{{ route('register') }}" class="bg-[#68dbae] hover:bg-[#55c99c] text-[#00372c] font-semibold px-8 py-3.5 rounded-xl transition-all text-sm">
                        Daftar Gratis Sekarang
                    </a>

                    <a href="#katalog" class="border border-white/40 hover:border-white text-white font-semibold px-8 py-3.5 rounded-xl transition-all text-sm">
                        Lihat Katalog
                    </a>
                </div>
            </div>
        </section>
    @endguest

@endsection