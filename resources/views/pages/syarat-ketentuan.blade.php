@extends('layouts.app')

@section('title', 'Syarat & Ketentuan')

@section('content')
<section class="pt-28 md:pt-32 pb-20 bg-white min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">

        <div class="mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-[#00372c] tracking-tight leading-tight mb-3">
                Syarat & Ketentuan
            </h1>

            <p class="text-slate-600 text-sm md:text-base leading-relaxed max-w-xl">
                Ketentuan penggunaan layanan SiRental untuk memastikan proses rental alat pendakian berjalan aman, jelas, dan bertanggung jawab.
            </p>
        </div>

        <div class="relative pl-8 md:pl-9">

            <div class="absolute left-[13px] md:left-[15px] top-2 bottom-2 w-[2px]"
                 style="background-image: linear-gradient(#e2e2de, #e2e2de); background-size: 2px 8px; background-repeat: repeat-y;"></div>

            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <path d="M14 2v6h6"/>
                        <path d="M8 13h8M8 17h5"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#0F6E56] mb-2">
                        UMUM
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Ketentuan Umum
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        SiRental adalah layanan penyewaan perlengkapan pendakian yang membantu customer melakukan pengajuan rental secara online. Dengan menggunakan layanan ini, customer dianggap telah membaca, memahami, dan menyetujui seluruh syarat dan ketentuan yang berlaku.
                    </p>
                </div>
            </article>

            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4"/>
                        <path d="M4 21v-1a7 7 0 0 1 14 0v1"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#0F6E56] mb-2">
                        UMUM
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Akun Customer
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Customer wajib menggunakan data yang benar saat mendaftar dan mengajukan rental, termasuk nama lengkap, nomor telepon, alamat, serta email yang aktif. Customer bertanggung jawab atas keamanan akun masing-masing.
                    </p>
                </div>
            </article>

            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M3 20l5-14 4 10 3-6 6 10z"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#185FA5] mb-2">
                        TRANSAKSI
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Pengajuan Rental
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Customer dapat memilih alat yang tersedia, menentukan jumlah barang, lama sewa, serta mengunggah foto barang dan foto KTP sebagai bagian dari proses verifikasi. Pengajuan rental belum dianggap aktif sebelum dikonfirmasi oleh admin.
                    </p>
                </div>
            </article>

            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="6" width="18" height="13" rx="2"/>
                        <path d="M3 10h18"/>
                        <circle cx="16" cy="14" r="1"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#185FA5] mb-2">
                        TRANSAKSI
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Pembayaran
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Pembayaran dilakukan secara tunai di kasir saat customer mengambil barang. Setelah pembayaran dilakukan dan pengajuan disetujui oleh admin, transaksi akan berstatus aktif dan masa sewa mulai berjalan.
                    </p>
                </div>
            </article>

            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 12a9 9 0 1 1-3-6.7"/>
                        <path d="M21 3v5h-5"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#185FA5] mb-2">
                        TRANSAKSI
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Pengambilan dan Pengembalian Barang
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Customer wajib mengambil dan mengembalikan barang sesuai jadwal yang telah ditentukan. Saat pengembalian, customer perlu menunjukkan kode pengembalian yang tersedia pada halaman detail transaksi.
                    </p>
                </div>
            </article>

            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2l9 5v10l-9 5-9-5V7z"/>
                        <path d="M3 7l9 5 9-5"/>
                        <path d="M12 12v10"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#0F6E56] mb-2">
                        UMUM
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Kondisi Barang
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Customer bertanggung jawab menjaga alat rental selama masa peminjaman. Barang wajib dikembalikan dalam kondisi baik, lengkap, dan sesuai dengan kondisi saat diterima.
                    </p>
                </div>
            </article>

            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 7v6l4 2"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#993C1D] mb-2">
                        PERHATIAN
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Denda Keterlambatan
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Jika barang dikembalikan melebihi batas waktu yang ditentukan, sistem dapat menghitung denda keterlambatan sesuai ketentuan yang berlaku. Denda akan ditampilkan pada detail transaksi apabila terjadi keterlambatan.
                    </p>
                </div>
            </article>

            <article class="relative mb-4">
                <div class="absolute -left-8 md:-left-9 top-4 w-[26px] h-[26px] rounded-full bg-white border-2 border-[#1D9E75] flex items-center justify-center">
                    <svg width="13" height="13" fill="none" stroke="#0F6E56" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M5.5 5.5l13 13"/>
                    </svg>
                </div>

                <div class="bg-[#fafaf9] border border-slate-200 rounded-2xl p-5 md:p-6 shadow-sm">
                    <p class="text-[10px] font-bold tracking-[0.18em] text-[#993C1D] mb-2">
                        PERHATIAN
                    </p>

                    <h2 class="text-lg md:text-xl font-bold text-slate-900 mb-2">
                        Penolakan Pengajuan
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        Admin berhak menolak pengajuan rental apabila data customer tidak valid, stok tidak tersedia, atau terdapat alasan lain yang berkaitan dengan keamanan dan kelayakan transaksi.
                    </p>
                </div>
            </article>

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
                        Perubahan Ketentuan
                    </h2>

                    <p class="text-slate-600 leading-relaxed text-[15px]">
                        SiRental dapat memperbarui syarat dan ketentuan ini apabila diperlukan. Perubahan akan berlaku setelah ditampilkan pada halaman ini.
                    </p>
                </div>
            </article>

        </div>
    </div>
</section>
@endsection
