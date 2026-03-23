// contact.js
// Handles conditional fields and file upload logic for the contact form

// ── Conditional field maps ─────────────────────────────────────────
const showOrderFor = new Set(['order_status', 'return_replacement', 'warranty_claim', 'complaint', 'installation_support']);
const showSkuFor   = new Set(['product_inquiry', 'installation_support', 'technical_issue', 'warranty_claim']);

const subjectEl         = document.getElementById('subject');
const orderWrapper      = document.getElementById('orderNumberWrapper');
const skuWrapper        = document.getElementById('productSkuWrapper');
const orderInput        = document.getElementById('order_number');
const skuInput          = document.getElementById('product_sku');

function toggleConditionals() {
    const val = subjectEl.value;

    const needOrder = showOrderFor.has(val);
    const needSku   = showSkuFor.has(val);

    // Order Number
    orderWrapper.classList.toggle('hidden', !needOrder);
    orderInput.required = needOrder;

    // Product SKU
    skuWrapper.classList.toggle('hidden', !needSku);
}

subjectEl.addEventListener('change', toggleConditionals);
// Run on page load in case of old() values
toggleConditionals();

// ── Drag & Drop ────────────────────────────────────────────────────
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('attachments');

if (dropZone && fileInput) {
    ['dragenter', 'dragover'].forEach(e => {
        dropZone.addEventListener(e, ev => {
            ev.preventDefault();
            dropZone.classList.add('border-primary', 'bg-primary/10');
        });
    });

    ['dragleave', 'drop'].forEach(e => {
        dropZone.addEventListener(e, ev => {
            ev.preventDefault();
            dropZone.classList.remove('border-primary', 'bg-primary/10');
        });
    });

    dropZone.addEventListener('drop', ev => {
        const dt = ev.dataTransfer;
        const files = dt.files;
        // Merge dropped files into the input
        const dataTransfer = new DataTransfer();
        [...(fileInput.files || []), ...files].slice(0, 5).forEach(f => dataTransfer.items.add(f));
        fileInput.files = dataTransfer.files;
        renderFileList(fileInput.files);
    });
}

// ── File Select & Preview ──────────────────────────────────────────
window.handleFileSelect = function(input) {
    const MAX_FILES = 5;
    const MAX_SIZE  = 5 * 1024 * 1024; // 5 MB

    // Trim to 5 files
    if (input.files.length > MAX_FILES) {
        alert(trans(`${trans('You can upload up to')} ${MAX_FILES} ${trans('files')}.`));
        const dt = new DataTransfer();
        [...input.files].slice(0, MAX_FILES).forEach(f => dt.items.add(f));
        input.files = dt.files;
    }

    // Check sizes
    const oversized = [...input.files].filter(f => f.size > MAX_SIZE);
    if (oversized.length) {
        alert(`${trans('The following files exceed 5 MB')}:\n${oversized.map(f => f.name).join('\n')}`);
        const dt = new DataTransfer();
        [...input.files].filter(f => f.size <= MAX_SIZE).forEach(f => dt.items.add(f));
        input.files = dt.files;
    }

    renderFileList(input.files);
};

function renderFileList(files) {
    const list = document.getElementById('fileList');
    if (!list) return;
    list.innerHTML = '';

    [...files].forEach((file, idx) => {
        const isImage = file.type.startsWith('image/');
        const icon = isImage
            ? `<svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>`
            : `<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>`;

        const sizeKB = (file.size / 1024).toFixed(1);

        const li = document.createElement('li');
        li.className = 'flex items-center justify-between gap-3 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2.5 text-sm';
        li.innerHTML = `
            <div class="flex items-center gap-2 min-w-0">
                ${icon}
                <span class="truncate text-gray-700 font-medium">${file.name}</span>
                <span class="text-gray-400 shrink-0">${sizeKB} KB</span>
            </div>
            <button type="button" onclick="removeFile(${idx})"
                class="shrink-0 w-6 h-6 rounded-full bg-red-100 hover:bg-red-200 flex items-center justify-center text-red-500 transition-colors duration-200">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;
        list.appendChild(li);
    });
}

window.removeFile = function(idx) {
    const dt = new DataTransfer();
    [...fileInput.files].filter((_, i) => i !== idx).forEach(f => dt.items.add(f));
    fileInput.files = dt.files;
    renderFileList(fileInput.files);
};

// AJAX submission for contact form
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(contactForm);
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50');

        fetch(contactForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
            },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50');
            if (data.status) {
                if (window.showUniversalPopup) {
                    showUniversalPopup(data.message, 'success');
                } else {
                    alert(data.message);
                }
                contactForm.reset();
                renderFileList([]);
            } else {
                if (window.showUniversalPopup) {
                    showUniversalPopup(data.message || trans('Submission failed.'), 'error');
                } else {
                    alert(data.message || trans('Submission failed.'));
                }
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50');
            if (window.showUniversalPopup) {
                showUniversalPopup(trans('An error occurred. Please try again.'), 'error');
            } else {
                alert(trans('An error occurred. Please try again.'));
            }
        });
    });
}
