@extends('layouts.app')

@section('title', 'Kebijakan Privasi')

@section('content')
<section class="pt-28 md:pt-32 pb-20 bg-white min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        {{-- Hero --}}
        <div class="mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-[#00372c] tracking-tight leading-tight mb-3">
                Kebijakan Privasi
            </h1>

            <p class="text-slate-600 text-sm md:text-base leading-relaxed max-w-xl">
                Penjelasan mengenai bagaimana SiRental mengumpulkan, menggunakan, menyimpan, dan menjaga data customer dalam proses rental alat pendakian.
            </p>
        </div>

        {{-- Content --}}
        <div class="relative pl-8 md:pl-9">

            {{-- Trail line --}}
            <div class="absolute left-[13px] md:left-[15px] top-2 bottom-2 w-[2px]"
                 style="background-image: linear-gradient(#e2e2de, #e2e2de); background-size: 2px 8px; background-repeat: repeat-y;"></div>

            {{-- 01 Data yang Dikumpulkan --}}
            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <ellipse cx="12" cy="5" rx="8" ry="3"/>
                        <path d="M4 5v14c0 1.66 3.58 3 8 3s8-1.34 8-3V5"/>
                        <path d="M4 12c0 1.66 3.58 3 8 3s8-1.34 8-3"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#0F6E56] mb-2">
                        UMUM
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Data yang Kami Kumpulkan
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        SiRental dapat mengumpulkan data customer seperti nama lengkap, email, nomor telepon, alamat, foto KTP, foto barang, serta data transaksi rental. Jika customer login menggunakan Google, sistem juga dapat menyimpan informasi akun seperti email, nama, dan foto profil.
                    </p>
                </div>
            </article>

            {{-- 02 Penggunaan Data --}}
            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h11"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#0F6E56] mb-2">
                        UMUM
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Penggunaan Data
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Data customer digunakan untuk kebutuhan pendaftaran akun, verifikasi pengajuan rental, pengelolaan transaksi, proses pengambilan dan pengembalian barang, serta komunikasi antara customer dan pihak SiRental.
                    </p>
                </div>
            </article>

            {{-- 03 Foto KTP dan Barang --}}
            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="2" y="5" width="20" height="14" rx="2"/>
                        <circle cx="8.5" cy="12" r="2"/>
                        <path d="M13 10h6M13 14h4"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#993C1D] mb-2">
                        PERHATIAN
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Foto KTP dan Foto Barang
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Foto KTP digunakan sebagai data verifikasi peminjam. Foto barang digunakan sebagai bukti kondisi barang dalam proses transaksi rental. Data tersebut hanya digunakan untuk kebutuhan administrasi dan keamanan transaksi.
                    </p>
                </div>
            </article>

            {{-- 04 Keamanan Data --}}
            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/>
                        <path d="M9 12l2 2 4-4"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#185FA5] mb-2">
                        KEAMANAN
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Keamanan Data
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        SiRental berupaya menjaga keamanan data customer dan membatasi akses data hanya kepada pihak yang berwenang, seperti admin yang bertugas mengelola transaksi rental.
                    </p>
                </div>
            </article>

            {{-- 05 Penyimpanan Data --}}
            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="4" width="18" height="6" rx="1"/>
                        <rect x="3" y="14" width="18" height="6" rx="1"/>
                        <path d="M7 7h.01M7 17h.01"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#0F6E56] mb-2">
                        UMUM
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Penyimpanan Data
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Data customer dan transaksi disimpan selama masih diperlukan untuk kebutuhan layanan, pencatatan riwayat rental, dan administrasi sistem. Data dapat diperbarui apabila customer melakukan perubahan informasi pada akun atau transaksi.
                    </p>
                </div>
            </article>

            {{-- 06 Pembagian Data --}}
            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="6" cy="12" r="3"/>
                        <circle cx="18" cy="6" r="3"/>
                        <circle cx="18" cy="18" r="3"/>
                        <path d="M8.5 10.5l7-3M8.5 13.5l7 3"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#993C1D] mb-2">
                        PERHATIAN
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Pembagian Data
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        SiRental tidak menjual data customer kepada pihak lain. Data hanya digunakan untuk kebutuhan operasional layanan rental dan tidak dibagikan kepada pihak luar kecuali diperlukan untuk kepentingan hukum atau keamanan.
                    </p>
                </div>
            </article>

            {{-- 07 Hak Customer --}}
            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 21v-1a7 7 0 0 1 12.5-4.3"/>
                        <path d="M17 14v6M14 17h6"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#185FA5] mb-2">
                        KEAMANAN
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Hak Customer
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Customer dapat menghubungi SiRental apabila ingin memperbarui data akun atau menanyakan penggunaan data pribadi yang tersimpan di sistem.
                    </p>
                </div>
            </article>

            {{-- Perubahan Kebijakan --}}
            <article class="relative">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 8v4M12 16h.01"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-slate-500 mb-2">
                        INFORMASI
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Perubahan Kebijakan
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Kebijakan privasi ini dapat diperbarui sewaktu-waktu sesuai kebutuhan pengembangan sistem dan layanan SiRental.
                    </p>
                </div>
            </article>

        </div>
    </div>
</section>
@endsection