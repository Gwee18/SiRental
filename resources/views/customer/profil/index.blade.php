@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
@php
    $fotoProfil = $customer->foto_profil;
    $fotoProfilUrl = $fotoProfil
        ? $customer->avatar_url
        : null;

    $inisial = strtoupper(
        substr($customer->nama_lengkap ?: 'P', 0, 1)
    );
@endphp

<section class="min-h-screen bg-[#f4f7f5] pt-[104px] pb-16 md:pt-[122px]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <div class="mb-7">
            <p class="text-sm font-semibold text-[#0f766e]">
                Akun Customer
            </p>

            <h1 class="mt-1 text-2xl md:text-3xl font-bold text-[#083b32]">
                Profil Saya
            </h1>

            <p class="mt-2 text-sm md:text-base text-gray-500">
                Perbarui data diri yang digunakan untuk proses rental.
            </p>
        </div>

        @if(session('success'))
            <div
                class="mb-5 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800"
                role="alert"
            >
                <svg
                    class="mt-0.5 h-5 w-5 shrink-0"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    viewBox="0 0 24 24"
                >
                    <circle cx="12" cy="12" r="9"/>
                    <path d="m8 12 2.5 2.5L16 9"/>
                </svg>

                <p class="text-sm font-medium">
                    {{ session('success') }}
                </p>
            </div>
        @endif

        <form
            action="{{ route('customer.profil.update') }}"
            method="POST"
            enctype="multipart/form-data"
            class="grid grid-cols-1 gap-6 lg:grid-cols-[300px_minmax(0,1fr)]"
        >
            @csrf
            @method('PUT')

            <aside class="h-fit rounded-3xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col items-center text-center">
                    <div
                        id="avatarFrame"
                        class="relative h-32 w-32 overflow-hidden rounded-full border-4 border-[#e5f3ee] bg-[#e8f5f0]"
                    >
                        @if($fotoProfilUrl)
                            <img
                                id="avatarPreview"
                                src="{{ $fotoProfilUrl }}"
                                alt="Foto profil {{ $customer->nama_lengkap }}"
                                class="h-full w-full object-cover"
                                referrerpolicy="no-referrer"
                                onerror="
                                    this.classList.add('hidden');
                                    document.getElementById('avatarInitial').classList.remove('hidden');
                                    document.getElementById('avatarInitial').classList.add('flex');
                                "
                            >
                        @else
                            <img
                                id="avatarPreview"
                                src=""
                                alt="Preview foto profil"
                                class="hidden h-full w-full object-cover"
                            >
                        @endif

                        <span
                            id="avatarInitial"
                            class="{{ $fotoProfilUrl ? 'hidden' : 'flex' }} absolute inset-0 items-center justify-center text-4xl font-bold text-[#085041]"
                        >
                            {{ $inisial }}
                        </span>
                    </div>

                    <h2 class="mt-4 text-lg font-bold text-[#083b32]">
                        {{ $customer->nama_lengkap }}
                    </h2>

                    <p class="mt-1 max-w-full truncate text-sm text-gray-500">
                        {{ $customer->email }}
                    </p>

                    <input
                        id="foto_profil"
                        type="file"
                        name="foto_profil"
                        class="hidden"
                        accept=".jpg,.jpeg,.jfif,.png,.webp,image/jpeg,image/png,image/webp"
                    >

                    <button
                        id="choosePhotoButton"
                        type="button"
                        class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-[#085041] px-4 py-3 text-sm font-semibold text-white transition hover:bg-[#00372c]"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path d="M14.5 4 16 6h4a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l1.5-2h5Z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>

                        Ganti Foto
                    </button>

                    <p class="mt-3 text-xs leading-relaxed text-gray-400">
                        Pada laptop akan membuka File Explorer. Pada HP akan
                        membuka pemilih kamera, galeri, atau file bawaan sistem.
                    </p>

                    <p class="mt-1 text-xs text-gray-400">
                        JPG, JPEG, JFIF, PNG, atau WEBP. Maksimal 2MB.
                    </p>

                    <p
                        id="photoClientError"
                        class="mt-3 hidden w-full rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-left text-xs font-medium text-red-700"
                    ></p>

                    @error('foto_profil')
                        <p class="mt-3 w-full rounded-xl border border-red-200 bg-red-50 px-3 py-2 text-left text-xs font-medium text-red-700">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </aside>

            <div class="rounded-3xl border border-gray-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="mb-6 flex items-center gap-3 border-b border-gray-100 pb-5">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#e8f5f0] text-[#085041]">
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M4 21a8 8 0 0 1 16 0"/>
                        </svg>
                    </div>

                    <div>
                        <h2 class="font-bold text-[#083b32]">
                            Informasi Pribadi
                        </h2>

                        <p class="text-sm text-gray-500">
                            Email akun tidak dapat diubah dari halaman ini.
                        </p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label
                            for="nama_lengkap"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Nama Lengkap
                        </label>

                        <input
                            id="nama_lengkap"
                            type="text"
                            name="nama_lengkap"
                            value="{{ old('nama_lengkap', $customer->nama_lengkap) }}"
                            maxlength="255"
                            autocomplete="name"
                            required
                            class="w-full rounded-xl border px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#0f766e] focus:ring-4 focus:ring-[#0f766e]/10 {{ $errors->has('nama_lengkap') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white' }}"
                        >

                        @error('nama_lengkap')
                            <p class="mt-2 text-xs font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="email"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Email
                        </label>

                        <div class="relative">
                            <svg
                                class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <rect x="3" y="5" width="18" height="14" rx="2"/>
                                <path d="m3 7 9 6 9-6"/>
                            </svg>

                            <input
                                id="email"
                                type="email"
                                value="{{ $customer->email }}"
                                disabled
                                class="w-full cursor-not-allowed rounded-xl border border-gray-200 bg-gray-100 py-3 pl-12 pr-4 text-sm text-gray-500"
                            >
                        </div>
                    </div>

                    <div>
                        <label
                            for="no_telp"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Nomor Telepon
                        </label>

                        <input
                            id="no_telp"
                            type="tel"
                            name="no_telp"
                            value="{{ old('no_telp', $customer->no_telp) }}"
                            maxlength="20"
                            autocomplete="tel"
                            placeholder="Contoh: 081234567890"
                            class="w-full rounded-xl border px-4 py-3 text-sm text-gray-800 outline-none transition focus:border-[#0f766e] focus:ring-4 focus:ring-[#0f766e]/10 {{ $errors->has('no_telp') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white' }}"
                        >

                        @error('no_telp')
                            <p class="mt-2 text-xs font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label
                            for="alamat"
                            class="mb-2 block text-sm font-semibold text-gray-700"
                        >
                            Alamat
                        </label>

                        <textarea
                            id="alamat"
                            name="alamat"
                            rows="5"
                            maxlength="1000"
                            autocomplete="street-address"
                            placeholder="Masukkan alamat lengkap"
                            class="w-full resize-none rounded-xl border px-4 py-3 text-sm leading-relaxed text-gray-800 outline-none transition focus:border-[#0f766e] focus:ring-4 focus:ring-[#0f766e]/10 {{ $errors->has('alamat') ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white' }}"
                        >{{ old('alamat', $customer->alamat) }}</textarea>

                        @error('alamat')
                            <p class="mt-2 text-xs font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-7 flex flex-col-reverse gap-3 border-t border-gray-100 pt-6 sm:flex-row sm:justify-end">
                    <a
                        href="{{ route('home') }}"
                        class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-5 py-3 text-sm font-semibold text-gray-600 transition hover:bg-gray-50"
                    >
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#085041] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#00372c]"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path d="M5 4h12l2 2v14H5z"/>
                            <path d="M8 4v6h8V4"/>
                            <path d="M8 20v-6h8v6"/>
                        </svg>

                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('foto_profil');
        const button = document.getElementById('choosePhotoButton');
        const preview = document.getElementById('avatarPreview');
        const initial = document.getElementById('avatarInitial');
        const errorBox = document.getElementById('photoClientError');

        const allowedExtensions = [
            'jpg',
            'jpeg',
            'jfif',
            'png',
            'webp'
        ];

        const allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        const maxFileSize = 2 * 1024 * 1024;

        button.addEventListener('click', function () {
            /*
             * Tanpa atribut capture:
             * - laptop/PC membuka File Explorer;
             * - HP membuka pemilih kamera/galeri/file bawaan sistem.
             */
            input.removeAttribute('capture');
            input.click();
        });

        input.addEventListener('change', function () {
            const file = input.files[0];

            hideError();

            if (!file) {
                return;
            }

            const extension = file.name
                .split('.')
                .pop()
                .toLowerCase();

            if (
                !allowedExtensions.includes(extension) ||
                !allowedMimeTypes.includes(file.type)
            ) {
                rejectFile(
                    'Format file tidak didukung. Gunakan JPG, JPEG, JFIF, PNG, atau WEBP.'
                );

                return;
            }

            if (file.size > maxFileSize) {
                rejectFile(
                    'Ukuran file terlalu besar. Maksimal 2MB.'
                );

                return;
            }

            const objectUrl = URL.createObjectURL(file);

            preview.src = objectUrl;
            preview.classList.remove('hidden');

            initial.classList.add('hidden');
            initial.classList.remove('flex');

            preview.onload = function () {
                URL.revokeObjectURL(objectUrl);
            };
        });

        function rejectFile(message) {
            input.value = '';
            errorBox.textContent = message;
            errorBox.classList.remove('hidden');
        }

        function hideError() {
            errorBox.textContent = '';
            errorBox.classList.add('hidden');
        }
    });
</script>
@endpush