<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Form Rental - SiRental</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Figtree", Arial, sans-serif;
            background: #f5f7f6;
            color: #191c1d;
        }

        .navbar {
            background: #00372c;
            color: white;
            height: 68px;
            padding: 0 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.06);
        }

        .navbar h1 {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 24px;
            font-size: 13px;
            font-weight: 700;
            opacity: 0.78;
        }

        .navbar a:hover {
            opacity: 1;
        }

        .container {
            max-width: 860px;
            margin: 42px auto 0;
            padding: 0 20px;
        }

        .page-title {
            margin-bottom: 24px;
            text-align: center;
        }

        .page-title h2 {
            color: #00372c;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.045em;
            margin-bottom: 8px;
        }

        .page-title p {
            color: #707975;
            font-size: 14px;
        }

        .stepper {
            position: relative;
            display: flex;
            justify-content: space-between;
            margin: 0 auto 30px;
            max-width: 560px;
        }

        .stepper::before {
            content: "";
            position: absolute;
            top: 18px;
            left: 86px;
            right: 86px;
            height: 1.5px;
            background: #d7ddda;
            z-index: 0;
        }

        .step {
            position: relative;
            z-index: 1;
            text-align: center;
            width: 112px;
        }

        .step-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #e1e3e4;
            color: #404945;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            font-size: 14px;
            font-weight: 800;
            border: 5px solid #f5f7f6;
        }

        .step.active .step-circle {
            background: #085041;
            color: white;
        }

        .step.done .step-circle {
            background: #086b53;
            color: white;
        }

        .step-title {
            font-size: 12px;
            font-weight: 800;
            color: #404945;
            letter-spacing: -0.01em;
        }

        .step.active .step-title,
        .step.done .step-title {
            color: #085041;
        }

        .card {
            background: white;
            border: 1px solid #dde6e1;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 18px 50px rgba(0, 55, 44, 0.06);
            margin-bottom: 24px;
        }

        .card h3 {
            color: #00372c;
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.04em;
            margin-bottom: 8px;
        }

        .card-subtitle {
            color: #707975;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 24px;
        }

        .hidden {
            display: none;
        }

        .info-box {
            background: #e8f5f0;
            border: 1px solid #68dbae;
            color: #085041;
            padding: 14px 16px;
            border-radius: 14px;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 22px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #191c1d;
            font-size: 14px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        input,
        select,
        textarea {
            width: 100%;
            height: 50px;
            padding: 0 14px;
            border: 1px solid #bfc9c4;
            border-radius: 12px;
            background: white;
            color: #191c1d;
            font-size: 14px;
            font-family: inherit;
            outline: none;
        }

        textarea {
            height: 115px;
            padding-top: 12px;
            resize: vertical;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #085041;
            box-shadow: 0 0 0 3px rgba(104, 219, 174, 0.22);
        }

        .is-invalid {
            border-color: #ba1a1a !important;
            box-shadow: 0 0 0 3px rgba(186, 26, 26, 0.12) !important;
        }

        .field-error {
            display: none;
            color: #ba1a1a;
            font-size: 12px;
            font-weight: 700;
            margin-top: 8px;
            line-height: 1.5;
        }

        .field-error.show {
            display: block;
        }

        .select-wrapper {
            position: relative;
        }

        .select-wrapper select {
            appearance: none;
            -webkit-appearance: none;
            padding-right: 46px;
        }

        .select-icon {
            position: absolute;
            top: 50%;
            right: 14px;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            pointer-events: none;
            color: #085041;
        }

        .barang-item {
            background: #fbfdfc;
            border: 1px solid #dfe7e3;
            border-radius: 18px;
            padding: 22px;
            margin-bottom: 18px;
        }

        .barang-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .barang-header strong {
            color: #085041;
            font-size: 15px;
            font-weight: 800;
        }

        .barang-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 18px;
        }

        .qty-box {
            height: 50px;
            display: flex;
            border: 1px solid #bfc9c4;
            border-radius: 12px;
            overflow: hidden;
            background: white;
        }

        .qty-box button {
            width: 46px;
            border: none;
            background: white;
            color: #085041;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .qty-box button:hover {
            background: #e8f5f0;
        }

        .qty-box button svg {
            width: 18px;
            height: 18px;
        }

        .qty-box input {
            border: none;
            box-shadow: none;
            text-align: center;
            font-weight: 800;
            padding: 0;
        }

        .price-info {
            margin-top: 10px;
            color: #707975;
            font-size: 13px;
            line-height: 1.5;
        }

        .upload-box {
            position: relative;
            border: 2px dashed #bfc9c4;
            border-radius: 16px;
            background: #f8f9fa;
            cursor: pointer;
            transition: 0.2s;
            min-height: 170px;
            overflow: hidden;
        }

        .upload-box:hover {
            border-color: #085041;
            background: #e8f5f0;
        }

        .upload-box.upload-error {
            border-color: #ba1a1a;
            background: #fff5f5;
        }

        .upload-content {
            min-height: 170px;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
            gap: 8px;
        }

        .upload-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #e8f5f0;
            color: #085041;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .upload-title {
            color: #191c1d;
            font-size: 14px;
            font-weight: 800;
        }

        .upload-desc {
            color: #707975;
            font-size: 12px;
        }

        .upload-box input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
            height: 100%;
            z-index: 3;
        }

        .preview-wrap {
            display: none;
            position: relative;
            width: 100%;
        }

        .preview-image {
            width: 100%;
            height: 230px;
            object-fit: cover;
            display: block;
        }

        .preview-label {
            position: absolute;
            left: 50%;
            bottom: 14px;
            transform: translateX(-50%);
            background: rgba(0, 55, 44, 0.86);
            color: white;
            padding: 8px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .upload-box.has-image {
            border-style: solid;
            background: white;
        }

        .upload-box.has-image .upload-content {
            display: none;
        }

        .upload-box.has-image .preview-wrap {
            display: block;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .total-box {
            background: linear-gradient(135deg, #dff7ef, #effaf5);
            border: 1px solid #a0f3d4;
            border-radius: 18px;
            padding: 20px;
            margin-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .total-box small {
            display: block;
            color: #086b53;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
        }

        .total-box strong {
            color: #00372c;
            font-size: 31px;
            font-weight: 800;
        }

        .total-note {
            background: rgba(255, 255, 255, 0.75);
            color: #086b53;
            padding: 9px 13px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            white-space: nowrap;
        }

        .button-row {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            margin-top: 28px;
        }

        .btn {
            border: none;
            border-radius: 14px;
            padding: 14px 20px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            font-family: inherit;
        }

        .btn svg {
            width: 18px;
            height: 18px;
        }

        .btn-primary {
            background: #085041;
            color: white;
        }

        .btn-primary:hover {
            background: #00372c;
        }

        .btn-secondary {
            background: white;
            color: #085041;
            border: 1px solid #085041;
        }

        .btn-secondary:hover {
            background: #e8f5f0;
        }

        .btn-add {
            background: white;
            color: #085041;
            border: 1px solid #a0f3d4;
        }

        .btn-add:hover {
            background: #e8f5f0;
        }

        .btn-danger {
            background: #ffdad6;
            color: #93000a;
            padding: 9px 13px;
        }

        .btn-submit {
            background: #b9f1d8;
            color: #00372c;
            box-shadow: 0 10px 22px rgba(104, 219, 174, 0.22);
        }

        .btn-submit:hover {
            background: #9be8c5;
        }

        .error-box {
            background: #ffdad6;
            color: #93000a;
            border: 1px solid #ffb4ab;
            padding: 16px 18px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .error-box ul {
            margin-left: 20px;
            margin-top: 8px;
        }

        .error-box p {
            margin-top: 10px;
            font-size: 13px;
            line-height: 1.5;
        }

        /* STEP 3 - CLEAN PAYMENT DESIGN */
        #step3 {
            padding: 0;
            overflow: hidden;
            background: transparent;
            border: none;
            box-shadow: none;
        }

        .payment-wrap {
            padding: 0;
        }

        .payment-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 26px;
        }

        .payment-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            background: #d8f3e6;
            color: #085041;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 14px;
        }

        .payment-badge svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        .payment-heading h3 {
            color: #00372c;
            font-size: 30px;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 12px;
            letter-spacing: -0.045em;
        }

        .payment-heading p {
            max-width: 620px;
            color: #4f5d57;
            font-size: 15px;
            line-height: 1.75;
        }

        .payment-top-icon {
            color: #00372c;
            flex-shrink: 0;
            margin-top: 8px;
        }

        .payment-top-icon svg {
            width: 42px;
            height: 42px;
            stroke-width: 1.7;
        }

        .payment-summary {
            display: grid;
            grid-template-columns: 1.55fr 1fr;
            gap: 18px;
            margin-bottom: 18px;
        }

        .payment-card {
            background: #ffffff;
            border: 1px solid #d9e2de;
            border-radius: 22px;
        }

        .payment-total-card {
            padding: 28px 28px 24px;
            min-height: 240px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .payment-total-label {
            color: #9aa5a0;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
        }

        .payment-total-value {
            color: #00372c;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.05;
        }

        .payment-total-divider {
            border-top: 1px solid #dfe5e2;
            margin: 22px 0 14px;
        }

        .payment-total-note {
            color: #6f7b76;
            font-size: 13px;
            line-height: 1.7;
            font-weight: 500;
        }

        .payment-method-card {
            padding: 26px;
            min-height: 240px;
            display: flex;
            flex-direction: column;
        }

        .payment-method-icon {
            color: #085041;
            margin-bottom: 18px;
        }

        .payment-method-icon svg {
            width: 32px;
            height: 32px;
            stroke-width: 1.8;
        }

        .payment-method-label {
            color: #9aa5a0;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-bottom: 10px;
        }

        .payment-method-title {
            color: #00372c;
            font-size: 18px;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 10px;
        }

        .payment-method-desc {
            color: #5f6c66;
            font-size: 14px;
            line-height: 1.7;
            margin-bottom: 16px;
        }

        .payment-method-tip {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            color: #00372c;
            font-size: 13px;
            font-weight: 700;
            line-height: 1.6;
            margin-top: auto;
        }

        .payment-method-tip svg {
            width: 14px;
            height: 14px;
            color: #085041;
            flex-shrink: 0;
            margin-top: 3px;
        }

        .payment-flow-card {
            padding: 26px 28px;
            margin-bottom: 26px;
        }

        .payment-flow-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
        }

        .payment-flow-icon {
            color: #085041;
            flex-shrink: 0;
        }

        .payment-flow-icon svg {
            width: 28px;
            height: 28px;
            stroke-width: 1.8;
        }

        .payment-flow-title {
            color: #00372c;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.3;
        }

        .payment-flow-list {
            display: grid;
            gap: 18px;
        }

        .payment-flow-item {
            display: grid;
            grid-template-columns: 26px 1fr;
            gap: 14px;
            align-items: start;
        }

        .payment-flow-number {
            color: #00372c;
            font-size: 19px;
            font-weight: 700;
            line-height: 1;
            padding-top: 2px;
        }

        .payment-flow-item strong {
            display: block;
            color: #00372c;
            font-size: 15px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .payment-flow-item p {
            color: #5f6c66;
            font-size: 14px;
            line-height: 1.65;
        }

        .payment-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .payment-actions .btn {
            min-width: 112px;
        }

        .payment-actions .btn-submit {
            min-width: 165px;
        }

        footer {
            background: #00372c;
            color: white;
            padding: 28px 40px;
            margin-top: 56px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        footer p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            margin-top: 4px;
        }

        footer a {
            color: rgba(255, 255, 255, 0.75);
            text-decoration: none;
            margin-left: 20px;
            font-size: 13px;
            font-weight: 700;
        }

        @media (max-width: 768px) {
            .navbar {
                height: auto;
                padding: 18px 20px;
                flex-direction: column;
                gap: 14px;
                align-items: flex-start;
            }

            .navbar a {
                margin-left: 0;
                margin-right: 14px;
            }

            .container {
                margin-top: 32px;
            }

            .page-title h2 {
                font-size: 28px;
            }

            .stepper::before {
                left: 50px;
                right: 50px;
            }

            .step {
                width: 90px;
            }

            .step-title {
                font-size: 11px;
            }

            .card {
                padding: 22px;
            }

            .barang-grid,
            .two-column,
            .payment-summary {
                grid-template-columns: 1fr;
            }

            .total-box {
                flex-direction: column;
                align-items: flex-start;
            }

            .total-note {
                white-space: normal;
            }

            .button-row {
                flex-direction: column;
            }

            .button-row .btn {
                width: 100%;
            }

            .payment-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .payment-total-card,
            .payment-method-card {
                min-height: auto;
            }

            .payment-actions {
                flex-direction: column;
            }

            .payment-actions .btn {
                width: 100%;
            }

            .payment-heading h3 {
                font-size: 24px;
            }

            .payment-total-value {
                font-size: 30px;
            }

            footer {
                flex-direction: column;
                gap: 16px;
                text-align: center;
            }

            footer a {
                margin: 0 8px;
            }
        }
    </style>
</head>

<body>
    <div class="navbar">
        <h1>SiRental</h1>

        <div>
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('customer.transaksi.index') }}">Pesanan Saya</a>
            <a href="{{ route('customer.profil') }}">Profil</a>
        </div>
    </div>

    <div class="container">
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

        <div class="page-title">
            <h2>Form Rental Alat</h2>
            <p>Pilih alat, isi data diri, lalu ajukan rental untuk dikonfirmasi admin.</p>
        </div>

        @if ($errors->any())
            <div class="error-box">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <p>
                    Silakan perbaiki bagian yang ditandai. Data yang sudah diisi tetap tersimpan, kecuali file foto yang perlu dipilih ulang.
                </p>
            </div>
        @endif

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

        <form action="{{ route('rental.store') }}" method="POST" enctype="multipart/form-data" id="rentalForm" novalidate>
            @csrf

            {{-- STEP 1 --}}
            <div class="card step-content {{ $formStep == 1 ? '' : 'hidden' }}" id="step1">
                <h3>Detail Rental Alat</h3>
                <p class="card-subtitle">
                    Pilih perlengkapan pendakian yang ingin disewa. Anda bisa menambahkan lebih dari satu barang dalam satu transaksi.
                </p>

                <div class="info-box">
                    Satu durasi sewa berlaku untuk semua barang dalam satu transaksi.
                </div>

                @error('stok')
                    <div class="error-box">
                        <strong>Stok tidak mencukupi:</strong>
                        <ul>
                            <li>{{ $message }}</li>
                        </ul>
                    </div>
                @enderror

                <div id="barang-list">
                    @foreach ($oldAlatIds as $index => $oldAlatId)
                        <div class="barang-item">
                            <div class="barang-header">
                                <strong>Barang {{ $index + 1 }}</strong>
                                <button type="button" class="btn btn-danger btn-hapus" onclick="hapusBarang(this)" style="{{ count($oldAlatIds) === 1 ? 'display: none;' : '' }}">
                                    Hapus
                                </button>
                            </div>

                            <div class="barang-grid">
                                <div class="form-group">
                                    <label>Pilih Barang</label>

                                    <div class="select-wrapper">
                                        <select name="alat_id[]" class="alat-select @error('alat_id.' . $index) is-invalid @enderror" onchange="hitungTotal()" required>
                                            <option value="">Pilih Barang</option>
                                            @foreach ($alat as $item)
                                                <option
                                                    value="{{ $item->id }}"
                                                    data-harga="{{ $item->harga_per_hari }}"
                                                    data-stok="{{ $item->stok_tersedia }}"
                                                    {{ (string) $oldAlatId === (string) $item->id ? 'selected' : '' }}
                                                >
                                                    {{ $item->nama_alat }}
                                                    - Rp {{ number_format($item->harga_per_hari, 0, ',', '.') }}/hari
                                                    - Stok {{ $item->stok_tersedia }}
                                                </option>
                                            @endforeach
                                        </select>

                                        <svg class="select-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M6 9l6 6 6-6"/>
                                        </svg>
                                    </div>

                                    <div class="price-info">
                                        Pilih barang untuk melihat harga, stok, dan subtotal.
                                    </div>

                                    @error('alat_id.' . $index)
                                        <p class="field-error show">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Jumlah</label>
                                    <div class="qty-box">
                                        <button type="button" onclick="kurangiJumlah(this)">
                                            <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
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
                                            <svg fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                                <path d="M12 5v14"/>
                                                <path d="M5 12h14"/>
                                            </svg>
                                        </button>
                                    </div>

                                    @error('jumlah.' . $index)
                                        <p class="field-error show">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Upload Foto Barang</label>

                                <div class="upload-box {{ $errors->has('foto_barang.' . $index) ? 'upload-error' : '' }}">
                                    <div class="upload-content">
                                        <div class="upload-icon">
                                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                                <path d="M17 8l-5-5-5 5"/>
                                                <path d="M12 3v12"/>
                                            </svg>
                                        </div>
                                        <div class="upload-title">Klik untuk upload foto barang</div>
                                        <div class="upload-desc">Format JPG, PNG, atau WEBP, maksimal 2MB</div>
                                    </div>

                                    <div class="preview-wrap">
                                        <img class="preview-image" src="" alt="Preview Foto Barang">
                                        <div class="preview-label">Klik gambar untuk mengganti foto</div>
                                    </div>

                                    <input
                                        type="file"
                                        name="foto_barang[]"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                        onchange="previewFile(this)"
                                        required
                                    >
                                </div>

                                <p class="field-error {{ $errors->has('foto_barang.' . $index) ? 'show' : '' }}">
                                    @error('foto_barang.' . $index)
                                        {{ $message }}
                                    @enderror
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button type="button" class="btn btn-add" onclick="tambahBarang()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 5v14"/>
                        <path d="M5 12h14"/>
                    </svg>
                    Tambah Barang
                </button>

                <div class="form-group" style="margin-top: 24px;">
                    <label>Lama Sewa</label>

                    <div class="select-wrapper">
                        <select name="lama_sewa" id="lama_sewa" class="@error('lama_sewa') is-invalid @enderror" onchange="hitungTotal()" required>
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

                <div class="total-box">
                    <div>
                        <small>Estimasi Total Harga</small>
                        <strong id="total-harga">Rp 0</strong>
                    </div>

                    <div class="total-note">
                        Dihitung dari harga per hari × jumlah × lama sewa
                    </div>
                </div>

                <div class="button-row">
                    <a href="{{ route('home') }}" class="btn btn-secondary">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M19 12H5"/>
                            <path d="M12 19l-7-7 7-7"/>
                        </svg>
                        Kembali ke Beranda
                    </a>

                    <button type="button" class="btn btn-primary" onclick="lanjutStep(2)">
                        Lanjut Isi Data Diri
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 12h14"/>
                            <path d="M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- STEP 2 --}}
            <div class="card step-content {{ $formStep == 2 ? '' : 'hidden' }}" id="step2">
                <h3>Lengkapi Data Diri</h3>
                <p class="card-subtitle">
                    Masukkan informasi yang valid untuk kebutuhan verifikasi peminjaman alat.
                </p>

                <div class="two-column">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input
                            type="text"
                            name="nama_lengkap"
                            value="{{ old('nama_lengkap', Auth::guard('web')->user()->nama_lengkap ?? '') }}"
                            placeholder="Sesuai KTP"
                            class="@error('nama_lengkap') is-invalid @enderror"
                            required
                        >

                        @error('nama_lengkap')
                            <p class="field-error show">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>No. Telepon / WhatsApp</label>
                        <input
                            type="text"
                            name="no_telp"
                            value="{{ old('no_telp', Auth::guard('web')->user()->no_telp ?? '') }}"
                            placeholder="Contoh: 081234567890"
                            class="@error('no_telp') is-invalid @enderror"
                            required
                        >

                        @error('no_telp')
                            <p class="field-error show">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <textarea
                        name="alamat"
                        placeholder="Masukkan alamat lengkap Anda"
                        class="@error('alamat') is-invalid @enderror"
                        required
                    >{{ old('alamat', Auth::guard('web')->user()->alamat ?? '') }}</textarea>

                    @error('alamat')
                        <p class="field-error show">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Upload Foto KTP</label>

                    <div class="upload-box {{ $errors->has('foto_ktp') ? 'upload-error' : '' }}">
                        <div class="upload-content">
                            <div class="upload-icon">
                                <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                                    <path d="M7 8h5"/>
                                    <path d="M7 12h10"/>
                                    <path d="M7 16h7"/>
                                </svg>
                            </div>
                            <div class="upload-title">Klik untuk upload foto KTP</div>
                            <div class="upload-desc">Format JPG, PNG, atau WEBP. Maksimal 2MB.</div>
                        </div>

                        <div class="preview-wrap">
                            <img class="preview-image" src="" alt="Preview Foto KTP">
                            <div class="preview-label">Klik gambar untuk mengganti foto</div>
                        </div>

                        <input
                            type="file"
                            name="foto_ktp"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            onchange="previewFile(this)"
                            required
                        >
                    </div>

                    <p class="field-error {{ $errors->has('foto_ktp') ? 'show' : '' }}">
                        @error('foto_ktp')
                            {{ $message }}
                        @enderror
                    </p>
                </div>

                <div class="info-box">
                    Data diri digunakan untuk verifikasi peminjaman dan keamanan barang rental.
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

            {{-- STEP 3 --}}
            <div class="card step-content {{ $formStep == 3 ? '' : 'hidden' }}" id="step3">
                <div class="payment-wrap">
                    <div class="payment-top">
                        <div class="payment-heading">
                            <div class="payment-badge">
                                <svg fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                    <path d="M20 6L9 17l-5-5"/>
                                </svg>
                                <span>Langkah Terakhir</span>
                            </div>

                            <h3>Pembayaran Tunai</h3>
                            <p>
                                Periksa kembali total rental Anda. Pembayaran dilakukan secara cash di kasir saat
                                pengambilan barang. Setelah pembayaran diterima, admin akan mengonfirmasi pesanan.
                            </p>
                        </div>

                        <div class="payment-top-icon">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <rect x="3" y="6" width="18" height="12" rx="2"/>
                                <path d="M7 10h10"/>
                                <path d="M7 14h6"/>
                                <path d="M17 14h.01"/>
                            </svg>
                        </div>
                    </div>

                    <div class="payment-summary">
                        <div class="payment-card payment-total-card">
                            <div>
                                <div class="payment-total-label">
                                    Total Pembayaran
                                </div>

                                <div class="payment-total-value" id="total-harga-final">
                                    Rp 0
                                </div>
                            </div>

                            <div>
                                <div class="payment-total-divider"></div>
                                <div class="payment-total-note">
                                    Total dihitung dari harga sewa per hari, jumlah barang, dan lama sewa yang dipilih.
                                </div>
                            </div>
                        </div>

                        <div class="payment-card payment-method-card">
                            <div class="payment-method-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <rect x="4" y="5" width="16" height="14" rx="2"/>
                                    <path d="M8 9h8"/>
                                    <path d="M8 13h3"/>
                                    <path d="M15 13h1"/>
                                    <path d="M15 16h1"/>
                                    <path d="M8 16h3"/>
                                </svg>
                            </div>

                            <div class="payment-method-label">
                                Metode Pembayaran
                            </div>

                            <div class="payment-method-title">
                                Cash di Kasir
                            </div>

                            <div class="payment-method-desc">
                                Tidak perlu transfer. Cukup lakukan pembayaran langsung saat mengambil barang.
                            </div>

                            <div class="payment-method-tip">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="9"/>
                                    <path d="M12 8h.01"/>
                                    <path d="M11.5 12h1v4h1"/>
                                </svg>
                                <span>Siapkan uang pas jika memungkinkan</span>
                            </div>
                        </div>
                    </div>

                    <div class="payment-card payment-flow-card">
                        <div class="payment-flow-header">
                            <div class="payment-flow-icon">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M4 6h16"/>
                                    <path d="M4 12h16"/>
                                    <path d="M4 18h16"/>
                                    <path d="M8 6v12"/>
                                </svg>
                            </div>

                            <div class="payment-flow-title">
                                Alur Pembayaran dan Konfirmasi
                            </div>
                        </div>

                        <div class="payment-flow-list">
                            <div class="payment-flow-item">
                                <div class="payment-flow-number">1</div>
                                <div>
                                    <strong>Ajukan Rental</strong>
                                    <p>Ajukan rental melalui tombol di bawah.</p>
                                </div>
                            </div>

                            <div class="payment-flow-item">
                                <div class="payment-flow-number">2</div>
                                <div>
                                    <strong>Kunjungi Outlet</strong>
                                    <p>Datang ke kasir saat pengambilan barang dan lakukan pembayaran tunai.</p>
                                </div>
                            </div>

                            <div class="payment-flow-item">
                                <div class="payment-flow-number">3</div>
                                <div>
                                    <strong>Konfirmasi Admin</strong>
                                    <p>Admin mengonfirmasi transaksi, lalu masa sewa akan mulai berjalan.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="payment-actions">
                        <button type="button" class="btn btn-secondary" onclick="pindahStep(2)">
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
        </form>
    </div>

    <footer>
        <div>
            <strong>SiRental</strong>
            <p>Rental alat pendakian Surabaya.</p>
        </div>

        <div>
            <a href="{{ route('home') }}">Beranda</a>
            <a href="https://wa.me/6281231793810" target="_blank">Hubungi Kami</a>
        </div>
    </footer>

    <script>
        const initialStep = {{ (int) $formStep }};
        const maxFileSize = 2 * 1024 * 1024;
        const allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        const allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

        function formatRupiah(angka) {
            return 'Rp ' + angka.toLocaleString('id-ID');
        }

        function getFileExtension(filename) {
            return filename.split('.').pop().toLowerCase();
        }

        function showUploadError(input, message) {
            let uploadBox = input.closest('.upload-box');
            let formGroup = input.closest('.form-group');
            let errorText = formGroup.querySelector('.field-error');

            uploadBox.classList.add('upload-error');

            if (errorText) {
                errorText.innerHTML = message;
                errorText.classList.add('show');
            }
        }

        function clearUploadError(input) {
            let uploadBox = input.closest('.upload-box');
            let formGroup = input.closest('.form-group');
            let errorText = formGroup.querySelector('.field-error');

            uploadBox.classList.remove('upload-error');

            if (errorText) {
                errorText.innerHTML = '';
                errorText.classList.remove('show');
            }
        }

        function resetPreview(input) {
            let uploadBox = input.closest('.upload-box');
            let previewImage = uploadBox.querySelector('.preview-image');

            previewImage.src = '';
            uploadBox.classList.remove('has-image');
        }

        function validateFileInput(input) {
            clearUploadError(input);

            if (!input.files || !input.files[0]) {
                showUploadError(input, 'File wajib dipilih.');
                return false;
            }

            let file = input.files[0];
            let extension = getFileExtension(file.name);
            let mimeTypeValid = allowedMimeTypes.includes(file.type);
            let extensionValid = allowedExtensions.includes(extension);

            if (!mimeTypeValid && !extensionValid) {
                input.value = '';
                resetPreview(input);
                showUploadError(input, 'Format file harus JPG, JPEG, PNG, atau WEBP.');
                return false;
            }

            if (file.size > maxFileSize) {
                input.value = '';
                resetPreview(input);
                showUploadError(input, 'Ukuran file maksimal 2MB.');
                return false;
            }

            return true;
        }

        function hitungTotal() {
            let total = 0;
            let lamaSewa = parseInt(document.getElementById('lama_sewa').value) || 0;
            let items = document.querySelectorAll('.barang-item');

            items.forEach(function(item) {
                let select = item.querySelector('.alat-select');
                let jumlahInput = item.querySelector('.jumlah-input');
                let info = item.querySelector('.price-info');

                let option = select.options[select.selectedIndex];
                let harga = parseInt(option.getAttribute('data-harga')) || 0;
                let stok = parseInt(option.getAttribute('data-stok')) || 0;
                let jumlah = parseInt(jumlahInput.value) || 1;

                if (jumlah < 1) {
                    jumlah = 1;
                    jumlahInput.value = 1;
                }

                if (stok > 0 && jumlah > stok) {
                    jumlah = stok;
                    jumlahInput.value = stok;
                    alert('Jumlah tidak boleh melebihi stok tersedia.');
                }

                if (harga > 0) {
                    let subtotal = harga * jumlah * lamaSewa;
                    total += subtotal;

                    info.innerHTML =
                        'Harga: ' + formatRupiah(harga) + '/hari' +
                        ' | Stok: ' + stok +
                        ' | Subtotal: ' + formatRupiah(subtotal);
                } else {
                    info.innerHTML = 'Pilih barang untuk melihat harga, stok, dan subtotal.';
                }
            });

            document.getElementById('total-harga').innerHTML = formatRupiah(total);
            document.getElementById('total-harga-final').innerHTML = formatRupiah(total);
        }

        function tambahJumlah(button) {
            let input = button.parentElement.querySelector('input');
            input.value = parseInt(input.value || 1) + 1;
            hitungTotal();
        }

        function kurangiJumlah(button) {
            let input = button.parentElement.querySelector('input');
            let jumlah = parseInt(input.value || 1);

            if (jumlah > 1) {
                input.value = jumlah - 1;
            }

            hitungTotal();
        }

        function tambahBarang() {
            let list = document.getElementById('barang-list');
            let itemPertama = list.querySelector('.barang-item');
            let itemBaru = itemPertama.cloneNode(true);

            itemBaru.querySelector('.alat-select').value = '';
            itemBaru.querySelector('.jumlah-input').value = 1;
            itemBaru.querySelector('.price-info').innerHTML = 'Pilih barang untuk melihat harga, stok, dan subtotal.';
            itemBaru.querySelector('.btn-hapus').style.display = 'inline-flex';

            let fileInput = itemBaru.querySelector('input[type="file"]');
            fileInput.value = '';

            let uploadBox = itemBaru.querySelector('.upload-box');
            let previewImage = itemBaru.querySelector('.preview-image');
            let errorText = itemBaru.querySelector('.field-error');

            uploadBox.classList.remove('has-image', 'upload-error');
            previewImage.src = '';

            if (errorText) {
                errorText.innerHTML = '';
                errorText.classList.remove('show');
            }

            list.appendChild(itemBaru);

            updateNomorBarang();
            hitungTotal();
        }

        function hapusBarang(button) {
            button.closest('.barang-item').remove();
            updateNomorBarang();
            hitungTotal();
        }

        function updateNomorBarang() {
            let items = document.querySelectorAll('.barang-item');

            items.forEach(function(item, index) {
                item.querySelector('.barang-header strong').innerHTML = 'Barang ' + (index + 1);

                let tombolHapus = item.querySelector('.btn-hapus');

                if (items.length === 1) {
                    tombolHapus.style.display = 'none';
                } else {
                    tombolHapus.style.display = 'inline-flex';
                }
            });
        }

        function previewFile(input) {
            if (!input.files || !input.files[0]) {
                resetPreview(input);
                return;
            }

            if (!validateFileInput(input)) {
                return;
            }

            let uploadBox = input.closest('.upload-box');
            let previewImage = uploadBox.querySelector('.preview-image');
            let file = input.files[0];
            let reader = new FileReader();

            reader.onload = function(e) {
                previewImage.src = e.target.result;
                uploadBox.classList.add('has-image');
            };

            reader.readAsDataURL(file);
        }

        function pindahStep(step) {
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.add('hidden');
            document.getElementById('step3').classList.add('hidden');

            document.getElementById('stepNav1').classList.remove('active', 'done');
            document.getElementById('stepNav2').classList.remove('active', 'done');
            document.getElementById('stepNav3').classList.remove('active', 'done');

            if (step === 1) {
                document.getElementById('stepNav1').classList.add('active');
            }

            if (step === 2) {
                document.getElementById('stepNav1').classList.add('done');
                document.getElementById('stepNav2').classList.add('active');
            }

            if (step === 3) {
                document.getElementById('stepNav1').classList.add('done');
                document.getElementById('stepNav2').classList.add('done');
                document.getElementById('stepNav3').classList.add('active');
            }

            document.getElementById('step' + step).classList.remove('hidden');

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function arahkanKeInputError(input, step) {
            pindahStep(step);

            setTimeout(function() {
                input.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                if (input.type !== 'file' && input.reportValidity) {
                    input.reportValidity();
                }
            }, 250);
        }

        function validasiStep(step) {
            let stepElement = document.getElementById('step' + step);
            let inputs = stepElement.querySelectorAll('input, select, textarea');

            for (let i = 0; i < inputs.length; i++) {
                let input = inputs[i];

                if (input.type === 'file') {
                    if (!validateFileInput(input)) {
                        arahkanKeInputError(input, step);
                        return false;
                    }
                } else {
                    if (!input.checkValidity()) {
                        arahkanKeInputError(input, step);
                        return false;
                    }
                }
            }

            return true;
        }

        function lanjutStep(stepTujuan) {
            let stepSekarang = stepTujuan - 1;

            if (validasiStep(stepSekarang)) {
                hitungTotal();
                pindahStep(stepTujuan);
            }
        }

        function submitForm() {
            if (!validasiStep(1)) {
                return;
            }

            if (!validasiStep(2)) {
                return;
            }

            hitungTotal();
            document.getElementById('rentalForm').submit();
        }

        hitungTotal();
        pindahStep(initialStep);
    </script>
</body>
</html>