@extends('layouts.admin')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

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

    @php
        $statusTextColor = match($transaksi->status) {
            'menunggu' => 'text-yellow-600',
            'disetujui', 'aktif' => 'text-[#085041]',
            'ditolak' => 'text-red-500',
            'selesai' => 'text-gray-500',
            default => 'text-gray-500',
        };

        $statusText = match($transaksi->status) {
            'menunggu' => 'Menunggu Konfirmasi',
            'disetujui' => 'Disetujui',
            'aktif' => 'Aktif',
            'ditolak' => 'Ditolak',
            'selesai' => 'Selesai',
            default => ucfirst($transaksi->status),
        };

        $fotoKtp = $transaksi->foto_ktp ?? ($transaksi->customer->foto_ktp ?? null);
        $totalPembayaran = ($transaksi->total_harga ?? 0) + ($transaksi->total_denda ?? 0);
    @endphp

    <div class="w-full">
        <a href="{{ route('admin.transaksi.index') }}"
           class="inline-flex items-center gap-1.5 text-[#085041] hover:text-[#00372c] text-sm font-semibold mb-6 transition-colors">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
            Kembali ke Daftar Transaksi
        </a>

        <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_390px] 2xl:grid-cols-[minmax(0,1fr)_420px] gap-6 items-start">

            <div class="min-w-0">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

                    <div class="flex flex-wrap items-start justify-between gap-4 mb-6 pb-6 border-b border-gray-100">
                        <div>
                            <h2 class="text-2xl font-bold text-[#00372c] mb-1">
                                {{ $transaksi->kode_transaksi }}
                            </h2>

                            <p class="text-gray-400 text-sm">
                                Dipesan {{ $transaksi->created_at->translatedFormat('d F Y, H:i') }} WIB
                            </p>
                        </div>

                        <p class="text-sm font-semibold {{ $statusTextColor }}">
                            {{ $statusText }}
                        </p>
                    </div>

                    <div class="mb-8">
                        <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-4">
                            Data Pelanggan
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 text-sm">
                            <div>
                                <p class="text-gray-400 text-xs mb-1">Nama</p>
                                <p class="font-semibold text-[#00372c]">
                                    {{ $transaksi->nama_peminjam
    ?: ($transaksi->customer->nama_lengkap ?? '-') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-400 text-xs mb-1">Email</p>
                                <p class="font-semibold text-[#00372c] break-all">
                                    {{ $transaksi->email_peminjam
    ?: ($transaksi->customer->email ?? '-') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-400 text-xs mb-1">No. Telepon</p>
                                <p class="font-semibold text-[#00372c]">
                                    {{ $transaksi->no_telp_peminjam
    ?: ($transaksi->customer->no_telp ?? '-') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-400 text-xs mb-1">Alamat</p>
                                <p class="font-semibold text-[#00372c] leading-relaxed">
                                    {{ $transaksi->alamat_peminjam
    ?: ($transaksi->customer->alamat ?? '-') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($transaksi->tanggal_mulai && $transaksi->tanggal_selesai)
                        <div class="mb-8">
                            <p class="text-sm text-gray-500">
                                <span class="font-semibold text-[#00372c]">
                                    Periode Sewa:
                                </span>

                                <span class="font-medium text-[#00372c]">
                                    {{ \Carbon\Carbon::parse($transaksi->tanggal_mulai)->translatedFormat('d M Y') }}
                                    &mdash;
                                    {{ \Carbon\Carbon::parse($transaksi->tanggal_selesai)->translatedFormat('d M Y') }}
                                </span>
                            </p>
                        </div>
                    @endif

                    <div class="mb-4">
                        <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-4">
                            Alat yang Disewa
                        </p>

                        <div class="hidden md:grid grid-cols-[70px_100px_minmax(0,1fr)_160px_120px] gap-4 px-4 py-3 bg-gray-50 rounded-xl text-xs font-semibold uppercase tracking-wide text-gray-400 mb-3">
                            <div>No</div>
                            <div>Foto Barang</div>
                            <div>Nama Barang</div>
                            <div>Total Harga</div>
                            <div>Foto Customer</div>
                        </div>

                        <div class="space-y-3">
                            @foreach($transaksi->detailTransaksi as $index => $detail)
                                <div class="border border-gray-100 rounded-2xl md:rounded-xl p-4 md:p-0 overflow-hidden">

                                    <div class="md:hidden space-y-4">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <p class="text-xs text-gray-400">No</p>
                                                <p class="font-semibold text-[#00372c]">{{ $index + 1 }}</p>
                                            </div>

                                            <div class="text-right">
                                                <p class="text-xs text-gray-400">Total Harga</p>
                                                <p class="font-bold text-[#085041]">
                                                    Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-4">
                                            <div class="w-20 h-20 rounded-xl overflow-hidden border border-gray-100 bg-gray-50 shrink-0">
                                                @if($detail->alat->foto_alat ?? false)
                                                    <img
                                                        src="{{ Storage::url($detail->alat->foto_alat) }}"
                                                        alt="{{ $detail->alat->nama_alat ?? 'Foto alat' }}"
                                                        class="w-full h-full object-cover"
                                                    >
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <svg width="24" height="24" fill="none" stroke="#94a3b8" stroke-width="1.8" viewBox="0 0 24 24">
                                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                                            <path d="M3.3 7L12 12l8.7-5"/>
                                                            <path d="M12 22V12"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="min-w-0 flex-1">
                                                <p class="font-semibold text-[#00372c]">
                                                    {{ $detail->alat->nama_alat ?? '-' }}
                                                </p>

                                                <p class="text-sm text-gray-400 mt-1">
                                                    {{ $detail->jumlah }} unit &middot;
                                                    {{ $detail->lama_sewa }} hari &middot;
                                                    Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}/hari
                                                </p>
                                            </div>
                                        </div>

                                        <div>
                                            <p class="text-xs text-gray-400 mb-2">Foto Customer</p>

                                            @if($detail->foto_barang)
                                                <button
                                                    type="button"
                                                    onclick="previewImage('{{ Storage::url($detail->foto_barang) }}', 'Foto Barang Customer - {{ $detail->alat->nama_alat ?? 'Barang' }}')"
                                                    class="w-20 h-20 rounded-xl overflow-hidden border border-gray-100 bg-gray-50 block"
                                                >
                                                    <img
                                                        src="{{ Storage::url($detail->foto_barang) }}"
                                                        alt="Foto barang customer"
                                                        class="w-full h-full object-cover"
                                                    >
                                                </button>
                                            @else
                                                <div class="w-20 h-20 rounded-xl border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-[11px] text-gray-400 text-center px-2">
                                                    Tidak ada foto
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="hidden md:grid grid-cols-[70px_100px_minmax(0,1fr)_160px_120px] gap-4 items-center px-4 py-4">

                                        <div class="font-semibold text-[#00372c]">
                                            {{ $index + 1 }}
                                        </div>

                                        <div>
                                            <div class="w-16 h-16 rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
                                                @if($detail->alat->foto_alat ?? false)
                                                    <img
                                                        src="{{ Storage::url($detail->alat->foto_alat) }}"
                                                        alt="{{ $detail->alat->nama_alat ?? 'Foto alat' }}"
                                                        class="w-full h-full object-cover"
                                                    >
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <svg width="22" height="22" fill="none" stroke="#94a3b8" stroke-width="1.8" viewBox="0 0 24 24">
                                                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                                                            <path d="M3.3 7L12 12l8.7-5"/>
                                                            <path d="M12 22V12"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="min-w-0">
                                            <p class="font-semibold text-[#00372c] truncate">
                                                {{ $detail->alat->nama_alat ?? '-' }}
                                            </p>

                                            <p class="text-sm text-gray-400 mt-1">
                                                {{ $detail->jumlah }} unit &middot;
                                                {{ $detail->lama_sewa }} hari &middot;
                                                Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}/hari
                                            </p>
                                        </div>

                                        <div class="font-bold text-[#085041]">
                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </div>

                                        <div>
                                            @if($detail->foto_barang)
                                                <button
                                                    type="button"
                                                    onclick="previewImage('{{ Storage::url($detail->foto_barang) }}', 'Foto Barang Customer - {{ $detail->alat->nama_alat ?? 'Barang' }}')"
                                                    class="w-16 h-16 rounded-xl overflow-hidden border border-gray-100 bg-gray-50 block hover:opacity-90 transition"
                                                >
                                                    <img
                                                        src="{{ Storage::url($detail->foto_barang) }}"
                                                        alt="Foto barang customer"
                                                        class="w-full h-full object-cover"
                                                    >
                                                </button>
                                            @else
                                                <div class="w-16 h-16 rounded-xl border border-dashed border-gray-200 bg-gray-50 flex items-center justify-center text-[10px] text-gray-400 text-center px-1">
                                                    Tidak ada
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @if($transaksi->catatan)
                        <div class="bg-gray-50 rounded-xl px-4 py-3 mt-6">
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-1">
                                Catatan
                            </p>

                            <p class="text-gray-600 text-sm">
                                {{ $transaksi->catatan }}
                            </p>
                        </div>
                    @endif

                </div>
            </div>

            <div class="min-w-0 space-y-5">

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-3">
                        Foto KTP / Identitas
                    </p>

                    @if($fotoKtp)
                        <button
                            type="button"
                            onclick="previewImage('{{ Storage::url($fotoKtp) }}', 'Foto KTP / Identitas Customer')"
                            class="block w-full group"
                        >
                            <div class="aspect-[4/3] rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
                                <img
                                    src="{{ Storage::url($fotoKtp) }}"
                                    alt="Foto KTP Customer"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                >
                            </div>
                        </button>

                        <p class="text-sm font-semibold text-[#00372c] mt-4">
                            Identitas untuk transaksi ini
                        </p>

                        <p class="text-xs text-gray-500 leading-relaxed mt-1">
                            Klik foto untuk melihat preview.
                        </p>
                    @else
                        <div class="bg-gray-50 border border-dashed border-gray-200 rounded-xl p-4">
                            <p class="text-sm text-gray-400">
                                Foto KTP tidak tersedia.
                            </p>
                        </div>
                    @endif
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-4">
                        Ringkasan Pembayaran
                    </p>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Total Sewa</span>
                            <span class="font-semibold text-[#00372c]">
                                Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                            </span>
                        </div>

                        @if(($transaksi->total_denda ?? 0) > 0)
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-red-500">Denda</span>
                                <span class="font-semibold text-red-500">
                                    Rp {{ number_format($transaksi->total_denda, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Metode</span>
                            <span class="text-sm font-semibold text-[#085041]">
                                Cash
                            </span>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-2">
                                Total Pembayaran
                            </p>

                            <p class="text-2xl font-bold text-[#085041]">
                                Rp {{ number_format($totalPembayaran, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                @if($transaksi->status === 'menunggu')
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                        <p class="text-xs text-gray-400 uppercase font-semibold tracking-wider mb-4">
                            Aksi Admin
                        </p>

                        <div class="space-y-3">
                            <form method="POST" action="{{ route('admin.transaksi.approve', $transaksi->id) }}">
                                @csrf

                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path d="M20 6L9 17l-5-5"/>
                                    </svg>
                                    Setujui Pesanan
                                </button>
                            </form>

                            <form method="POST" action="{{ route('admin.transaksi.tolak', $transaksi->id) }}" onsubmit="return confirm('Yakin ingin menolak pesanan ini?')">
                                @csrf

                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path d="M18 6L6 18"/>
                                        <path d="M6 6l12 12"/>
                                    </svg>
                                    Tolak Pesanan
                                </button>
                            </form>
                        </div>
                    </div>
                @elseif($transaksi->status === 'aktif')
                    <a
                        href="{{ route('admin.pengembalian.index') }}"
                        class="w-full flex items-center justify-center gap-2 bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm"
                    >
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <path d="M14 2v6h6"/>
                            <path d="M9 14h6"/>
                        </svg>
                        Verifikasi Pengembalian
                    </a>
                @endif

            </div>
        </div>
    </div>

    <div id="imagePreviewModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/80 px-6 py-10">
        <button
            type="button"
            onclick="closePreviewImage()"
            class="absolute top-5 right-5 w-11 h-11 rounded-full bg-white text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors"
            aria-label="Tutup preview"
        >
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M18 6L6 18"/>
                <path d="M6 6l12 12"/>
            </svg>
        </button>

        <div class="max-w-5xl w-full">
            <p id="imagePreviewTitle" class="text-white text-sm font-semibold mb-3 text-center"></p>

            <img
                id="imagePreviewTarget"
                src=""
                alt="Preview gambar"
                class="max-h-[78vh] w-auto max-w-full mx-auto rounded-2xl object-contain bg-white shadow-2xl"
            >
        </div>
    </div>

    <script>
        function previewImage(src, title = 'Preview Gambar') {
            const modal = document.getElementById('imagePreviewModal');
            const image = document.getElementById('imagePreviewTarget');
            const heading = document.getElementById('imagePreviewTitle');

            image.src = src;
            heading.textContent = title;

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closePreviewImage() {
            const modal = document.getElementById('imagePreviewModal');
            const image = document.getElementById('imagePreviewTarget');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            image.src = '';
            document.body.style.overflow = '';
        }

        document.getElementById('imagePreviewModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closePreviewImage();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePreviewImage();
            }
        });
    </script>

@endsection
