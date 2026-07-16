@extends('layouts.app')

@section('title', 'Form Rental')

@push('styles')
    @vite('resources/css/rental-form.css')
@endpush

@section('content')

<div class="rental-page">
    <div class="rental-container">

        @php
            $oldAlatIds = old('alat_id', ['']);

            if (!is_array($oldAlatIds) || count($oldAlatIds) === 0) {
                $oldAlatIds = [''];
            }

            $formStep = session('form_step', 1);

            if (!session()->has('form_step') && $errors->any()) {
                $stepTwoFields = ['nama_lengkap', 'no_telp', 'alamat', 'foto_ktp'];

                foreach ($stepTwoFields as $field) {
                    if ($errors->has($field)) {
                        $formStep = 2;
                        break;
                    }
                }
            }
        @endphp

        <div class="rental-hero">
            <h1>Form Rental Alat</h1>
            <p>Pilih alat, lengkapi data diri, lalu ajukan rental. Admin akan mengonfirmasi pesanan sebelum masa sewa dimulai.</p>
        </div>

        @if ($errors->any())
            <div class="error-box" id="formErrorSummary">
                <strong>Terjadi kesalahan:</strong>
                <ul id="formErrorList">
                    @foreach ($errors->getMessages() as $field => $messages)
                        @foreach ($messages as $message)
                            <li data-error-field="{{ $field }}">{{ $message }}</li>
                        @endforeach
                    @endforeach
                </ul>
                <p>Silakan perbaiki bagian yang ditandai. File foto perlu dipilih ulang jika sebelumnya gagal tersimpan.</p>
            </div>
        @endif

        <form action="{{ route('rental.store') }}" method="POST" enctype="multipart/form-data" id="rentalForm" data-initial-step="{{ $formStep }}" novalidate>
            @csrf
            <input type="hidden" name="request_token" value="{{ old('request_token', (string) \Illuminate\Support\Str::uuid()) }}">

            <div class="stepper-standalone">
                <div class="stepper">
                    <div class="step {{ $formStep == 1 ? 'active' : ($formStep > 1 ? 'done' : '') }}" id="stepNav1">
                        <div class="step-circle">1</div>
                        <div class="step-title">Detail Rental</div>
                    </div>

                    <div class="step {{ $formStep == 2 ? 'active' : ($formStep > 2 ? 'done' : '') }}" id="stepNav2">
                        <div class="step-circle">2</div>
                        <div class="step-title">Data Diri</div>
                    </div>

                    <div class="step {{ $formStep == 3 ? 'active' : '' }}" id="stepNav3">
                        <div class="step-circle">3</div>
                        <div class="step-title">Pembayaran</div>
                    </div>
                </div>
            </div>

            <div class="rental-layout" id="rentalLayout">
                <div class="wizard-card">
                    <div class="wizard-body">

                        <div class="step-content {{ $formStep == 1 ? '' : 'hidden' }}" id="step1">
                            <div class="section-heading">
                                <div>
                                    <h2>Detail Rental Alat</h2>
                                    <p>Pilih perlengkapan pendakian yang ingin disewa. Anda bisa menambahkan lebih dari satu barang dalam satu transaksi.</p>
                                </div>
                            </div>

                            <p class="form-note">
                                Satu durasi sewa berlaku untuk semua barang dalam satu transaksi.
                            </p>

                            <div id="duplicateToolNotice" class="duplicate-warning" role="alert" hidden>
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="M12 7v6"/>
                                    <path d="M12 17h.01"/>
                                </svg>
                                <span>Barang yang sama tidak boleh dipilih lebih dari satu kali.</span>
                            </div>

                            @error('stok')
                                <div class="error-box">
                                    <strong>Stok tidak mencukupi:</strong>
                                    <ul>
                                        <li>{{ $message }}</li>
                                    </ul>
                                </div>
                            @enderror

                            <div id="barang-list" class="barang-list">
                                @foreach ($oldAlatIds as $index => $oldAlatId)
                                    <div class="barang-item">

                                        <div class="barang-number">{{ $index + 1 }}</div>

                                        <div class="barang-select-wrapper">
                                            <div class="select-wrapper">
                                                <select name="alat_id[]" class="form-select alat-select @error('alat_id.' . $index) is-invalid @enderror" onchange="handleToolChange()" required>
                                                    <option value="">Pilih Barang</option>
                                                    @foreach ($alat as $item)
                                                        <option
                                                            value="{{ $item->id }}"
                                                            data-nama="{{ $item->nama_alat }}"
                                                            data-harga="{{ $item->harga_per_hari }}"
                                                            data-stok="{{ $item->stok_tersedia }}"
                                                            {{ (string) $oldAlatId === (string) $item->id ? 'selected' : '' }}
                                                        >
                                                            {{ $item->nama_alat }} - Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}/hari
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <svg class="select-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M6 9l6 6 6-6"/>
                                                </svg>
                                            </div>
                                            @error('alat_id.' . $index)
                                                <p class="field-error show" data-error-field="alat_id.{{ $index }}" style="margin-top: 4px;">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="barang-qty-wrapper">
                                            <div class="qty-box">
                                                <button type="button" onclick="kurangiJumlah(this)">
                                                    <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                        <path d="M5 12h14"/>
                                                    </svg>
                                                </button>
                                                <input
                                                    type="number"
                                                    name="jumlah[]"
                                                    value="{{ old('jumlah.' . $index, 1) }}"
                                                    min="1"
                                                    class="jumlah-input @error('jumlah.' . $index) is-invalid @enderror"
                                                    onchange="hitungTotal()"
                                                    required
                                                >
                                                <button type="button" onclick="tambahJumlah(this)">
                                                    <svg fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                                        <path d="M12 5v14"/>
                                                        <path d="M5 12h14"/>
                                                    </svg>
                                                </button>
                                            </div>
                                            @error('jumlah.' . $index)
                                                <p class="field-error show" style="margin-top: 4px;">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="barang-upload-wrapper">
                                            <div
                                                class="upload-box-compact {{ $errors->has('foto_barang.' . $index) ? 'upload-error' : '' }}"
                                                role="button"
                                                tabindex="0"
                                                aria-label="Pilih sumber foto barang"
                                                onclick="triggerNativeUpload(this.querySelector('.customer-upload-input'))"
                                                onkeydown="handleUploadBoxKey(event, this)"
                                            >
                                                <svg class="upload-icon-compact" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                    <path d="M17 8l-5-5-5 5"/>
                                                    <path d="M12 3v12"/>
                                                </svg>

                                                <img
                                                    class="upload-preview-compact"
                                                    src=""
                                                    alt="Preview"
                                                    onclick="event.stopPropagation(); previewImageModal(this)"
                                                >

                                                <input
                                                    type="file"
                                                    name="foto_barang[]"
                                                    class="customer-upload-input"
                                                    accept=".jpg,.jpeg,.jfif,.png,.webp,image/jpeg,image/png,image/webp"
                                                    onchange="previewFileCompact(this)"
                                                    required
                                                >
                                            </div>

                                            <button
                                                type="button"
                                                class="upload-refresh-btn"
                                                onclick="triggerFileInput(this)"
                                                title="Ganti foto"
                                            >
                                                <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2"/>
                                                </svg>
                                            </button>

                                            <p class="field-error {{ $errors->has('foto_barang.' . $index) ? 'show' : '' }}">
                                                @error('foto_barang.' . $index)
                                                    {{ $message }}
                                                @enderror
                                            </p>
                                        </div>

                                        <div class="barang-action-wrapper">
                                            <button
                                                type="button"
                                                class="btn-delete-icon btn-hapus"
                                                onclick="hapusBarang(this)"
                                                style="{{ count($oldAlatIds) === 1 ? 'display: none;' : '' }}"
                                                aria-label="Hapus barang"
                                                title="Hapus barang"
                                            >
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path d="M3 6h18"/>
                                                    <path d="M8 6V4h8v2"/>
                                                    <path d="M19 6l-1 14H6L5 6"/>
                                                    <path d="M10 11v6"/>
                                                    <path d="M14 11v6"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div id="uploadInlineNotice" class="upload-inline-notice" role="alert" aria-live="assertive">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="M12 7v6"/>
                                    <path d="M12 17h.01"/>
                                </svg>

                                <span id="uploadInlineNoticeMessage"></span>
                            </div>

                            <button type="button" class="btn btn-add" onclick="tambahBarang()">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M12 5v14"/>
                                    <path d="M5 12h14"/>
                                </svg>
                                Tambah Barang
                            </button>

                            <div class="form-group" style="margin-top: 24px;">
                                <label class="form-label">Lama Sewa</label>

                                <div class="select-wrapper">
                                    <select name="lama_sewa" id="lama_sewa" class="form-select @error('lama_sewa') is-invalid @enderror" onchange="hitungTotal()" required>
                                        <option value="">Pilih Lama Sewa</option>
                                        <option value="1" {{ old('lama_sewa') == 1 ? 'selected' : '' }}>1 Hari</option>
                                        <option value="2" {{ old('lama_sewa') == 2 ? 'selected' : '' }}>2 Hari</option>
                                        <option value="3" {{ old('lama_sewa') == 3 ? 'selected' : '' }}>3 Hari</option>
                                        <option value="4" {{ old('lama_sewa') == 4 ? 'selected' : '' }}>4 Hari</option>
                                        <option value="5" {{ old('lama_sewa') == 5 ? 'selected' : '' }}>5 Hari</option>
                                        <option value="6" {{ old('lama_sewa') == 6 ? 'selected' : '' }}>6 Hari</option>
                                        <option value="7" {{ old('lama_sewa') == 7 ? 'selected' : '' }}>7 Hari</option>
                                    </select>

                                    <svg class="select-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M6 9l6 6 6-6"/>
                                    </svg>
                                </div>

                                @error('lama_sewa')
                                    <p class="field-error show">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="step-content {{ $formStep == 2 ? '' : 'hidden' }}" id="step2">
                            <div class="section-heading">
                                <div>
                                    <h2>Lengkapi Data Diri</h2>
                                    <p>Data digunakan untuk verifikasi peminjaman dan keamanan barang rental.</p>
                                </div>
                            </div>

                            <div class="data-grid">
                                <div>
                                    <div class="two-column">
                                        <div class="form-group">
                                            <label class="form-label">Nama Lengkap</label>
                                            <input
                                                type="text"
                                                name="nama_lengkap"
                                                value="{{ old('nama_lengkap', $customer->nama_lengkap ?? '') }}"
                                                placeholder="Sesuai KTP"
                                                class="form-control @error('nama_lengkap') is-invalid @enderror"
                                                required
                                            >

                                            @error('nama_lengkap')
                                                <p class="field-error show">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label class="form-label">No. Telepon / WhatsApp</label>
                                            <input
                                                type="text"
                                                name="no_telp"
                                                value="{{ old('no_telp', $customer->no_telp ?? '') }}"
                                                placeholder="Contoh: 081234567890"
                                                class="form-control @error('no_telp') is-invalid @enderror"
                                                required
                                            >

                                            @error('no_telp')
                                                <p class="field-error show">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Alamat Lengkap</label>
                                        <textarea
                                            name="alamat"
                                            placeholder="Masukkan alamat lengkap Anda"
                                            class="form-textarea @error('alamat') is-invalid @enderror"
                                            required
                                        >{{ old('alamat', $customer->alamat ?? '') }}</textarea>

                                        @error('alamat')
                                            <p class="field-error show">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Upload Foto KTP</label>

                                        <div
                                            class="upload-box-large {{ $errors->has('foto_ktp') ? 'upload-error' : '' }}"
                                            role="button"
                                            tabindex="0"
                                            aria-label="Pilih sumber foto KTP"
                                            onclick="triggerNativeUpload(this.querySelector('.customer-upload-input'))"
                                            onkeydown="handleUploadBoxKey(event, this)"
                                        >
                                            <div class="upload-content-large">
                                                <div class="upload-icon-large">
                                                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                        <path d="M17 8l-5-5-5 5"/>
                                                        <path d="M12 3v12"/>
                                                    </svg>
                                                </div>

                                                <div class="upload-title-large">Tambahkan foto KTP</div>
                                                <div class="upload-desc-large">Ambil foto dari kamera atau pilih dari galeri. Maksimal 2MB.</div>
                                            </div>

                                            <img
                                                class="upload-preview-large"
                                                src=""
                                                alt="Preview Foto KTP"
                                                onclick="event.stopPropagation(); previewImageModal(this)"
                                            >

                                            <input
                                                type="file"
                                                name="foto_ktp"
                                                class="customer-upload-input"
                                                accept=".jpg,.jpeg,.jfif,.png,.webp,image/jpeg,image/png,image/webp"
                                                onchange="previewFileLarge(this)"
                                                required
                                            >
                                        </div>

                                        <p class="field-error {{ $errors->has('foto_ktp') ? 'show' : '' }}">
                                            @error('foto_ktp')
                                                {{ $message }}
                                            @enderror
                                        </p>
                                    </div>

                                    <div class="plain-info-text">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        </svg>

                                        <span>
                                            Data diri hanya digunakan untuk kebutuhan verifikasi peminjaman alat.
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="button-row">
                                <button type="button" class="btn btn-secondary" onclick="pindahStep(1)">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M19 12H5"/>
                                        <path d="M12 19l-7-7 7-7"/>
                                    </svg>
                                    Kembali
                                </button>

                                <button type="button" class="btn btn-primary" onclick="lanjutStep(3)">
                                    Lanjut Pembayaran
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M5 12h14"/>
                                        <path d="M12 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="step-content {{ $formStep == 3 ? '' : 'hidden' }}" id="step3">
                            <div class="section-heading">
                                <div>
                                    <h2>Konfirmasi Pembayaran</h2>
                                    <p>Periksa kembali data rental Anda. Pembayaran dilakukan secara tunai di kasir saat pengambilan barang.</p>
                                </div>
                            </div>

                            <div class="payment-unified-card">

                                <div class="payment-unified-section">
                                    <div class="payment-unified-label">Total Pembayaran</div>
                                    <div class="payment-unified-desc">Total dihitung dari harga sewa, jumlah barang, dan lama sewa.</div>
                                    <div class="payment-unified-amount" id="total-harga-final">Rp 0</div>
                                </div>

                                <div class="payment-unified-section">
                                    <div class="payment-method-header">
                                        <div>
                                            <div class="payment-unified-label">Metode Pembayaran</div>
                                            <div class="payment-unified-desc">Tidak perlu transfer. Bayar langsung saat pengambilan barang.</div>
                                        </div>
                                        <div class="payment-method-badge">Cash di Kasir</div>
                                    </div>
                                </div>

                                <div class="payment-unified-section payment-unified-section-last">
                                    <div class="payment-unified-label">Alur Pembayaran</div>
                                    <div class="payment-unified-desc">Langkah-langkah yang perlu Anda lakukan.</div>

                                    <div class="payment-flow-list">
                                        <div class="payment-flow-item">
                                            <div class="payment-flow-badge">1</div>
                                            <div class="payment-flow-content">
                                                <div class="payment-flow-title">Ajukan Rental</div>
                                                <div class="payment-flow-desc">Customer mengajukan rental melalui tombol di bawah.</div>
                                            </div>
                                        </div>

                                        <div class="payment-flow-item">
                                            <div class="payment-flow-badge">2</div>
                                            <div class="payment-flow-content">
                                                <div class="payment-flow-title">Kunjungi Outlet</div>
                                                <div class="payment-flow-desc">Datang ke outlet saat pengambilan barang dan lakukan pembayaran tunai.</div>
                                            </div>
                                        </div>

                                        <div class="payment-flow-item">
                                            <div class="payment-flow-badge">3</div>
                                            <div class="payment-flow-content">
                                                <div class="payment-flow-title">Konfirmasi Admin</div>
                                                <div class="payment-flow-desc">Admin mengonfirmasi transaksi, lalu masa sewa mulai berjalan.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="button-row button-row-step3">
                                <button type="button" class="btn btn-outline" onclick="pindahStep(2)">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M19 12H5"/>
                                        <path d="M12 19l-7-7 7-7"/>
                                    </svg>
                                    Kembali
                                </button>

                                <button type="button" class="btn btn-submit" onclick="submitForm()">
                                    Ajukan Rental
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M22 2L11 13"/>
                                        <path d="M22 2l-7 20-4-9-9-4 20-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="summary-wide"
                    id="summaryWide"
                    style="{{ $formStep == 1 ? 'display: block;' : 'display: none;' }}"
                >
                    <div class="summary-wide-title">Ringkasan Rental</div>
                    <div class="summary-wide-subtitle">
                        Ringkasan otomatis berubah sesuai pilihan Anda.
                    </div>

                    <div id="summary-wide-list" class="summary-wide-list">
                        <div class="summary-wide-empty">
                            Belum ada barang yang dipilih.
                        </div>
                    </div>

                    <div class="summary-wide-meta">
                        <div class="summary-wide-meta-item">
                            <span class="summary-wide-meta-label">Lama sewa:</span>
                            <strong
                                class="summary-wide-meta-value"
                                id="summary-wide-duration"
                            >
                                -
                            </strong>
                        </div>

                        <div class="summary-wide-meta-item">
                            <span class="summary-wide-meta-label">Metode:</span>
                            <strong class="summary-wide-meta-value">Cash</strong>
                        </div>
                    </div>

                    <div class="summary-wide-total">
                        <div class="summary-wide-total-label">Total</div>
                        <div
                            class="summary-wide-total-value"
                            id="total-harga-wide"
                        >
                            Rp 0
                        </div>
                    </div>

                    <div class="summary-wide-buttons">
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            <svg
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path d="M19 12H5"/>
                                <path d="M12 19l-7-7 7-7"/>
                            </svg>

                            Kembali ke Beranda
                        </a>

                        <button
                            type="button"
                            class="btn btn-primary"
                            onclick="lanjutStep(2)"
                        >
                            Lanjut Isi Data Diri

                            <svg
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path d="M5 12h14"/>
                                <path d="M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <aside class="summary-card" id="summaryCard" style="display: none;">
                    <div class="summary-title">Ringkasan Rental</div>
                    <div class="summary-subtitle">Ringkasan barang yang Anda pilih.</div>

                    <div id="summary-list" class="summary-list">
                        <div class="summary-empty">Belum ada barang yang dipilih.</div>
                    </div>

                    <div class="summary-row">
                        <span>Lama sewa</span>
                        <strong id="summary-duration">-</strong>
                    </div>

                    <div class="summary-row">
                        <span>Metode</span>
                        <strong>Cash</strong>
                    </div>

                    <div class="summary-total">
                        <small>Estimasi Total</small>
                        <strong id="total-harga">Rp 0</strong>
                    </div>

                    <div class="summary-mobile-actions">
                        <button type="button" class="btn btn-outline" onclick="pindahStep(2)">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M19 12H5"/>
                                <path d="M12 19l-7-7 7-7"/>
                            </svg>
                            Kembali
                        </button>

                        <button type="button" class="btn btn-submit" onclick="submitForm()">
                            Ajukan Rental
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M22 2L11 13"/>
                                <path d="M22 2l-7 20-4-9-9-4 20-7z"/>
                            </svg>
                        </button>
                    </div>
                </aside>
            </div>
        </form>
    </div>
</div>

<div id="uploadToast" class="upload-toast" role="alert" aria-live="assertive">
    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <circle cx="12" cy="12" r="9"/>
        <path d="M12 7v6"/>
        <path d="M12 17h.01"/>
    </svg>

    <div>
        <strong>File tidak dapat digunakan</strong>
        <span id="uploadToastMessage"></span>
    </div>
</div>

@endsection

@push('scripts')
    @vite('resources/js/rental-form.js')
@endpush
