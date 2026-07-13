@extends('layouts.admin')

@section('title', isset($alat) ? 'Edit Alat' : 'Tambah Alat')
@section('page-title', isset($alat) ? 'Edit Alat' : 'Tambah Alat Baru')

@section('content')

    @php
        $jumlahSedangDisewa = isset($alat)
            ? max(0, $alat->stok_total - $alat->stok_tersedia)
            : 0;
    @endphp

    <div class="max-w-5xl">
        <a
            href="{{ route('admin.alat.index') }}"
            class="inline-flex items-center gap-2 text-[#085041] hover:text-[#00372c] text-sm font-semibold mb-6 transition-colors"
        >
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M15 18l-6-6 6-6"/>
            </svg>
            Kembali ke Katalog Alat
        </a>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-100 text-red-600 text-sm font-medium px-4 py-3 rounded-xl">
                <p class="font-bold mb-1">
                    Terjadi kesalahan:
                </p>

                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-100">
                <h2 class="text-xl font-bold text-[#00372c]">
                    {{ isset($alat) ? 'Informasi Alat' : 'Informasi Alat Baru' }}
                </h2>

                <p class="text-sm text-gray-500 mt-1.5">
                    Stok tersedia akan disesuaikan otomatis berdasarkan jumlah barang yang sedang disewa.
                </p>
            </div>

            <form
                method="POST"
                action="{{ isset($alat) ? route('admin.alat.update', $alat->id) : route('admin.alat.store') }}"
                enctype="multipart/form-data"
                class="px-8 py-8"
            >
                @csrf

                @if(isset($alat))
                    @method('PUT')
                @endif

                <input type="hidden" name="kategori" value="{{ old('kategori', $alat->kategori ?? 'Umum') }}">
                <input type="hidden" name="kondisi" value="{{ old('kondisi', $alat->kondisi ?? 'baik') }}">
                <input type="hidden" name="deskripsi" value="{{ old('deskripsi', $alat->deskripsi ?? '-') }}">

                <div class="space-y-6">
                    <div>
                        <label for="nama_alat" class="block text-sm font-semibold text-[#00372c] mb-2">
                            Nama Alat
                        </label>

                        <input
                            id="nama_alat"
                            type="text"
                            name="nama_alat"
                            value="{{ old('nama_alat', $alat->nama_alat ?? '') }}"
                            placeholder="Contoh: Tas Carrier 60L"
                            class="w-full h-14 px-4 rounded-xl border border-gray-200 text-sm text-[#00372c] placeholder-gray-400 focus:outline-none focus:border-[#085041] focus:ring-4 focus:ring-[#085041]/10 transition-all"
                            required
                        >

                        @error('nama_alat')
                            <p class="text-red-500 text-xs font-medium mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="stok_total" class="block text-sm font-semibold text-[#00372c] mb-2">
                                Stok Total
                            </label>

                            <input
                                id="stok_total"
                                type="number"
                                name="stok_total"
                                value="{{ old('stok_total', $alat->stok_total ?? '') }}"
                                min="{{ max(1, $jumlahSedangDisewa) }}"
                                placeholder="Contoh: 12"
                                class="w-full h-14 px-4 rounded-xl border border-gray-200 text-sm text-[#00372c] placeholder-gray-400 focus:outline-none focus:border-[#085041] focus:ring-4 focus:ring-[#085041]/10 transition-all"
                                required
                            >

                            @isset($alat)
                                <div class="mt-2 space-y-1">
                                    <p class="text-xs text-gray-400">
                                        Tersedia saat ini: {{ $alat->stok_tersedia }} dari {{ $alat->stok_total }} unit.
                                    </p>

                                    @if($jumlahSedangDisewa > 0)
                                        <p class="text-xs font-medium text-amber-600">
                                            {{ $jumlahSedangDisewa }} unit sedang disewa. Stok total minimal {{ $jumlahSedangDisewa }}.
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-400">
                                            Tidak ada unit yang sedang disewa.
                                        </p>
                                    @endif
                                </div>
                            @endisset

                            @error('stok_total')
                                <p class="text-red-500 text-xs font-medium mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="harga_per_hari" class="block text-sm font-semibold text-[#00372c] mb-2">
                                Harga Sewa / Hari
                            </label>

                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-gray-400">
                                    Rp
                                </span>

                                <input
                                    id="harga_per_hari"
                                    type="number"
                                    name="harga_per_hari"
                                    value="{{ old('harga_per_hari', $alat->harga_per_hari ?? '') }}"
                                    min="0"
                                    placeholder="25000"
                                    class="w-full h-14 pl-11 pr-4 rounded-xl border border-gray-200 text-sm text-[#00372c] placeholder-gray-400 focus:outline-none focus:border-[#085041] focus:ring-4 focus:ring-[#085041]/10 transition-all"
                                    required
                                >
                            </div>

                            @error('harga_per_hari')
                                <p class="text-red-500 text-xs font-medium mt-2">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#00372c] mb-3">
                            Foto Alat
                        </label>

                        <div class="relative w-44 h-44">
                            <button
                                type="button"
                                id="fotoPreviewButton"
                                onclick="handleFotoClick()"
                                data-has-image="{{ isset($alat) && $alat->foto_alat ? 'true' : 'false' }}"
                                class="group relative flex items-center justify-center w-44 h-44 rounded-2xl border border-dashed border-[#b7ddd0] bg-[#fbfefd] hover:bg-[#f7fcfa] hover:border-[#085041] transition-all overflow-hidden"
                            >
                                <img
                                    id="fotoPreview"
                                    src="{{ isset($alat) && $alat->foto_alat ? Storage::url($alat->foto_alat) : '' }}"
                                    alt="Preview foto alat"
                                    class="{{ isset($alat) && $alat->foto_alat ? 'block' : 'hidden' }} w-full h-full object-cover"
                                >

                                <div
                                    id="uploadPlaceholder"
                                    class="{{ isset($alat) && $alat->foto_alat ? 'hidden' : 'flex' }} flex-col items-center justify-center"
                                >
                                    <div class="w-12 h-12 rounded-full bg-[#e8f5f0] flex items-center justify-center text-[#085041] group-hover:scale-105 transition-transform">
                                        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                            <path d="M17 8l-5-5-5 5"/>
                                            <path d="M12 3v12"/>
                                        </svg>
                                    </div>
                                </div>

                                <div
                                    id="previewOverlay"
                                    class="{{ isset($alat) && $alat->foto_alat ? 'flex' : 'hidden' }} pointer-events-none absolute inset-0 items-center justify-center bg-black/30 opacity-0 transition-opacity duration-200 group-hover:opacity-100"
                                >
                                    <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </div>
                            </button>

                            <button
                                id="refreshPhotoButton"
                                type="button"
                                onclick="triggerFotoInput()"
                                class="{{ isset($alat) && $alat->foto_alat ? 'flex' : 'hidden' }} absolute -top-2 -right-2 w-10 h-10 rounded-xl bg-white border border-[#dbeae3] text-[#085041] shadow-sm items-center justify-center hover:bg-[#e8f5f0] hover:border-[#085041] transition-all"
                                aria-label="Ganti foto"
                                title="Ganti foto"
                            >
                                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M21.5 2v6h-6"/>
                                    <path d="M2.5 22v-6h6"/>
                                    <path d="M2 11.5a10 10 0 0 1 18.8-4.3"/>
                                    <path d="M22 12.5a10 10 0 0 1-18.8 4.2"/>
                                </svg>
                            </button>
                        </div>

                        <input
                            id="foto_alat"
                            type="file"
                            name="foto_alat"
                            accept="image/png,image/jpeg,image/jpg,image/webp"
                            class="hidden"
                            onchange="previewFotoAlat(this)"
                        >

                        <p class="text-xs text-gray-400 mt-3">
                            Format JPG, JPEG, PNG, atau WEBP. Maksimal 2MB.
                            @isset($alat)
                                Kosongkan jika tidak ingin mengganti foto.
                            @endisset
                        </p>

                        @error('foto_alat')
                            <p class="text-red-500 text-xs font-medium mt-2">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center bg-[#085041] hover:bg-[#00372c] text-white text-sm font-bold px-6 py-3 rounded-xl transition-all"
                    >
                        {{ isset($alat) ? 'Simpan Perubahan' : 'Tambah Alat' }}
                    </button>

                    <a
                        href="{{ route('admin.alat.index') }}"
                        class="inline-flex items-center justify-center border border-gray-200 hover:border-gray-300 hover:bg-gray-50 text-gray-600 hover:text-[#00372c] text-sm font-semibold px-6 py-3 rounded-xl transition-all"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div id="previewModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/80 px-6">
        <button
            type="button"
            onclick="closePreviewModal()"
            class="absolute top-5 right-5 w-11 h-11 rounded-full bg-white text-gray-700 hover:bg-gray-100 flex items-center justify-center transition-colors"
            aria-label="Tutup preview"
        >
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M18 6L6 18"/>
                <path d="M6 6l12 12"/>
            </svg>
        </button>

        <img
            id="previewModalImage"
            src=""
            alt="Preview foto alat"
            class="max-w-full max-h-[82vh] rounded-2xl object-contain bg-white shadow-2xl"
        >
    </div>

    <script>
        function triggerFotoInput() {
            document.getElementById('foto_alat').click();
        }

        function handleFotoClick() {
            const button = document.getElementById('fotoPreviewButton');
            const hasImage = button.dataset.hasImage === 'true';

            if (!hasImage) {
                triggerFotoInput();
                return;
            }

            openPreviewModal();
        }

        function previewFotoAlat(input) {
            const preview = document.getElementById('fotoPreview');
            const placeholder = document.getElementById('uploadPlaceholder');
            const refreshButton = document.getElementById('refreshPhotoButton');
            const button = document.getElementById('fotoPreviewButton');
            const overlay = document.getElementById('previewOverlay');

            if (!input.files || input.files.length === 0) {
                return;
            }

            const file = input.files[0];
            const imageUrl = URL.createObjectURL(file);

            preview.src = imageUrl;
            preview.classList.remove('hidden');
            preview.classList.add('block');

            placeholder.classList.add('hidden');
            placeholder.classList.remove('flex');

            refreshButton.classList.remove('hidden');
            refreshButton.classList.add('flex');

            overlay.classList.remove('hidden');
            overlay.classList.add('flex');

            button.dataset.hasImage = 'true';
        }

        function openPreviewModal() {
            const preview = document.getElementById('fotoPreview');
            const modal = document.getElementById('previewModal');
            const modalImage = document.getElementById('previewModalImage');

            if (!preview.src) {
                return;
            }

            modalImage.src = preview.src;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closePreviewModal() {
            const modal = document.getElementById('previewModal');
            const modalImage = document.getElementById('previewModalImage');

            modal.classList.add('hidden');
            modal.classList.remove('flex');
            modalImage.src = '';
            document.body.style.overflow = '';
        }

        document.getElementById('previewModal').addEventListener('click', function(event) {
            if (event.target === this) {
                closePreviewModal();
            }
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closePreviewModal();
            }
        });
    </script>

@endsection
