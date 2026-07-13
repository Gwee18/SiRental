@extends('layouts.app')

@section('title', 'Form Rental')

@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

* {
    font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}

.rental-page {
    min-height: 100vh;
    padding: 140px 24px 80px;
    background: #FCFCFC;
}

.rental-container {
    max-width: 1200px;
    margin: 0 auto;
}

.rental-hero {
    text-align: center;
    margin-bottom: 48px;
}

.rental-eyebrow {
    display: inline-flex;
    align-items: center;
    padding: 10px 18px;
    border-radius: 999px;
    background: #E8F5F0;
    color: #0E5344;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 20px;
    transition: all .3s cubic-bezier(.4, 0, .2, 1);
}

.rental-eyebrow:hover {
    background: #D4ECE5;
    transform: translateY(-1px);
}

.rental-hero h1 {
    color: #0A1F1A;
    font-size: 42px;
    line-height: 1.2;
    letter-spacing: -.02em;
    font-weight: 800;
    margin-bottom: 16px;
}

.rental-hero p {
    color: #5A6B64;
    font-size: 16px;
    line-height: 1.65;
    font-weight: 500;
    max-width: 680px;
    margin: 0 auto;
}

.rental-layout {
    display: grid;
    grid-template-columns: 1fr;
    gap: 0;
    align-items: start;
}

.rental-layout.has-sidebar {
    grid-template-columns: minmax(0, 1fr) 360px;
    gap: 32px;
}

.wizard-card,
.summary-card {
    background: #FFF;
    border: none;
    border-radius: 20px;
    box-shadow: 0 2px 12px rgba(0, 100, 0, .06);
    transition: all .3s cubic-bezier(.4, 0, .2, 1);
}

.wizard-card {
    overflow: hidden;
}

.wizard-top {
    padding: 32px 32px 28px;
    border-bottom: 1px solid #E8EFE6;
    background: #FFF;
    display: none;
}

.stepper-standalone {
    max-width: 600px;
    margin: 0 auto 40px;
    padding: 0;
    background: transparent;
}

.stepper {
    position: relative;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.stepper::before {
    content: "";
    position: absolute;
    left: 16.5%;
    right: 16.5%;
    top: 20px;
    height: 2px;
    background: #E8EFE6;
    z-index: 0;
}

.step {
    position: relative;
    z-index: 1;
    text-align: center;
}

.step-circle {
    width: 42px;
    height: 42px;
    border-radius: 999px;
    background: #F8FBF9;
    border: 3px solid #E8EFE6;
    color: #8A9B94;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-size: 15px;
    font-weight: 800;
    transition: all .3s cubic-bezier(.4, 0, .2, 1);
}

.step.active .step-circle,
.step.done .step-circle {
    background: #006400;
    border-color: #006400;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 100, 0, .25);
}

.step-title {
    color: #8A9B94;
    font-size: 13px;
    font-weight: 700;
    transition: all .3s cubic-bezier(.4, 0, .2, 1);
}

.step.active .step-title,
.step.done .step-title {
    color: #0E5344;
    font-weight: 800;
}

.wizard-body {
    padding: 40px 36px;
}

.wizard-body + .summary-wide {
    margin-top: 30px;
}

.step-content.hidden {
    display: none;
}

.section-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 24px;
    margin-bottom: 32px;
}

.section-heading > div {
    flex: 1;
}

.section-heading h2 {
    color: #0A1F1A;
    font-size: 30px;
    line-height: 1.3;
    letter-spacing: -.02em;
    font-weight: 800;
    margin-bottom: 10px;
}

.section-heading p {
    color: #5A6B64;
    font-size: 15px;
    font-weight: 500;
    line-height: 1.6;
}

.section-icon {
    display: none;
}

.info-box {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    background: #E8F5F0;
    border: 1px solid #C4E8DA;
    color: #0E5344;
    padding: 16px 18px;
    border-radius: 14px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.6;
    margin-bottom: 28px;
}

.info-box svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
    margin-top: 1px;
}

.form-note {
    color: #0E5344;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.6;
    margin: 0 0 28px;
}

.plain-info-text {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #0E5344;
    font-size: 14px;
    font-weight: 700;
    line-height: 1.6;
    margin: 0 0 28px;
}

.plain-info-text svg {
    width: 20px;
    height: 20px;
    flex-shrink: 0;
}

.error-box {
    background: #FFF5F5;
    color: #DC2626;
    border: 1px solid #FCA5A5;
    padding: 18px 20px;
    border-radius: 14px;
    margin-bottom: 28px;
    font-size: 14px;
    font-weight: 600;
    line-height: 1.6;
}

.error-box ul {
    margin-left: 20px;
    margin-top: 10px;
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    color: #0A1F1A;
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 10px;
}

.form-control,
.form-select,
.form-textarea {
    width: 100%;
    border: 1px solid #D4DFD8;
    background: #FFF;
    color: #0A1F1A;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 500;
    font-family: inherit;
    outline: none;
    transition: all .3s cubic-bezier(.4, 0, .2, 1);
}

.form-control,
.form-select {
    height: 54px;
    padding: 0 20px;
}

.form-select {
    padding-right: 48px;
}

.form-select option {
    padding: 12px 16px;
    font-weight: 500;
}

.form-textarea {
    min-height: 140px;
    padding: 16px 20px;
    resize: vertical;
}

.form-control:hover,
.form-select:hover,
.form-textarea:hover {
    border-color: #A0D4BB;
    background: #FDFFFE;
}

.form-control:focus,
.form-select:focus,
.form-textarea:focus {
    border-color: #006400;
    box-shadow: 0 0 0 4px rgba(0, 100, 0, .12);
    background: #FFF;
}

.is-invalid {
    border-color: #DC2626 !important;
    box-shadow: 0 0 0 4px rgba(220, 38, 38, .12) !important;
}

.field-error {
    display: none;
    color: #DC2626;
    font-size: 13px;
    font-weight: 600;
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
    padding-right: 48px;
}

.select-icon {
    position: absolute;
    top: 50%;
    right: 16px;
    transform: translateY(-50%);
    width: 22px;
    height: 22px;
    color: #0A1F1A;
    pointer-events: none;
}

.barang-list {
    display: grid;
    gap: 12px;
}

.barang-item {
    background: #F8FBF9;
    border: none;
    border-radius: 12px;
    padding: 16px 20px;
    display: grid;
    grid-template-columns: 40px 1fr 160px 100px 52px;
    gap: 16px;
    align-items: center;
    transition: none;
}

.barang-number {
    width: auto;
    height: auto;
    border-radius: 0;
    background: transparent;
    color: #0E5344;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 800;
    flex-shrink: 0;
}

.barang-select-wrapper {
    flex: 1;
}

.barang-qty-wrapper,
.barang-upload-wrapper,
.barang-action-wrapper {
    flex-shrink: 0;
}

.barang-header,
.barang-title,
.barang-grid {
    display: none;
}

.qty-box {
    height: 42px;
    display: flex;
    border: 1px solid #D4DFD8;
    border-radius: 10px;
    overflow: hidden;
    background: #FFF;
    transition: all .3s cubic-bezier(.4, 0, .2, 1);
}

.qty-box:focus-within {
    border-color: #006400;
    box-shadow: 0 0 0 3px rgba(0, 100, 0, .12);
}

.qty-box button {
    width: 40px;
    border: none;
    background: #FFF;
    color: #0E5344;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .3s cubic-bezier(.4, 0, .2, 1);
}

.qty-box button svg {
    width: 20px;
    height: 20px;
    stroke-width: 2.5;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.qty-box button:hover {
    background: #E8F5F0;
    color: #006400;
}

.qty-box button:hover svg {
    transform: scale(1.1);
}

.qty-box input {
    border: none;
    box-shadow: none;
    text-align: center;
    font-weight: 700;
    font-size: 14px;
    padding: 0;
    height: 100%;
    color: #0A1F1A;
    width: 100%;
}

.price-info {
    margin-top: 12px;
    color: #5A6B64;
    font-size: 13px;
    font-weight: 500;
    line-height: 1.5;
}

.barang-upload-wrapper {
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.upload-box-compact {
    position: relative;
    width: 60px;
    height: 50px;
    border: 1.5px solid #C4E8DA;
    border-radius: 10px;
    background: #F8FBF9;
    cursor: pointer;
    transition: all .3s cubic-bezier(.4, 0, .2, 1);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.upload-box-compact:hover {
    border-color: #006400;
    background: #E8F5F0;
}

.upload-box-compact.upload-error {
    border-color: #DC2626;
    background: #FFF5F5;
}

.upload-box-compact.has-image {
    border-color: #C4E8DA;
    background: #FFF;
    padding: 0;
    cursor: default;
}

.upload-icon-compact {
    width: 24px;
    height: 24px;
    color: #0E5344;
    transition: all .3s cubic-bezier(.4, 0, .2, 1);
    pointer-events: none;
}

.upload-box-compact:hover .upload-icon-compact {
    color: #006400;
    transform: scale(1.1);
}

.upload-box-compact.has-image .upload-icon-compact {
    display: none;
}

.upload-preview-compact {
    display: none;
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
}

.upload-box-compact.has-image .upload-preview-compact {
    display: block;
}

.upload-box-compact input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
    z-index: 3;
}

.upload-box-compact.has-image input[type="file"] {
    display: none;
}

.upload-refresh-btn {
    display: none;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #E8F5F0;
    border: 1px solid #C4E8DA;
    color: #0E5344;
    cursor: pointer;
    align-items: center;
    justify-content: center;
    transition: all .3s cubic-bezier(.4, 0, .2, 1);
    flex-shrink: 0;
    padding: 0;
}

.upload-box-compact.has-image + .upload-refresh-btn {
    display: flex;
}

.upload-refresh-btn:hover {
    background: #D4ECE5;
    border-color: #006400;
    transform: scale(1.05);
}

.upload-refresh-btn svg {
    width: 16px;
    height: 16px;
}

    .upload-box-large {
        position: relative;
        border: 2px dashed #C4E8DA;
        border-radius: 16px;
        background: #F8FBF9;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        min-height: 200px;
        overflow: hidden;
    }

    .upload-box-large:hover {
        border-color: #006400;
        background: #E8F5F0;
        transform: translateY(-2px);
    }

    .upload-box-large.upload-error {
        border-color: #DC2626;
        background: #FFF5F5;
    }

    .upload-content-large {
        min-height: 200px;
        padding: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
        gap: 12px;
    }

    .upload-icon-large {
        width: 48px;
        height: 48px;
        border-radius: 999px;
        background: #E8F5F0;
        color: #0E5344;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .upload-box-large:hover .upload-icon-large {
        background: #D4ECE5;
        transform: scale(1.1);
    }

    .upload-title-large {
        color: #0A1F1A;
        font-size: 15px;
        font-weight: 700;
    }

    .upload-desc-large {
        color: #5A6B64;
        font-size: 14px;
        font-weight: 500;
    }

    .upload-preview-large {
        display: none;
        width: 100%;
        height: 240px;
        object-fit: cover;
    }

    .upload-box-large.has-image {
        border-style: solid;
        border-color: #C4E8DA;
        background: #FFFFFF;
    }

    .upload-box-large.has-image .upload-content-large {
        display: none;
    }

    .upload-box-large.has-image .upload-preview-large {
        display: block;
    }

    .upload-box-large input[type="file"] {
        position: absolute;
        inset: 0;
        opacity: 0;
        cursor: pointer;
        width: 100%;
        height: 100%;
        z-index: 3;
    }

    .upload-preview-label {
        position: absolute;
        left: 50%;
        bottom: 16px;
        transform: translateX(-50%);
        background: rgba(10, 31, 26, 0.9);
        color: white;
        padding: 10px 16px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        backdrop-filter: blur(8px);
    }

    .two-column {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .data-grid {
        display: block;
    }

    .side-upload-card {
        background: #FFFFFF;
        border: 1px solid #E8EFE6;
        border-radius: 16px;
        padding: 20px;
    }

    .btn {
        border: none;
        border-radius: 12px;
        padding: 16px 24px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        font-family: inherit;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn svg {
        width: 20px;
        height: 20px;
    }

    .btn-primary {
        background: #4A5F75;
        color: white;
        box-shadow: 0 2px 8px rgba(74, 95, 117, 0.25);
    }

    .btn-primary:hover {
        background: #3A4D62;
        box-shadow: 0 4px 20px rgba(74, 95, 117, 0.35);
        transform: translateY(-2px);
    }

    .btn-primary:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(74, 95, 117, 0.25);
    }

    .btn-secondary {
        background: #FFFFFF;
        color: #4A5F75;
        border: 2px solid #D4DFD8;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .btn-secondary:hover {
        background: #F8FBF9;
        border-color: #4A5F75;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .btn-add {
        background: #FFFFFF;
        color: #0E5344;
        border: 2px solid #C4E8DA;
        margin-top: 20px;
        box-shadow: 0 2px 8px rgba(0, 100, 0, 0.08);
    }

    .btn-add:hover {
        background: #E8F5F0;
        border-color: #006400;
        box-shadow: 0 4px 12px rgba(0, 100, 0, 0.15);
        transform: translateY(-2px);
    }

    .btn-danger {
        background: #FFF5F5;
        color: #DC2626;
        border: 1px solid #FCA5A5;
        padding: 10px 16px;
        font-size: 13px;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.12);
    }

    .btn-danger:hover {
        background: #FEE2E2;
        border-color: #DC2626;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.20);
    }

    .btn-submit {
        background: #006400;
        color: white;
        box-shadow: 0 4px 16px rgba(0, 100, 0, 0.30);
    }

    .btn-submit:hover {
        background: #0E5344;
        box-shadow: 0 6px 24px rgba(0, 100, 0, 0.40);
        transform: translateY(-2px);
    }

    .button-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-top: 36px;
    }

    .summary-wide {
        background: #FFFFFF;
        border: none;
        border-radius: 16px;
        padding: 24px 28px;
        margin-top: 40px;
        margin-bottom: 24px;
        box-shadow: 0 2px 12px rgba(0, 100, 0, 0.06);
    }

    .button-row-external {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 40px;
    }

    .summary-wide-title {
        color: #0A1F1A;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -0.01em;
        margin-bottom: 6px;
    }

    .summary-wide-subtitle {
        color: #5A6B64;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.5;
        margin-bottom: 20px;
    }

    .summary-wide-list {
        display: grid;
        gap: 8px;
        margin-bottom: 16px;
    }

    .summary-wide-empty {
        background: #F8FBF9;
        border: 2px dashed #D4DFD8;
        border-radius: 10px;
        padding: 16px;
        color: #5A6B64;
        font-size: 14px;
        font-weight: 500;
        text-align: center;
    }

    .summary-wide-item {
        color: #0A1F1A;
        font-size: 14px;
        font-weight: 600;
        padding: 8px 0;
        border-bottom: 1px solid #F8FBF9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .summary-wide-item:last-child {
        border-bottom: none;
    }

    .summary-wide-item-name {
        color: #0A1F1A;
        font-weight: 700;
    }

    .summary-wide-item-detail {
        color: #5A6B64;
        font-size: 13px;
        font-weight: 500;
        margin-left: 8px;
    }

    .summary-wide-item-price {
        color: #0E5344;
        font-size: 15px;
        font-weight: 800;
    }

    .summary-wide-meta {
        display: flex;
        gap: 24px;
        padding: 16px 0;
        border-top: 2px solid #E8EFE6;
        border-bottom: 2px solid #E8EFE6;
        margin-bottom: 16px;
    }

    .summary-wide-meta-item {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .summary-wide-meta-label {
        color: #5A6B64;
        font-size: 14px;
        font-weight: 600;
    }

    .summary-wide-meta-value {
        color: #0A1F1A;
        font-size: 14px;
        font-weight: 800;
    }

    .summary-wide-total {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 22px;
        border-radius: 12px;
        background: #E8F5F0;
        border: 2px solid #C4E8DA;
    }

    .summary-wide-total-label {
        color: #0E5344;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
    }

    .summary-wide-total-value {
        color: #0A1F1A;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .summary-wide-buttons {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-top: 24px;
        padding-top: 24px;
        border-top: 2px solid #E8EFE6;
    }

    .summary-card {
        position: sticky;
        top: 120px;
        padding: 28px;
    }

    .summary-title {
        color: #0A1F1A;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: -0.01em;
        margin-bottom: 8px;
    }

    .summary-subtitle {
        color: #5A6B64;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .summary-list {
        display: grid;
        gap: 12px;
        margin-bottom: 24px;
    }

    .summary-empty {
        background: #F8FBF9;
        border: 2px dashed #D4DFD8;
        border-radius: 14px;
        padding: 16px;
        color: #5A6B64;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.6;
    }

    .summary-item {
        background: #F8FBF9;
        border: 1px solid #E8EFE6;
        border-radius: 14px;
        padding: 16px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .summary-item:hover {
        background: #FFFFFF;
        border-color: #C4E8DA;
    }

    .summary-item strong {
        display: block;
        color: #0A1F1A;
        font-size: 14px;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .summary-item span {
        display: block;
        color: #5A6B64;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.5;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 0;
        border-top: 1px solid #E8EFE6;
        color: #5A6B64;
        font-size: 14px;
        font-weight: 500;
    }

    .summary-row strong {
        color: #0A1F1A;
        font-weight: 800;
    }

    .summary-total {
        margin-top: 20px;
        padding: 24px;
        border-radius: 16px;
        background: #E8F5F0;
        border: 2px solid #C4E8DA;
    }

    .summary-total small {
        display: block;
        color: #0E5344;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .summary-total strong {
        display: block;
        color: #0A1F1A;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .payment-grid {
        display: grid;
        grid-template-columns: 1.35fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .payment-card {
        background: #FFFFFF;
        border: 1px solid #E8EFE6;
        border-radius: 16px;
        padding: 26px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .payment-card:hover {
        border-color: #C4E8DA;
        box-shadow: 0 4px 16px rgba(0, 100, 0, 0.08);
    }

    /* NEW UNIFIED PAYMENT CARD */
    .payment-unified-card {
        background: #FFFFFF;
        border: 1px solid #E8EFE6;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 32px;
    }

    .payment-unified-section {
        padding: 28px;
        border-bottom: 2px solid #F8FBF9;
    }

    .payment-unified-section-last {
        border-bottom: none;
    }

    .payment-unified-label {
        color: #0A1F1A;
        font-size: 18px;
        font-weight: 800;
        letter-spacing: -0.01em;
        margin-bottom: 8px;
    }

    .payment-unified-desc {
        color: #5A6B64;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .payment-unified-amount {
        color: #0A1F1A;
        font-size: 44px;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1;
    }

    .payment-method-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 24px;
    }

    .payment-method-badge {
        display: inline-flex;
        align-items: center;
        padding: 12px 20px;
        border-radius: 12px;
        background: #F8FBF9;
        border: 2px solid #E8EFE6;
        color: #0A1F1A;
        font-size: 15px;
        font-weight: 700;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .payment-flow-list {
        display: grid;
        gap: 14px;
        margin-top: 20px;
    }

    .payment-flow-item {
        display: grid;
        grid-template-columns: 28px 1fr;
        gap: 14px;
        align-items: start;
    }

    .payment-flow-badge {
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        flex-shrink: 0;
        color: #0A1F1A;
        margin-top: 2px;
    }

    .payment-flow-title {
        color: #0A1F1A;
        font-size: 15px;
        font-weight: 800;
        margin-bottom: 6px;
        letter-spacing: -0.01em;
    }

    .payment-flow-desc {
        color: #5A6B64;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.6;
    }

    /* BUTTON OUTLINE STYLE */
    .btn-outline {
        background: #FFFFFF;
        border: 2px solid #D4DFD8;
        color: #4A5F75;
    }

    .btn-outline:hover {
        background: #F8FBF9;
        border-color: #C4E8DA;
        color: #0E5344;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(74, 95, 117, 0.15);
    }

    .button-row-step3 {
        margin-top: 0;
    }

    .payment-label {
        color: #8A9B94;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 10px;
    }

    .payment-total-value {
        color: #0A1F1A;
        font-size: 36px;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .payment-title {
        color: #0A1F1A;
        font-size: 19px;
        font-weight: 800;
        margin-bottom: 10px;
    }

    .payment-desc {
        color: #5A6B64;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.6;
    }

    .flow-list {
        display: grid;
        gap: 16px;
        margin-top: 20px;
    }

    .flow-item {
        display: grid;
        grid-template-columns: 36px 1fr;
        gap: 14px;
    }

    .flow-number {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #E8F5F0;
        color: #0E5344;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 15px;
    }

    .flow-item strong {
        color: #0A1F1A;
        font-size: 15px;
        font-weight: 800;
    }

    .flow-item p {
        color: #5A6B64;
        font-size: 14px;
        font-weight: 500;
        line-height: 1.6;
        margin-top: 4px;
    }

/* Tombol hapus ikon */
.btn-delete-icon {
    width: 44px;
    height: 44px;
    border: 1.5px solid #F3CACA;
    border-radius: 10px;
    background: #FFFFFF;
    color: #DC2626;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .25s cubic-bezier(.4, 0, .2, 1);
    padding: 0;
    flex-shrink: 0;
}

.btn-delete-icon:hover {
    background: #FFF5F5;
    border-color: #F0A8A8;
    color: #B91C1C;
    transform: translateY(-1px);
}

.btn-delete-icon svg {
    width: 18px;
    height: 18px;
    stroke-linecap: round;
    stroke-linejoin: round;
}

@media (max-width: 1024px) {
    .rental-layout,
    .rental-layout.has-sidebar {
        grid-template-columns: 1fr;
    }

    .summary-card {
        position: static;
    }

    .data-grid {
        grid-template-columns: 1fr;
    }

    .summary-wide-meta {
        grid-template-columns: 1fr;
    }
}

/* =========================
   MOBILE RESPONSIVE CLEAN
========================= */
@media (max-width: 768px) {
    .rental-page {
        padding: 104px 14px 48px;
        background: #F8FAF9;
    }

    .rental-container {
        max-width: 100%;
    }

    .rental-hero {
        margin-bottom: 28px;
    }

    .rental-eyebrow {
        display: none;
    }

    .rental-hero h1 {
        font-size: 28px;
        line-height: 1.2;
        margin-bottom: 10px;
    }

    .rental-hero p {
        font-size: 14px;
        line-height: 1.6;
        max-width: 100%;
    }

    .stepper-standalone {
        max-width: 100%;
        margin-bottom: 24px;
    }

    .stepper {
        gap: 8px;
    }

    .stepper::before {
        left: 18%;
        right: 18%;
        top: 18px;
    }

    .step-circle {
        width: 36px;
        height: 36px;
        font-size: 13px;
        border-width: 2px;
        margin-bottom: 8px;
    }

    .step-title {
        font-size: 11px;
        line-height: 1.25;
    }

    .wizard-card,
    .summary-wide,
    .summary-card {
        border-radius: 18px;
        box-shadow: 0 2px 10px rgba(0, 100, 0, .05);
    }

    .wizard-body,
    .wizard-top {
        padding: 22px 16px;
    }

    .section-heading {
        flex-direction: column;
        gap: 12px;
        margin-bottom: 22px;
    }

    .section-heading h2 {
        font-size: 24px;
        line-height: 1.25;
        margin-bottom: 8px;
    }

    .section-heading p {
        font-size: 14px;
    }

    .info-box,
    .error-box {
        padding: 14px;
        border-radius: 14px;
        font-size: 13px;
        margin-bottom: 22px;
    }

    .plain-info-text {
        align-items: flex-start;
        gap: 9px;
        font-size: 13px;
        line-height: 1.6;
        margin: 0 0 24px;
    }

    .plain-info-text svg {
        width: 18px;
        height: 18px;
        margin-top: 1px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        font-size: 13px;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        height: 50px;
        padding: 0 16px;
        font-size: 14px;
        border-radius: 12px;
    }

    .form-textarea {
        min-height: 120px;
        padding: 14px 16px;
        font-size: 14px;
        border-radius: 12px;
    }

    .two-column,
    .payment-grid {
        grid-template-columns: 1fr;
        gap: 0;
    }

    /* Card barang mobile: ringkas, semua aksi sejajar */
    .barang-list {
        gap: 10px;
    }

    .barang-item {
        display: grid;
        grid-template-columns: 18px minmax(0, 1fr) auto auto;
        grid-template-areas:
            "number select select select"
            ". qty upload action";
        gap: 9px 8px;
        align-items: center;
        padding: 12px;
        border-radius: 14px;
        background: #FFFFFF;
        border: 1px solid #E8EFE6;
        box-shadow: 0 2px 8px rgba(0, 100, 0, .04);
    }

    .barang-number {
        grid-area: number;
        width: auto;
        height: auto;
        border-radius: 0;
        background: transparent;
        color: #0E5344;
        font-size: 13px;
        font-weight: 800;
        justify-content: flex-start;
        align-self: center;
    }

    .barang-select-wrapper {
        grid-area: select;
        width: 100%;
        min-width: 0;
    }

    .barang-select-wrapper .form-select {
        height: 44px;
        padding-left: 14px;
        padding-right: 40px;
        font-size: 12.5px;
        border-radius: 12px;
    }

    .barang-qty-wrapper {
        grid-area: qty;
        width: 100%;
        min-width: 0;
    }

    .qty-box {
        width: 100%;
        height: 42px;
        border-radius: 11px;
    }

    .qty-box button {
        width: 36px;
        height: 42px;
    }

    .qty-box input {
        height: 42px;
        font-size: 13.5px;
    }

    .barang-upload-wrapper {
        grid-area: upload;
        width: auto;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        flex-wrap: nowrap;
    }

    .upload-box-compact {
        width: 42px;
        height: 42px;
        border-radius: 11px;
    }

    .upload-icon-compact {
        width: 20px;
        height: 20px;
    }

    .upload-refresh-btn {
        width: 42px;
        height: 42px;
        border-radius: 11px;
        padding: 0;
    }

    .upload-refresh-btn svg {
        width: 17px;
        height: 17px;
    }

    .barang-action-wrapper {
        grid-area: action;
        width: auto;
        display: flex;
        align-items: center;
        justify-content: center;
        justify-self: end;
    }

    .btn-delete-icon,
    .barang-action-wrapper .btn-hapus {
        width: 42px !important;
        height: 42px !important;
        min-height: 42px !important;
        padding: 0 !important;
        border-radius: 11px !important;
    }

    .btn-delete-icon svg,
    .barang-action-wrapper .btn-hapus svg {
        width: 17px;
        height: 17px;
    }

    .price-info {
        margin-top: 8px;
        font-size: 12px;
        line-height: 1.4;
    }

    .btn-add {
        width: 100%;
        margin-top: 16px;
    }

    /* Upload KTP mobile */
    .upload-box-large {
        min-height: 170px;
        border-radius: 16px;
    }

    .upload-content-large {
        min-height: 170px;
        padding: 24px 18px;
    }

    .upload-icon-large {
        width: 44px;
        height: 44px;
    }

    .upload-title-large {
        font-size: 14px;
    }

    .upload-desc-large {
        font-size: 13px;
    }

    .upload-preview-large {
        height: 220px;
    }

    .upload-preview-label {
        font-size: 12px;
        padding: 8px 12px;
        bottom: 12px;
    }

    /* Ringkasan step 1 */
    .summary-wide {
        padding: 20px 16px;
        margin-top: 22px;
        margin-bottom: 20px;
    }

    .summary-wide-title {
        font-size: 17px;
    }

    .summary-wide-subtitle {
        font-size: 13px;
        margin-bottom: 16px;
    }

    .summary-wide-item {
        display: block;
        padding: 12px 0;
    }

    .summary-wide-item-detail {
        display: block;
        margin-left: 0;
        margin-top: 4px;
    }

    .summary-wide-item-price {
        display: block;
        margin-top: 8px;
        font-size: 15px;
    }

    .summary-wide-meta {
        flex-direction: column;
        gap: 10px;
        padding: 14px 0;
    }

    .summary-wide-total {
        display: block;
        padding: 16px;
    }

    .summary-wide-total-value {
        font-size: 26px;
        margin-top: 6px;
    }

    .summary-wide-buttons {
        flex-direction: column;
        gap: 12px;
        margin-top: 18px;
        padding-top: 18px;
    }

    .summary-wide-buttons .btn {
        width: 100%;
    }

    .button-row,
    .button-row-step3 {
        flex-direction: column;
        gap: 12px;
        margin-top: 28px;
    }

    .button-row .btn,
    .button-row-step3 .btn {
        width: 100%;
    }

    .btn {
        min-height: 50px;
        padding: 14px 18px;
        font-size: 14px;
        border-radius: 12px;
    }

    /* Step 3 pembayaran */
    .payment-unified-card {
        border-radius: 18px;
        margin-bottom: 24px;
    }

    .payment-unified-section {
        padding: 20px 16px;
    }

    .payment-unified-label {
        font-size: 16px;
    }

    .payment-unified-desc {
        font-size: 13px;
    }

    .payment-unified-amount {
        font-size: 34px;
    }

    .payment-method-header {
        flex-direction: column;
        gap: 14px;
    }

    .payment-method-badge {
        width: 100%;
        justify-content: center;
        padding: 12px 16px;
        font-size: 14px;
    }

    .payment-flow-list {
        gap: 16px;
    }

    .payment-flow-item {
        grid-template-columns: 26px 1fr;
        gap: 12px;
    }

    .payment-flow-badge {
        width: 26px;
        height: 26px;
        font-size: 14px;
    }

    .payment-flow-title {
        font-size: 14px;
    }

    .payment-flow-desc {
        font-size: 13px;
    }

    .rental-layout.has-sidebar {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .summary-card {
        position: static;
        padding: 20px 16px;
    }

    .summary-total {
        padding: 18px;
    }

    .summary-total strong {
        font-size: 28px;
    }

    .button-row-step3 {
        display: none !important;
    }

    .summary-mobile-actions {
        display: flex !important;
        flex-direction: column;
        gap: 12px;
        margin-top: 22px;
        padding-top: 22px;
        border-top: 1px solid #E8EFE6;
    }

    .summary-mobile-actions .btn {
        width: 100%;
    }
}

@media (max-width: 420px) {
    .rental-page {
        padding-left: 12px;
        padding-right: 12px;
    }

    .rental-hero h1 {
        font-size: 26px;
    }

    .rental-hero p {
        font-size: 13px;
    }

    .wizard-body {
        padding: 20px 14px;
    }

    .section-heading h2 {
        font-size: 22px;
    }

    .step-title {
        font-size: 10px;
    }

    .barang-item {
        grid-template-columns: 18px minmax(0, 1fr) auto auto;
        gap: 8px 6px;
        padding: 10px;
    }

    .barang-select-wrapper .form-select {
        height: 42px;
        font-size: 12px;
    }

    .qty-box,
    .qty-box input {
        height: 40px;
    }

    .qty-box button {
        width: 33px;
        height: 40px;
    }

    .upload-box-compact,
    .upload-refresh-btn,
    .btn-delete-icon,
    .barang-action-wrapper .btn-hapus {
        width: 40px !important;
        height: 40px !important;
        min-height: 40px !important;
        border-radius: 10px !important;
    }

    .summary-wide-total-value,
    .summary-total strong {
        font-size: 24px;
    }

    .payment-unified-amount {
        font-size: 30px;
    }
}

/* Tombol tambahan khusus mobile di ringkasan step 3 */
.summary-mobile-actions {
    display: none !important;
}

@media (min-width: 769px) {
    .summary-mobile-actions {
        display: none !important;
    }
}

.summary-mobile-actions {
    display: none !important;
}

@media (max-width: 768px) {
    .button-row-step3 {
        display: none !important;
    }

    .summary-mobile-actions {
        display: flex !important;
        flex-direction: column;
        gap: 12px;
        margin-top: 22px;
        padding-top: 22px;
        border-top: 1px solid #E8EFE6;
    }

    .summary-mobile-actions .btn {
        width: 100%;
    }
}

@media (min-width: 769px) {
    .summary-mobile-actions {
        display: none !important;
    }
}

/* Upload native perangkat dan notifikasi file */
.customer-upload-input {
    display: none !important;
}

.upload-toast {
    position: fixed;
    top: 100px;
    right: 24px;
    z-index: 11000;
    width: min(390px, calc(100% - 32px));
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 15px 16px;
    border: 1px solid #FCA5A5;
    border-radius: 14px;
    background: #FFF5F5;
    color: #B91C1C;
    box-shadow: 0 14px 38px rgba(127, 29, 29, .16);
    transform: translateY(-16px);
    opacity: 0;
    pointer-events: none;
    transition: .25s ease;
}

.upload-toast.show {
    transform: translateY(0);
    opacity: 1;
    pointer-events: auto;
}

.upload-toast svg {
    width: 21px;
    height: 21px;
    flex-shrink: 0;
    margin-top: 1px;
}

.upload-toast strong {
    display: block;
    font-size: 14px;
    margin-bottom: 3px;
}

.upload-toast span {
    display: block;
    font-size: 13px;
    line-height: 1.45;
}

.upload-inline-notice {
    display: none;
    align-items: flex-start;
    gap: 10px;
    margin: 12px 0 18px;
    padding: 12px 14px;
    border: 1px solid #FCA5A5;
    border-radius: 12px;
    background: #FFF5F5;
    color: #B91C1C;
    font-size: 13px;
    font-weight: 600;
    line-height: 1.5;
}

.upload-inline-notice.show {
    display: flex;
}

.upload-inline-notice svg {
    width: 19px;
    height: 19px;
    flex-shrink: 0;
    margin-top: 1px;
}

.barang-upload-wrapper .field-error {
    display: none !important;
}

@media (max-width: 768px) {
    .upload-toast {
        top: 88px;
        left: 16px;
        right: 16px;
        width: auto;
    }

    .upload-inline-notice {
        margin-top: 10px;
        font-size: 12px;
    }
}

</style>

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
            <div class="error-box">
                <strong>Terjadi kesalahan:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <p>Silakan perbaiki bagian yang ditandai. File foto perlu dipilih ulang jika sebelumnya gagal tersimpan.</p>
            </div>
        @endif

        <form action="{{ route('rental.store') }}" method="POST" enctype="multipart/form-data" id="rentalForm" novalidate>
            @csrf

            {{-- STEPPER STANDALONE --}}
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
                    <div class="wizard-top">
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

                    <div class="wizard-body">

                        {{-- STEP 1 --}}
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
                                        {{-- Number --}}
                                        <div class="barang-number">{{ $index + 1 }}</div>

                                        {{-- Select Barang --}}
                                        <div class="barang-select-wrapper">
                                            <div class="select-wrapper">
                                                <select name="alat_id[]" class="form-select alat-select @error('alat_id.' . $index) is-invalid @enderror" onchange="hitungTotal()" required>
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
                                                <p class="field-error show" style="margin-top: 4px;">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        {{-- Quantity --}}
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

                                        {{-- Upload Foto Compact --}}
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

                                        {{-- Action --}}
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

                        {{-- STEP 2 --}}
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
                                                value="{{ old('nama_lengkap', Auth::guard('web')->user()->nama_lengkap ?? '') }}"
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
                                                value="{{ old('no_telp', Auth::guard('web')->user()->no_telp ?? '') }}"
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
                                        >{{ old('alamat', Auth::guard('web')->user()->alamat ?? '') }}</textarea>

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

                        {{-- STEP 3 --}}
                        <div class="step-content {{ $formStep == 3 ? '' : 'hidden' }}" id="step3">
                            <div class="section-heading">
                                <div>
                                    <h2>Konfirmasi Pembayaran</h2>
                                    <p>Periksa kembali data rental Anda. Pembayaran dilakukan secara tunai di kasir saat pengambilan barang.</p>
                                </div>
                            </div>

                            {{-- UNIFIED PAYMENT CARD --}}
                            <div class="payment-unified-card">
                                {{-- Total Section --}}
                                <div class="payment-unified-section">
                                    <div class="payment-unified-label">Total Pembayaran</div>
                                    <div class="payment-unified-desc">Total dihitung dari harga sewa, jumlah barang, dan lama sewa.</div>
                                    <div class="payment-unified-amount" id="total-harga-final">Rp 0</div>
                                </div>

                                {{-- Method Section --}}
                                <div class="payment-unified-section">
                                    <div class="payment-method-header">
                                        <div>
                                            <div class="payment-unified-label">Metode Pembayaran</div>
                                            <div class="payment-unified-desc">Tidak perlu transfer. Bayar langsung saat pengambilan barang.</div>
                                        </div>
                                        <div class="payment-method-badge">Cash di Kasir</div>
                                    </div>
                                </div>

                                {{-- Flow Section --}}
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

                            {{-- BUTTON ROW --}}
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

                {{-- WIDE SUMMARY --}}
<div class="summary-wide" id="summaryWide" style="{{ $formStep == 1 ? 'display: block;' : 'display: none;' }}">
    <div class="summary-wide-title">Ringkasan Rental</div>
    <div class="summary-wide-subtitle">Ringkasan otomatis berubah sesuai pilihan Anda</div>

    <div id="summary-wide-list" class="summary-wide-list">
        <div class="summary-wide-empty">Belum ada barang yang dipilih</div>
    </div>

    <div class="summary-wide-meta">
        <div class="summary-wide-meta-item">
            <span class="summary-wide-meta-label">Lama sewa:</span>
            <strong class="summary-wide-meta-value" id="summary-wide-duration">-</strong>
        </div>

        <div class="summary-wide-meta-item">
            <span class="summary-wide-meta-label">Metode:</span>
            <strong class="summary-wide-meta-value">Cash</strong>
        </div>
    </div>

    <div class="summary-wide-total">
        <div class="summary-wide-total-label">Total</div>
        <div class="summary-wide-total-value" id="total-harga-wide">Rp 0</div>
    </div>

    {{-- Tombol khusus mobile untuk Step 3 --}}
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
</div>

                    {{-- BUTTONS INSIDE SUMMARY --}}
                    <div class="summary-wide-buttons">
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

                {{-- SUMMARY (only for step 2 & 3) --}}
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
<script>
    const initialStep = {{ (int) $formStep }};
    const maxFileSize = 2 * 1024 * 1024;
    const allowedExtensions = ['jpg', 'jpeg', 'jfif', 'png', 'webp'];
    const allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

    let uploadToastTimer = null;

    function isMobileDevice() {
        if (
            navigator.userAgentData &&
            typeof navigator.userAgentData.mobile === 'boolean'
        ) {
            return navigator.userAgentData.mobile;
        }

        const mobileUserAgent =
            /Android|iPhone|iPad|iPod|IEMobile|Opera Mini/i.test(
                navigator.userAgent
            );

        const coarsePointer =
            window.matchMedia &&
            window.matchMedia('(pointer: coarse)').matches;

        return mobileUserAgent || (
            coarsePointer &&
            window.innerWidth <= 1024
        );
    }

    function triggerNativeUpload(input) {
        if (!input) {
            return;
        }

        /*
         * Tanpa atribut capture, browser HP akan menampilkan pemilih
         * bawaan sistem yang biasanya berisi Kamera, Galeri, dan File.
         * Pada laptop/PC, browser langsung membuka File Explorer.
         */
        input.removeAttribute('capture');

        if (isMobileDevice()) {
            input.setAttribute(
                'accept',
                'image/jpeg,image/png,image/webp'
            );
        } else {
            input.setAttribute(
                'accept',
                '.jpg,.jpeg,.jfif,.png,.webp,image/jpeg,image/png,image/webp'
            );
        }

        input.click();
    }

    function handleUploadBoxKey(event, uploadBox) {
        if (event.key !== 'Enter' && event.key !== ' ') {
            return;
        }

        event.preventDefault();

        triggerNativeUpload(
            uploadBox.querySelector('.customer-upload-input')
        );
    }

    function showUploadToast(message) {
        let toast = document.getElementById('uploadToast');
        let text = document.getElementById('uploadToastMessage');

        text.textContent = message;
        toast.classList.add('show');

        clearTimeout(uploadToastTimer);

        uploadToastTimer = setTimeout(function() {
            toast.classList.remove('show');
        }, 4500);
    }

    function showInlineUploadNotice(message) {
        let notice = document.getElementById('uploadInlineNotice');
        let text = document.getElementById(
            'uploadInlineNoticeMessage'
        );

        if (!notice || !text) {
            return;
        }

        text.textContent = message;
        notice.classList.add('show');
    }

    function clearInlineUploadNotice() {
        let notice = document.getElementById('uploadInlineNotice');
        let text = document.getElementById(
            'uploadInlineNoticeMessage'
        );

        if (notice) {
            notice.classList.remove('show');
        }

        if (text) {
            text.textContent = '';
        }
    }

    function formatRupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    function getFileExtension(filename) {
        return filename.split('.').pop().toLowerCase();
    }

    function showUploadError(input, message) {
        let uploadBox = input.closest('.upload-box-compact, .upload-box-large');
        let formGroup = input.closest('.form-group, .barang-upload-wrapper');
        let errorText = formGroup ? formGroup.querySelector('.field-error') : null;

        if (uploadBox) {
            uploadBox.classList.add('upload-error');
        }

        if (errorText) {
            errorText.innerHTML = message;
            errorText.classList.add('show');
        }

        showUploadToast(message);

        if (input.name === 'foto_barang[]') {
            showInlineUploadNotice(message);
        }
    }

    function clearUploadError(input) {
        let uploadBox = input.closest('.upload-box-compact, .upload-box-large');
        let formGroup = input.closest('.form-group, .barang-upload-wrapper');
        let errorText = formGroup ? formGroup.querySelector('.field-error') : null;

        if (uploadBox) {
            uploadBox.classList.remove('upload-error');
        }

        if (errorText) {
            errorText.innerHTML = '';
            errorText.classList.remove('show');
        }

        if (input.name === 'foto_barang[]') {
            clearInlineUploadNotice();
        }
    }

    function resetPreview(input) {
        let uploadBox = input.closest('.upload-box-compact, .upload-box-large');
        if (!uploadBox) return;
        
        let previewImage = uploadBox.querySelector('.upload-preview-compact, .upload-preview-large');
        if (previewImage) {
            previewImage.src = '';
        }
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

        if (!mimeTypeValid || !extensionValid) {
            input.value = '';
            resetPreview(input);
            showUploadError(input, 'Format file tidak didukung. Gunakan JPG, JPEG, JFIF, PNG, atau WEBP.');
            return false;
        }

        if (file.size > maxFileSize) {
            input.value = '';
            resetPreview(input);
            showUploadError(input, 'Ukuran file terlalu besar. Maksimal 2MB per foto.');
            return false;
        }

        return true;
    }

    function updateRingkasan(total) {
        let summaryList = document.getElementById('summary-list');
        let summaryDuration = document.getElementById('summary-duration');
        let summaryWideList = document.getElementById('summary-wide-list');
        let summaryWideDuration = document.getElementById('summary-wide-duration');
        let lamaSewa = parseInt(document.getElementById('lama_sewa').value) || 0;
        let items = document.querySelectorAll('.barang-item');
        let html = '';
        let htmlWide = '';

        items.forEach(function(item) {
            let select = item.querySelector('.alat-select');
            let jumlahInput = item.querySelector('.jumlah-input');
            let option = select.options[select.selectedIndex];

            if (!option || !option.value) {
                return;
            }

            let nama = option.getAttribute('data-nama') || option.textContent.trim();
            let harga = parseInt(option.getAttribute('data-harga')) || 0;
            let jumlah = parseInt(jumlahInput.value) || 1;
            let subtotal = harga * jumlah * lamaSewa;

            // For sidebar (old summary - for step 2)
            html += `
                <div class="summary-item">
                    <strong>${nama}</strong>
                    <span>${jumlah} barang × ${lamaSewa || 0} hari</span>
                    <span>${formatRupiah(subtotal)}</span>
                </div>
            `;

            // For wide table (new level 2 summary in step 1)
            htmlWide += `
                <div class="summary-wide-item">
                    <div>
                        <span class="summary-wide-item-name">${nama}</span>
                        <span class="summary-wide-item-detail">- ${jumlah} × ${lamaSewa || 0}hari × ${formatRupiah(harga)}/hari</span>
                    </div>
                    <span class="summary-wide-item-price">${formatRupiah(subtotal)}</span>
                </div>
            `;
        });

        if (!html) {
            html = '<div class="summary-empty">Belum ada barang yang dipilih.</div>';
        }

        if (!htmlWide) {
            htmlWide = '<div class="summary-wide-empty">Belum ada barang yang dipilih</div>';
        }

        if (summaryList) {
            summaryList.innerHTML = html;
        }

        if (summaryDuration) {
            summaryDuration.innerHTML = lamaSewa > 0 ? lamaSewa + ' hari' : '-';
        }

        if (summaryWideList) {
            summaryWideList.innerHTML = htmlWide;
        }

        if (summaryWideDuration) {
            summaryWideDuration.innerHTML = lamaSewa > 0 ? lamaSewa + ' hari' : '-';
        }
    }

    function hitungTotal() {
        let total = 0;
        let lamaSewa = parseInt(document.getElementById('lama_sewa').value) || 0;
        let items = document.querySelectorAll('.barang-item');

        items.forEach(function(item) {
            let select = item.querySelector('.alat-select');
            let jumlahInput = item.querySelector('.jumlah-input');

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
            }
        });

        let totalHargaElement = document.getElementById('total-harga');
        let totalHargaFinalElement = document.getElementById('total-harga-final');
        let totalHargaWideElement = document.getElementById('total-harga-wide');

        if (totalHargaElement) {
            totalHargaElement.innerHTML = formatRupiah(total);
        }
        
        if (totalHargaFinalElement) {
            totalHargaFinalElement.innerHTML = formatRupiah(total);
        }
        
        if (totalHargaWideElement) {
            totalHargaWideElement.innerHTML = formatRupiah(total);
        }

        updateRingkasan(total);
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

        // Reset select
        itemBaru.querySelector('.alat-select').value = '';
        
        // Reset jumlah
        itemBaru.querySelector('.jumlah-input').value = 1;
        
        // Reset upload
        let fileInput = itemBaru.querySelector('input[type="file"]');
        fileInput.value = '';

        let uploadBox = itemBaru.querySelector('.upload-box-compact');
        let previewImage = itemBaru.querySelector('.upload-preview-compact');
        
        if (uploadBox) {
            uploadBox.classList.remove('has-image', 'upload-error');
        }
        
        if (previewImage) {
            previewImage.src = '';
        }

        // Reset error
        let errorTexts = itemBaru.querySelectorAll('.field-error');
        errorTexts.forEach(function(errorText) {
            errorText.innerHTML = '';
            errorText.classList.remove('show');
        });

        // Show hapus button
        let btnHapus = itemBaru.querySelector('.btn-hapus');
        if (btnHapus) {
            btnHapus.style.display = 'inline-flex';
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
            let numberBadge = item.querySelector('.barang-number');
            if (numberBadge) {
                numberBadge.innerHTML = index + 1;
            }

            let tombolHapus = item.querySelector('.btn-hapus');
            if (tombolHapus) {
                if (items.length === 1) {
                    tombolHapus.style.display = 'none';
                } else {
                    tombolHapus.style.display = 'inline-flex';
                }
            }
        });
    }

    function previewFileCompact(input) {
        if (!input.files || !input.files[0]) {
            return;
        }

        if (!validateFileInput(input)) {
            return;
        }

        let uploadBox = input.closest('.upload-box-compact');
        let previewImage = uploadBox.querySelector('.upload-preview-compact');
        let file = input.files[0];
        let reader = new FileReader();

        reader.onload = function(e) {
            previewImage.src = e.target.result;
            uploadBox.classList.add('has-image');
        };

        reader.readAsDataURL(file);
    }

    function previewFileLarge(input) {
        if (!input.files || !input.files[0]) {
            return;
        }

        if (!validateFileInput(input)) {
            return;
        }

        let uploadBox = input.closest('.upload-box-large');
        let previewImage = uploadBox.querySelector('.upload-preview-large');
        let file = input.files[0];
        let reader = new FileReader();

        reader.onload = function(e) {
            previewImage.src = e.target.result;
            uploadBox.classList.add('has-image');
        };

        reader.readAsDataURL(file);
    }

    function previewFile(input) {
        previewFileLarge(input);
    }

    function pindahStep(step) {
        document.getElementById('step1').classList.add('hidden');
        document.getElementById('step2').classList.add('hidden');
        document.getElementById('step3').classList.add('hidden');

        document.getElementById('stepNav1').classList.remove('active', 'done');
        document.getElementById('stepNav2').classList.remove('active', 'done');
        document.getElementById('stepNav3').classList.remove('active', 'done');

        // Update layout and summary visibility
        let rentalLayout = document.getElementById('rentalLayout');
        let summaryCard = document.getElementById('summaryCard');
        let summaryWide = document.getElementById('summaryWide');

        if (step === 1) {
            document.getElementById('stepNav1').classList.add('active');
            rentalLayout.classList.remove('has-sidebar');
            if (summaryCard) summaryCard.style.display = 'none';
            if (summaryWide) summaryWide.style.display = 'block';
        }

        if (step === 2) {
            document.getElementById('stepNav1').classList.add('done');
            document.getElementById('stepNav2').classList.add('active');
            rentalLayout.classList.remove('has-sidebar');
            if (summaryCard) summaryCard.style.display = 'none';
            if (summaryWide) summaryWide.style.display = 'none';
        }

        if (step === 3) {
            document.getElementById('stepNav1').classList.add('done');
            document.getElementById('stepNav2').classList.add('done');
            document.getElementById('stepNav3').classList.add('active');
            rentalLayout.classList.add('has-sidebar');
            if (summaryCard) summaryCard.style.display = 'block';
            if (summaryWide) summaryWide.style.display = 'none';
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

    // Function to trigger file input when refresh button is clicked
    function triggerFileInput(button) {
        let wrapper = button.closest('.barang-upload-wrapper');
        let fileInput = wrapper.querySelector('.customer-upload-input');

        if (fileInput) {
            triggerNativeUpload(fileInput);
        }
    }

    // Function to preview image in modal when clicked
    function previewImageModal(img) {
        if (!img.src || img.src === '') {
            return;
        }

        // Create modal overlay
        let modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            inset: 0;
            background: rgba(10, 31, 26, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            cursor: zoom-out;
            padding: 40px;
        `;

        // Create image element
        let modalImg = document.createElement('img');
        modalImg.src = img.src;
        modalImg.style.cssText = `
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        `;

        // Create close button
        let closeBtn = document.createElement('button');
        closeBtn.innerHTML = `
            <svg fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" style="width: 24px; height: 24px;">
                <path d="M18 6L6 18M6 6l12 12"/>
            </svg>
        `;
        closeBtn.style.cssText = `
            position: absolute;
            top: 20px;
            right: 20px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: white;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            color: #0A1F1A;
        `;

        // Close on click
        modal.onclick = function() {
            modal.remove();
        };

        closeBtn.onclick = function(e) {
            e.stopPropagation();
            modal.remove();
        };

        // Close on ESC key
        document.addEventListener('keydown', function escHandler(e) {
            if (e.key === 'Escape') {
                modal.remove();
                document.removeEventListener('keydown', escHandler);
            }
        });

        modal.appendChild(modalImg);
        modal.appendChild(closeBtn);
        document.body.appendChild(modal);
    }

</script>
@endpush