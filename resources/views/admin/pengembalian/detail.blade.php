@extends('layouts.admin')

@section('title', 'Detail Pengembalian')
@section('page-title', 'Detail Pengembalian')

@section('content')

    @if(session('error'))
        <div class="mb-6 bg-red-50 text-red-600 text-sm font-medium px-4 py-3 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    <div class="max-w-5xl">

        <a href="{{ route('admin.pengembalian.index') }}" class="inline-flex items-center gap-2 text-[#085041] hover:text-[#00372c] text-sm font-semibold mb-6 transition-colors">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
            Kembali ke Verifikasi Pengembalian
        </a>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

            <div class="xl:col-span-8 space-y-6">

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-7">
                    <div class="flex flex-wrap items-start justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
                        <div>
                            <span class="inline-flex bg-[#e8f5f0] text-[#085041] text-xs font-bold uppercase tracking-widest px-3 py-1.5 rounded-full mb-4">
                                Transaksi Aktif
                            </span>

                            <h2 class="text-2xl font-bold text-[#00372c]">
                                {{ $transaksi->kode_transaksi }}
                            </h2>

                            <p class="text-gray-400 text-sm mt-1">
                                Dikonfirmasi pada {{ $transaksi->updated_at->translatedFormat('d F Y, H:i') }}
                            </p>
                        </div>

                        <span class="inline-flex items-center gap-2 bg-[#e8f5f0] text-[#085041] text-xs font-bold px-3 py-1.5 rounded-full">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M8.5 12.5l2.2 2.2L15.5 10"/>
                            </svg>
                            Sedang Disewa
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-2">
                                Customer
                            </p>
                            <p class="font-bold text-[#00372c]">
                                {{ $transaksi->customer->nama_lengkap ?? '-' }}
                            </p>
                            <p class="text-gray-500 text-sm mt-1">
                                {{ $transaksi->customer->no_telp ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-2">
                                Periode Sewa
                            </p>
                            <p class="font-bold text-[#00372c]">
                                {{ \Carbon\Carbon::parse($transaksi->tanggal_mulai)->translatedFormat('d M Y') }}
                                -
                                {{ \Carbon\Carbon::parse($transaksi->tanggal_selesai)->translatedFormat('d M Y') }}
                            </p>
                            <p class="text-gray-500 text-sm mt-1">
                                Batas pengembalian sesuai tanggal selesai.
                            </p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.transaksi.selesai', $transaksi->id) }}" id="formPengembalian">
                    @csrf
                    <input type="hidden" name="source" value="pengembalian">

                    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-7 py-5 border-b border-gray-100">
                            <h2 class="font-bold text-[#00372c] text-lg">
                                Checklist Barang Dikembalikan
                            </h2>
                            <p class="text-gray-500 text-sm mt-1">
                                Centang semua barang yang sudah diterima dan diperiksa secara fisik.
                            </p>
                        </div>

                        <div class="p-7 space-y-4">
                            @foreach($transaksi->detailTransaksi as $detail)
                                <label class="block cursor-pointer">
                                    <div class="return-item flex items-center gap-4 rounded-2xl border border-gray-100 bg-gray-50 p-4 transition-all">
                                        <div class="shrink-0">
                                            <input
                                                type="checkbox"
                                                name="barang_dikembalikan[]"
                                                value="{{ $detail->id }}"
                                                class="return-check hidden"
                                            >

                                            <div class="check-ui w-7 h-7 rounded-lg border-2 border-gray-300 bg-white flex items-center justify-center text-white transition-all">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                    <path d="M20 6L9 17l-5-5"/>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="w-16 h-16 rounded-xl bg-white border border-gray-100 overflow-hidden flex items-center justify-center shrink-0">
                                            @if($detail->foto_barang)
                                                <img src="{{ asset('storage/' . $detail->foto_barang) }}" class="w-full h-full object-cover">
                                            @elseif($detail->alat && $detail->alat->foto_alat)
                                                <img src="{{ asset('storage/' . $detail->alat->foto_alat) }}" class="w-full h-full object-cover">
                                            @else
                                                <svg width="26" height="26" fill="none" stroke="#085041" stroke-width="1.8" viewBox="0 0 24 24">
                                                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                                    <path d="M3.3 7L12 12l8.7-5"/>
                                                    <path d="M12 22V12"/>
                                                </svg>
                                            @endif
                                        </div>

                                        <div class="flex-1">
                                            <p class="font-bold text-[#00372c]">
                                                {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                            </p>
                                            <p class="text-gray-500 text-sm mt-1">
                                                {{ $detail->jumlah }} unit · {{ $detail->lama_sewa }} hari · Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}/hari
                                            </p>
                                        </div>

                                        <div class="text-right hidden sm:block">
                                            <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-1">
                                                Subtotal
                                            </p>
                                            <p class="font-bold text-[#085041]">
                                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        <div class="px-7 py-5 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                            <p class="text-sm text-gray-500">
                                Tombol selesai akan aktif setelah semua barang dicentang.
                            </p>

                            <button
                                type="submit"
                                id="btnSelesai"
                                disabled
                                onclick="return confirm('Pastikan semua barang sudah diperiksa. Selesaikan transaksi ini?')"
                                class="inline-flex items-center gap-2 bg-gray-300 text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm cursor-not-allowed"
                            >
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                                Selesaikan Transaksi
                            </button>
                        </div>
                    </div>
                </form>

            </div>

            <div class="xl:col-span-4 space-y-6">

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="bg-[#085041] px-6 py-5">
                        <h2 class="font-bold text-white">
                            Ringkasan Pengembalian
                        </h2>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Total Sewa</span>
                            <span class="font-bold text-[#00372c]">
                                Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Denda per Hari</span>
                            <span class="font-bold text-[#00372c]">
                                Rp {{ number_format($dendaPerHari, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="border-t border-dashed border-gray-200 pt-5">
                            @if($hariTerlambat > 0)
                                <div class="bg-red-50 border border-red-100 rounded-2xl p-4">
                                    <p class="text-red-600 font-bold text-sm mb-1">
                                        Terlambat {{ $hariTerlambat }} hari
                                    </p>
                                    <p class="text-red-500 text-sm">
                                        Estimasi denda:
                                    </p>
                                    <p class="text-red-600 text-2xl font-bold mt-1">
                                        Rp {{ number_format($estimasiDenda, 0, ',', '.') }}
                                    </p>
                                </div>
                            @else
                                <div class="bg-[#e8f5f0] border border-[#bcebd8] rounded-2xl p-4">
                                    <p class="text-[#085041] font-bold text-sm mb-1">
                                        Tidak Ada Denda
                                    </p>
                                    <p class="text-[#085041]/70 text-sm">
                                        Transaksi ini belum melewati batas pengembalian.
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="border-t border-gray-100 pt-5">
                            <p class="text-xs uppercase tracking-widest text-gray-400 font-bold mb-2">
                                Total Akhir
                            </p>
                            <p class="text-3xl font-bold text-[#085041]">
                                Rp {{ number_format($transaksi->total_harga + $estimasiDenda, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#e8f5f0] text-[#085041] flex items-center justify-center shrink-0">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 8h.01"/>
                                <path d="M11 12h1v5h1"/>
                            </svg>
                        </div>

                        <p class="text-gray-500 text-sm leading-6">
                            Pastikan kondisi fisik barang sudah dicek sebelum menyelesaikan transaksi.
                            Setelah selesai, status transaksi akan berubah menjadi selesai dan stok barang otomatis kembali.
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <script>
        const checkboxes = document.querySelectorAll('.return-check');
        const btnSelesai = document.getElementById('btnSelesai');

        function updateButton() {
            let semuaDicentang = true;

            checkboxes.forEach(function(checkbox) {
                const item = checkbox.closest('.return-item');
                const checkUi = item.querySelector('.check-ui');

                if (checkbox.checked) {
                    item.classList.remove('bg-gray-50');
                    item.classList.add('bg-[#e8f5f0]', 'border-[#68dbae]');

                    checkUi.classList.remove('border-gray-300', 'bg-white');
                    checkUi.classList.add('border-[#085041]', 'bg-[#085041]');
                } else {
                    item.classList.add('bg-gray-50');
                    item.classList.remove('bg-[#e8f5f0]', 'border-[#68dbae]');

                    checkUi.classList.add('border-gray-300', 'bg-white');
                    checkUi.classList.remove('border-[#085041]', 'bg-[#085041]');

                    semuaDicentang = false;
                }
            });

            if (semuaDicentang && checkboxes.length > 0) {
                btnSelesai.disabled = false;
                btnSelesai.classList.remove('bg-gray-300', 'cursor-not-allowed');
                btnSelesai.classList.add('bg-[#085041]', 'hover:bg-[#00372c]', 'cursor-pointer');
            } else {
                btnSelesai.disabled = true;
                btnSelesai.classList.add('bg-gray-300', 'cursor-not-allowed');
                btnSelesai.classList.remove('bg-[#085041]', 'hover:bg-[#00372c]', 'cursor-pointer');
            }
        }

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', updateButton);
        });

        updateButton();
    </script>

@endsection