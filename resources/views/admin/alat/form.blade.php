@extends('layouts.admin')

@section('title', isset($alat) ? 'Edit Alat' : 'Tambah Alat')
@section('page-title', isset($alat) ? 'Edit Alat' : 'Tambah Alat Baru')

@section('content')

    <div class="max-w-2xl">

        <a href="{{ route('admin.alat.index') }}" class="inline-flex items-center gap-1 text-[#085041] hover:text-[#00372c] text-sm font-semibold mb-6 transition-colors">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            Kembali ke Katalog Alat
        </a>

        @if($errors->any())
            <div class="mb-6 bg-red-50 text-red-600 text-sm font-medium px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 p-8">
            <form method="POST" action="{{ isset($alat) ? route('admin.alat.update', $alat->id) : route('admin.alat.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @if(isset($alat))
                    @method('PUT')
                @endif

                <div>
                    <label class="block text-sm font-semibold text-[#00372c] mb-2">Nama Alat</label>
                    <input type="text" name="nama_alat" value="{{ old('nama_alat', $alat->nama_alat ?? '') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#085041] focus:border-transparent"
                        placeholder="Contoh: Tas Carrier 60L" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#00372c] mb-2">Kategori</label>
                    <input type="text" name="kategori" value="{{ old('kategori', $alat->kategori ?? '') }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#085041] focus:border-transparent"
                        placeholder="Contoh: Tas, Tenda, Penerangan" required>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-[#00372c] mb-2">Stok Total</label>
                        <input type="number" name="stok_total" value="{{ old('stok_total', $alat->stok_total ?? '') }}" min="1"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#085041] focus:border-transparent"
                            required>
                        @isset($alat)
                            <p class="text-xs text-gray-400 mt-1">Stok tersedia saat ini: {{ $alat->stok_tersedia }}</p>
                        @endisset
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-[#00372c] mb-2">Harga / Hari (Rp)</label>
                        <input type="number" name="harga_per_hari" value="{{ old('harga_per_hari', $alat->harga_per_hari ?? '') }}" min="0"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#085041] focus:border-transparent"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#00372c] mb-2">Kondisi</label>
                    <select name="kondisi" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#085041] focus:border-transparent" required>
                        <option value="">Pilih kondisi</option>
                        @foreach(['baik', 'rusak ringan', 'perbaikan'] as $opt)
                            <option value="{{ $opt }}" {{ old('kondisi', $alat->kondisi ?? '') == $opt ? 'selected' : '' }}>
                                {{ ucfirst($opt) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#00372c] mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#085041] focus:border-transparent"
                        placeholder="Spesifikasi singkat, catatan kondisi, dll (opsional)">{{ old('deskripsi', $alat->deskripsi ?? '') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-[#00372c] mb-2">Foto Alat</label>
                    @isset($alat)
                        @if($alat->foto_alat)
                            <img src="{{ Storage::url($alat->foto_alat) }}" alt="{{ $alat->nama_alat }}" class="w-32 h-32 object-cover rounded-xl mb-3 border border-gray-100">
                        @endif
                    @endisset
                    <input type="file" name="foto_alat" accept="image/png,image/jpeg,image/jpg"
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-[#e8f5f0] file:text-[#085041] file:font-semibold hover:file:bg-[#d8eee5] file:cursor-pointer cursor-pointer">
                    <p class="text-xs text-gray-400 mt-1">Format JPG/PNG, maksimal 2MB. {{ isset($alat) ? 'Kosongkan jika tidak ingin mengganti foto.' : '' }}</p>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="bg-[#085041] hover:bg-[#00372c] text-white font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                        {{ isset($alat) ? 'Simpan Perubahan' : 'Tambah Alat' }}
                    </button>
                    <a href="{{ route('admin.alat.index') }}" class="border border-gray-200 hover:bg-gray-50 text-gray-600 font-semibold px-6 py-3 rounded-xl transition-all text-sm">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>

@endsection