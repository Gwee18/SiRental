const form = document.getElementById('rentalForm');

if (form) {
    const maxFileSize = 2 * 1024 * 1024;
    const allowedExtensions = ['jpg', 'jpeg', 'jfif', 'png', 'webp'];
    const allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
    const duplicateMessage = 'Barang yang sama tidak boleh dipilih lebih dari satu kali.';
    let uploadToastTimer;
    let submitting = false;

    const byId = id => document.getElementById(id);
    const all = (selector, parent = document) => [...parent.querySelectorAll(selector)];
    const rupiah = value => `Rp ${Number(value || 0).toLocaleString('id-ID')}`;
    const escapeHtml = value => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    function isMobileDevice() {
        if (navigator.userAgentData && typeof navigator.userAgentData.mobile === 'boolean') {
            return navigator.userAgentData.mobile;
        }

        return /Android|iPhone|iPad|iPod|IEMobile|Opera Mini/i.test(navigator.userAgent)
            || (window.matchMedia('(pointer: coarse)').matches && window.innerWidth <= 1024);
    }

    function triggerNativeUpload(input) {
        if (!input) {
            return;
        }

        input.removeAttribute('capture');
        input.accept = isMobileDevice()
            ? 'image/jpeg,image/png,image/webp'
            : '.jpg,.jpeg,.jfif,.png,.webp,image/jpeg,image/png,image/webp';
        input.click();
    }

    function handleUploadBoxKey(event, uploadBox) {
        if (!['Enter', ' '].includes(event.key)) {
            return;
        }

        event.preventDefault();
        triggerNativeUpload(uploadBox.querySelector('.customer-upload-input'));
    }

    function showUploadToast(message) {
        const toast = byId('uploadToast');
        const text = byId('uploadToastMessage');

        if (!toast || !text) {
            return;
        }

        text.textContent = message;
        toast.classList.add('show');
        clearTimeout(uploadToastTimer);
        uploadToastTimer = setTimeout(() => toast.classList.remove('show'), 4500);
    }

    function showInlineUploadNotice(message) {
        const notice = byId('uploadInlineNotice');
        const text = byId('uploadInlineNoticeMessage');

        if (!notice || !text) {
            return;
        }

        text.textContent = message;
        notice.classList.add('show');
    }

    function clearInlineUploadNotice() {
        const notice = byId('uploadInlineNotice');
        const text = byId('uploadInlineNoticeMessage');

        notice?.classList.remove('show');

        if (text) {
            text.textContent = '';
        }
    }

    function uploadElements(input) {
        const box = input.closest('.upload-box-compact, .upload-box-large');
        const group = input.closest('.form-group, .barang-upload-wrapper');

        return {
            box,
            error: group?.querySelector('.field-error') ?? null,
            preview: box?.querySelector('.upload-preview-compact, .upload-preview-large') ?? null,
        };
    }

    function showUploadError(input, message) {
        const elements = uploadElements(input);
        elements.box?.classList.add('upload-error');

        if (elements.error) {
            elements.error.textContent = message;
            elements.error.classList.add('show');
        }

        showUploadToast(message);

        if (input.name === 'foto_barang[]') {
            showInlineUploadNotice(message);
        }
    }

    function clearUploadError(input) {
        const elements = uploadElements(input);
        elements.box?.classList.remove('upload-error');

        if (elements.error) {
            elements.error.textContent = '';
            elements.error.classList.remove('show');
        }

        if (input.name === 'foto_barang[]') {
            clearInlineUploadNotice();
        }
    }

    function resetPreview(input) {
        const elements = uploadElements(input);

        if (elements.preview) {
            elements.preview.src = '';
        }

        elements.box?.classList.remove('has-image');
    }

    function validateFileInput(input) {
        clearUploadError(input);

        if (!input.files?.[0]) {
            showUploadError(input, 'File wajib dipilih.');
            return false;
        }

        const file = input.files[0];
        const extension = file.name.split('.').pop().toLowerCase();

        if (!allowedExtensions.includes(extension) || !allowedMimeTypes.includes(file.type)) {
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

    function previewFile(input, compact) {
        if (!input.files?.[0] || !validateFileInput(input)) {
            return;
        }

        const box = input.closest(compact ? '.upload-box-compact' : '.upload-box-large');
        const preview = box.querySelector(compact ? '.upload-preview-compact' : '.upload-preview-large');
        const reader = new FileReader();

        reader.addEventListener('load', event => {
            preview.src = event.target.result;
            box.classList.add('has-image');
        });

        reader.readAsDataURL(input.files[0]);
    }

    function selectedItems() {
        const duration = Number(byId('lama_sewa')?.value || 0);

        return all('.barang-item').flatMap(item => {
            const select = item.querySelector('.alat-select');
            const quantityInput = item.querySelector('.jumlah-input');
            const option = select.options[select.selectedIndex];

            if (!option?.value) {
                return [];
            }

            const quantity = Math.max(1, Number(quantityInput.value || 1));
            const price = Number(option.dataset.harga || 0);

            return [{
                name: option.dataset.nama || option.textContent.trim(),
                price,
                quantity,
                duration,
                subtotal: price * quantity * duration,
            }];
        });
    }

    function updateSummary(total) {
        const items = selectedItems();
        const duration = Number(byId('lama_sewa')?.value || 0);
        const sidebar = byId('summary-list');
        const wide = byId('summary-wide-list');

        if (sidebar) {
            sidebar.innerHTML = items.length
                ? items.map(item => `
                    <div class="summary-item">
                        <strong>${escapeHtml(item.name)}</strong>
                        <span>${item.quantity} barang × ${item.duration} hari</span>
                        <span>${rupiah(item.subtotal)}</span>
                    </div>
                `).join('')
                : '<div class="summary-empty">Belum ada barang yang dipilih.</div>';
        }

        if (wide) {
            wide.innerHTML = items.length
                ? items.map(item => `
                    <div class="summary-wide-item">
                        <div>
                            <span class="summary-wide-item-name">${escapeHtml(item.name)}</span>
                            <span class="summary-wide-item-detail">${item.quantity} × ${item.duration} hari × ${rupiah(item.price)}/hari</span>
                        </div>
                        <span class="summary-wide-item-price">${rupiah(item.subtotal)}</span>
                    </div>
                `).join('')
                : '<div class="summary-wide-empty">Belum ada barang yang dipilih.</div>';
        }

        ['summary-duration', 'summary-wide-duration'].forEach(id => {
            const element = byId(id);

            if (element) {
                element.textContent = duration > 0 ? `${duration} hari` : '-';
            }
        });

        ['total-harga', 'total-harga-final', 'total-harga-wide'].forEach(id => {
            const element = byId(id);

            if (element) {
                element.textContent = rupiah(total);
            }
        });
    }

    function hitungTotal() {
        let total = 0;
        const duration = Number(byId('lama_sewa')?.value || 0);

        all('.barang-item').forEach(item => {
            const select = item.querySelector('.alat-select');
            const quantityInput = item.querySelector('.jumlah-input');
            const option = select.options[select.selectedIndex];
            const stock = Number(option?.dataset.stok || 0);
            const price = Number(option?.dataset.harga || 0);
            let quantity = Math.max(1, Number(quantityInput.value || 1));

            if (stock > 0 && quantity > stock) {
                quantity = stock;
                quantityInput.value = stock;
                showUploadToast('Jumlah tidak boleh melebihi stok tersedia.');
            } else {
                quantityInput.value = quantity;
            }

            total += price * quantity * duration;
        });

        updateSummary(total);
    }

    function duplicateValues() {
        const counts = new Map();

        all('.alat-select').forEach(select => {
            if (select.value) {
                counts.set(select.value, (counts.get(select.value) || 0) + 1);
            }
        });

        return new Set([...counts].filter(([, count]) => count > 1).map(([value]) => value));
    }

    function clearDuplicateServerErrors() {
        all('[data-error-field]').forEach(element => {
            if (element.textContent.trim() !== duplicateMessage) {
                return;
            }

            element.closest('.barang-select-wrapper')
                ?.querySelector('.alat-select')
                ?.classList.remove('is-invalid');
            element.remove();
        });

        const list = byId('formErrorList');
        const summary = byId('formErrorSummary');

        if (list && !list.querySelector('li')) {
            summary?.remove();
        }
    }

    function validateUniqueTools(focus = false) {
        const duplicates = duplicateValues();
        const notice = byId('duplicateToolNotice');
        let firstDuplicate;

        all('.alat-select').forEach(select => {
            const duplicate = select.value && duplicates.has(select.value);
            select.classList.toggle('duplicate-invalid', Boolean(duplicate));

            if (duplicate && !firstDuplicate) {
                firstDuplicate = select;
            }
        });

        if (duplicates.size === 0) {
            if (notice) {
                notice.hidden = true;
            }

            clearDuplicateServerErrors();
            return true;
        }

        if (notice) {
            notice.hidden = false;
        }

        if (focus && firstDuplicate) {
            firstDuplicate.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstDuplicate.focus({ preventScroll: true });
        }

        return false;
    }

    function handleToolChange() {
        validateUniqueTools(false);
        hitungTotal();
    }

    function tambahJumlah(button) {
        const input = button.parentElement.querySelector('input');
        input.value = Number(input.value || 1) + 1;
        hitungTotal();
    }

    function kurangiJumlah(button) {
        const input = button.parentElement.querySelector('input');
        input.value = Math.max(1, Number(input.value || 1) - 1);
        hitungTotal();
    }

    function resetRentalItem(item) {
        item.querySelector('.alat-select').value = '';
        item.querySelector('.jumlah-input').value = 1;
        const fileInput = item.querySelector('input[type="file"]');
        fileInput.value = '';
        resetPreview(fileInput);
        item.querySelector('.upload-box-compact')?.classList.remove('upload-error');
        all('.field-error', item).forEach(error => {
            error.textContent = '';
            error.classList.remove('show');
        });
        item.querySelector('.btn-hapus').style.display = 'inline-flex';
    }

    function updateItemNumbers() {
        const items = all('.barang-item');

        items.forEach((item, index) => {
            item.querySelector('.barang-number').textContent = index + 1;
            item.querySelector('.btn-hapus').style.display = items.length === 1 ? 'none' : 'inline-flex';
        });
    }

    function tambahBarang() {
        const list = byId('barang-list');
        const item = list.querySelector('.barang-item').cloneNode(true);
        resetRentalItem(item);
        list.appendChild(item);
        updateItemNumbers();
        validateUniqueTools(false);
        hitungTotal();
    }

    function hapusBarang(button) {
        button.closest('.barang-item').remove();
        updateItemNumbers();
        validateUniqueTools(false);
        hitungTotal();
    }

    function pindahStep(step) {
        [1, 2, 3].forEach(number => {
            byId(`step${number}`).classList.add('hidden');
            byId(`stepNav${number}`).classList.remove('active', 'done');
        });

        const layout = byId('rentalLayout');
        const summaryCard = byId('summaryCard');
        const summaryWide = byId('summaryWide');

        if (step === 1) {
            byId('stepNav1').classList.add('active');
            layout.classList.remove('has-sidebar');
            summaryCard.style.display = 'none';
            summaryWide.style.display = 'block';
        } else if (step === 2) {
            byId('stepNav1').classList.add('done');
            byId('stepNav2').classList.add('active');
            layout.classList.remove('has-sidebar');
            summaryCard.style.display = 'none';
            summaryWide.style.display = 'none';
        } else {
            byId('stepNav1').classList.add('done');
            byId('stepNav2').classList.add('done');
            byId('stepNav3').classList.add('active');
            layout.classList.add('has-sidebar');
            summaryCard.style.display = 'block';
            summaryWide.style.display = 'none';
        }

        byId(`step${step}`).classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function focusInvalidInput(input, step) {
        pindahStep(step);

        setTimeout(() => {
            input.scrollIntoView({ behavior: 'smooth', block: 'center' });

            if (input.type !== 'file') {
                input.reportValidity();
            }
        }, 250);
    }

    function validasiStep(step) {
        if (step === 1 && !validateUniqueTools(true)) {
            return false;
        }

        const inputs = all('input, select, textarea', byId(`step${step}`));

        for (const input of inputs) {
            const valid = input.type === 'file' ? validateFileInput(input) : input.checkValidity();

            if (!valid) {
                focusInvalidInput(input, step);
                return false;
            }
        }

        return true;
    }

    function lanjutStep(target) {
        if (validasiStep(target - 1)) {
            hitungTotal();
            pindahStep(target);
        }
    }

    function setSubmitting(state) {
        submitting = state;
        all('.btn-submit').forEach(button => {
            button.disabled = state;
            button.setAttribute('aria-disabled', state ? 'true' : 'false');
        });
    }

    function submitForm() {
        if (submitting || !validasiStep(1) || !validasiStep(2)) {
            return;
        }

        form.requestSubmit();
    }

    function triggerFileInput(button) {
        triggerNativeUpload(button.closest('.barang-upload-wrapper')?.querySelector('.customer-upload-input'));
    }

    function previewImageModal(image) {
        if (!image.src) {
            return;
        }

        const modal = document.createElement('div');
        modal.className = 'image-preview-modal';
        modal.innerHTML = `
            <img src="${escapeHtml(image.src)}" alt="Preview gambar">
            <button type="button" aria-label="Tutup preview">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
        `;

        const close = () => modal.remove();
        modal.addEventListener('click', close);
        modal.querySelector('img').addEventListener('click', event => event.stopPropagation());
        modal.querySelector('button').addEventListener('click', close);
        document.body.appendChild(modal);
    }

    form.addEventListener('submit', event => {
        if (submitting) {
            event.preventDefault();
            return;
        }

        if (!validasiStep(1) || !validasiStep(2)) {
            event.preventDefault();
            return;
        }

        setSubmitting(true);
    });

    Object.assign(window, {
        handleToolChange,
        handleUploadBoxKey,
        hapusBarang,
        hitungTotal,
        kurangiJumlah,
        lanjutStep,
        pindahStep,
        previewFile: input => previewFile(input, false),
        previewFileCompact: input => previewFile(input, true),
        previewFileLarge: input => previewFile(input, false),
        previewImageModal,
        submitForm,
        tambahBarang,
        tambahJumlah,
        triggerFileInput,
        triggerNativeUpload,
    });

    hitungTotal();
    validateUniqueTools(false);
    pindahStep(Number(form.dataset.initialStep || 1));
}
