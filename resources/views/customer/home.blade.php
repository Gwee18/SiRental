@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

@php
    $isCustomerLogin = auth('web')->check();
    $rentalUrl = $isCustomerLogin ? route('rental.index') : route('login');
    $rentalLabel = $isCustomerLogin ? 'Rental Sekarang' : 'Mulai Rental';
@endphp

<section class="relative min-h-[100svh] md:h-screen md:min-h-[640px] flex items-start md:items-center pt-[120px] md:pt-0 pb-16 md:pb-0 overflow-hidden">

    <div class="absolute inset-0 z-0">
        <img
            src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1920&q=80"
            alt="Pemandangan gunung Indonesia"
            class="w-full h-full object-cover"
        >

        <div class="absolute inset-0 bg-[#00372c]/60"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-6 w-full">
        <div class="max-w-2xl">
            <span class="inline-block bg-white/10 text-[#F7F2E8] text-[11px] sm:text-xs font-semibold tracking-widest uppercase px-3 py-1.5 rounded-full mb-5 md:mb-6 border border-white/20">
                Rental Alat Pendakian Surabaya
            </span>

            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white leading-tight mb-5 md:mb-6">
                Siap mendaki? <br>
                Sewa alat di <span class="text-[#F7F2E8]">SiRental</span>
            </h1>

            <p class="text-white/80 text-base md:text-lg leading-relaxed mb-8 md:mb-10 max-w-xl">
                Perlengkapan mendaki gunung berkualitas, bersih, dan terawat untuk petualangan Anda.
                Kami memastikan setiap alat siap tempur untuk puncak impian Anda.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a
                    href="{{ $rentalUrl }}"
                    class="w-full sm:w-auto text-center bg-[#F7F2E8] hover:bg-white text-[#00372c] font-bold px-8 py-3.5 rounded-xl transition-all duration-200 text-sm shadow-sm hover:shadow-md"
                >
                    {{ $rentalLabel }}
                </a>

                <a
                    href="#katalog"
                    class="w-full sm:w-auto text-center border border-white/45 hover:border-white hover:bg-white/10 text-white font-semibold px-8 py-3.5 rounded-xl transition-all duration-200 text-sm"
                >
                    Lihat Katalog Alat
                </a>
            </div>
        </div>
    </div>

    <div class="hidden md:block absolute bottom-8 left-1/2 -translate-x-1/2 z-10 animate-bounce">
        <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24" class="opacity-60">
            <polyline points="6 9 12 15 18 9"/>
        </svg>
    </div>

</section>

<section class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center mb-12 md:mb-16">
            <h2 class="text-2xl md:text-3xl font-bold text-[#00372c] mb-3">
                Langkah Mudah Menyewa
            </h2>

            <p class="text-gray-500 text-sm md:text-base">
                Petualangan dimulai dari proses yang sederhana
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

            <div class="text-center space-y-4">
                <svg width="34" height="34" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24" class="mx-auto">
                    <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/>
                    <path d="M22 6l-10 7L2 6"/>
                </svg>

                <div class="text-xs font-bold text-[#085041] uppercase tracking-widest">
                    01
                </div>

                <h3 class="font-semibold text-[#00372c] text-base">
                    Masuk dengan Email
                </h3>

                <p class="text-gray-500 text-sm leading-relaxed">
                    Masuk menggunakan Google atau kode verifikasi yang dikirim ke email Anda.
                </p>
            </div>

            <div class="text-center space-y-4">
                <svg width="34" height="34" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24" class="mx-auto">
                    <path d="M21 10V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4A2 2 0 0 0 12 22a2 2 0 0 0 1-.27l2-1.14"/>
                    <polyline points="16.5 9.4 7.55 4.24"/>
                    <polyline points="3.29 7 12 12 20.71 7"/>
                    <line x1="12" y1="22" x2="12" y2="12"/>
                    <circle cx="18.5" cy="15.5" r="2.5"/>
                    <path d="M20.27 17.27L22 19"/>
                </svg>

                <div class="text-xs font-bold text-[#085041] uppercase tracking-widest">
                    02
                </div>

                <h3 class="font-semibold text-[#00372c] text-base">
                    Pilih Alat
                </h3>

                <p class="text-gray-500 text-sm leading-relaxed">
                    Pilih alat yang ingin disewa dan tentukan jumlah serta lama sewa.
                </p>
            </div>

            <div class="text-center space-y-4">
                <svg width="34" height="34" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24" class="mx-auto">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>

                <div class="text-xs font-bold text-[#085041] uppercase tracking-widest">
                    03
                </div>

                <h3 class="font-semibold text-[#00372c] text-base">
                    Isi Data Rental
                </h3>

                <p class="text-gray-500 text-sm leading-relaxed">
                    Lengkapi data peminjam, alamat, nomor telepon, dan upload foto KTP.
                </p>
            </div>

            <div class="text-center space-y-4">
                <svg width="34" height="34" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24" class="mx-auto">
                    <polyline points="9 11 12 14 22 4"/>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                </svg>

                <div class="text-xs font-bold text-[#085041] uppercase tracking-widest">
                    04
                </div>

                <h3 class="font-semibold text-[#00372c] text-base">
                    Tunggu Konfirmasi
                </h3>

                <p class="text-gray-500 text-sm leading-relaxed">
                    Admin akan memverifikasi pengajuan setelah pembayaran cash dilakukan di tempat.
                </p>
            </div>

        </div>
    </div>
</section>

<section id="katalog" class="py-14 md:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-8 md:mb-16">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-[#00372c] mb-2 md:mb-3">
                    Katalog Perlengkapan
                </h2>

                <p class="text-gray-500 text-sm md:text-base">
                    Hanya alat dengan standar profesional pendakian
                </p>
            </div>

            <a
                href="{{ route('katalog.index') }}"
                class="text-[#085041] hover:text-[#00372c] text-sm font-semibold inline-flex items-center gap-1 transition-colors"
            >
                Lihat Semua
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse($alat as $item)

                <div class="sm:hidden bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="flex gap-4 p-3">

                        <div class="w-[104px] h-[104px] rounded-xl bg-[#f2f6f4] overflow-hidden flex items-center justify-center shrink-0">
                            @if($item->foto_alat)
                                <img
                                    src="{{ Storage::url($item->foto_alat) }}"
                                    alt="{{ $item->nama_alat }}"
                                    class="w-full h-full object-cover"
                                >
                            @else
                                <svg width="38" height="38" fill="none" stroke="#085041" stroke-width="1.3" viewBox="0 0 24 24" opacity="0.35">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                            @endif
                        </div>

                        <div class="min-w-0 flex-1 flex flex-col justify-between py-1">
                            <div>
                                <p class="text-[10px] font-bold text-[#085041] uppercase tracking-wider mb-1">
                                    {{ $item->kategori }}
                                </p>

                                <h4 class="font-bold text-[#00372c] text-sm leading-snug line-clamp-2">
                                    {{ $item->nama_alat }}
                                </h4>

                                <p class="text-[#085041] font-bold text-sm mt-2">
                                    Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}
                                    <span class="text-gray-400 font-normal text-xs">
                                        / hari
                                    </span>
                                </p>
                            </div>

                            <div class="flex items-center justify-start mt-3">
                                <p class="text-[11px] font-semibold {{ $item->stok_tersedia > 0 ? 'text-[#085041]' : 'text-red-500' }}">
                                    {{ $item->stok_tersedia > 0 ? 'Stok: ' . $item->stok_tersedia : 'Habis' }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="hidden sm:block bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-md hover:-translate-y-1 transition-all duration-200">

                    <div class="h-48 bg-[#e8f5f0] flex items-center justify-center">
                        @if($item->foto_alat)
                            <img
                                src="{{ Storage::url($item->foto_alat) }}"
                                alt="{{ $item->nama_alat }}"
                                class="w-full h-full object-cover"
                            >
                        @else
                            <svg width="56" height="56" fill="none" stroke="#085041" stroke-width="1.2" viewBox="0 0 24 24" opacity="0.4">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        @endif
                    </div>

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
                            <span class="text-gray-400 font-normal text-sm">
                                / hari
                            </span>
                        </p>

                        <div class="pt-1">
                            <p class="text-xs font-semibold {{ $item->stok_tersedia > 0 ? 'text-[#085041]' : 'text-red-500' }}">
                                {{ $item->stok_tersedia > 0 ? 'Stok: ' . $item->stok_tersedia : 'Habis' }}
                            </p>
                        </div>
                    </div>
                </div>

            @empty
                <div class="sm:col-span-2 lg:col-span-4 text-center py-16 text-gray-400">
                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.2" viewBox="0 0 24 24" class="mx-auto mb-4 opacity-40">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    </svg>

                    <p class="text-sm">
                        Belum ada alat tersedia
                    </p>
                </div>
            @endforelse
        </div>

    </div>
</section>

<section id="harga" class="py-16 md:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6">

        <div class="text-center mb-10 md:mb-16">
            <h2 class="text-2xl md:text-3xl font-bold text-[#00372c] mb-3">
                Daftar Harga Sewa
            </h2>

            <p class="text-gray-500 text-sm md:text-base">
                Harga transparan tanpa biaya tersembunyi
            </p>

            <p class="md:hidden text-xs text-gray-400 mt-2">
                Geser tabel untuk melihat harga hari berikutnya.
            </p>
        </div>

        <div class="space-y-8">
            @forelse($alatByKategori as $kategori => $items)
                <div class="rounded-2xl border border-gray-100 bg-white overflow-hidden shadow-sm">

                    <div class="bg-[#085041] text-white px-4 md:px-6 py-3 flex items-center gap-2">
                        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M4 6h16"/>
                            <path d="M4 12h16"/>
                            <path d="M4 18h10"/>
                        </svg>

                        <span class="font-semibold text-sm">
                            Kategori: {{ $kategori }}
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[620px] bg-white">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wide">
                                    <th class="sticky left-0 z-10 bg-gray-50 text-left px-4 md:px-6 py-3.5 min-w-[150px]">
                                        Nama Alat
                                    </th>

                                    <th class="px-4 md:px-6 py-3.5 text-center whitespace-nowrap">
                                        1 Hari
                                    </th>

                                    <th class="px-4 md:px-6 py-3.5 text-center whitespace-nowrap">
                                        2 Hari
                                    </th>

                                    <th class="px-4 md:px-6 py-3.5 text-center whitespace-nowrap">
                                        3 Hari
                                    </th>

                                    <th class="px-4 md:px-6 py-3.5 text-center whitespace-nowrap">
                                        4 Hari
                                    </th>

                                    <th class="px-4 md:px-6 py-3.5 text-center whitespace-nowrap">
                                        5 Hari
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="text-sm text-gray-700">
                                @foreach($items as $i => $item)
                                    <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="sticky left-0 z-10 bg-white px-4 md:px-6 py-4 font-semibold text-[#00372c] min-w-[150px]">
                                            {{ $item->nama_alat }}
                                        </td>

                                        @for($day = 1; $day <= 5; $day++)
                                            <td class="px-4 md:px-6 py-4 text-center whitespace-nowrap">
                                                Rp {{ number_format($item->harga_per_hari * $day, 0, ',', '.') }}
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            @empty
                <div class="text-center py-14 text-gray-400">
                    <p class="text-sm">
                        Belum ada daftar harga tersedia
                    </p>
                </div>
            @endforelse
        </div>

    </div>
</section>

<section id="tentang" class="py-16 md:py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-6">

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 md:gap-16 items-center">

            <div class="relative">
                <img
                    src="https://images.unsplash.com/photo-1551632811-561732d1e306?w=800&q=80"
                    alt="Pendakian gunung"
                    class="w-full h-[280px] md:h-[420px] object-cover rounded-2xl"
                >
            </div>

            <div class="space-y-6">
                <div>
                    <span class="text-xs font-bold text-[#085041] uppercase tracking-widest">
                        Tentang Kami
                    </span>

                    <h2 class="text-2xl md:text-3xl font-bold text-[#00372c] mt-2 mb-4">
                        Mitra Terpercaya Para Pendaki Indonesia
                    </h2>

                    <p class="text-gray-500 leading-relaxed text-sm md:text-base">
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
                            <h4 class="font-semibold text-[#00372c] text-sm">
                                Alat Terawat & Berkualitas
                            </h4>

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
                            <h4 class="font-semibold text-[#00372c] text-sm">
                                Harga Transparan
                            </h4>

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
                            <h4 class="font-semibold text-[#00372c] text-sm">
                                Proses Mudah & Cepat
                            </h4>

                            <p class="text-gray-500 text-sm mt-0.5">
                                Masuk dengan email, pilih alat, isi data rental, lalu tunggu konfirmasi admin.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <a
                        href="{{ $rentalUrl }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all duration-200 text-sm"
                    >
                        Mulai Rental
                    </a>

                    <a
                        href="https://wa.me/6281231793810"
                        target="_blank"
                        class="w-full sm:w-auto inline-flex items-center justify-center border border-[#085041] text-[#085041] hover:bg-[#085041] hover:text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm gap-2"
                    >
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

@endsection
