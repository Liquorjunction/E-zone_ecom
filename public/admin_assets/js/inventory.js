document.addEventListener('DOMContentLoaded', function () {
    "use strict";

    // --- Configuration & Selectors ---
    const config = {
        selectors: {
            batchForm: '#batch-form',
            editForm: '#edit-form',
            locationRadio: 'input[name="location_type"]',
            warehouseDiv: '#warehouse_div',
            storeDiv: '#store_div',
            warehouseSelect: 'select[name="warehouse_id"]',
            storeSelect: 'select[name="store_id"]',
            productSelect: '.product-select',
            variantSelect: '.variant-select',
            addRowBtn: '#add-row-btn',
            tableBody: '#table-body',
            rowTemplate: '#row-template',
            removeRowBtn: '.remove-row'
        }
    };

    // Helper functions
    const $ = (selector, scope = document) => scope.querySelector(selector);
    const $$ = (selector, scope = document) => scope.querySelectorAll(selector);

    const mainForm = $(config.selectors.batchForm) || $(config.selectors.editForm);
    if (!mainForm) return; // Exit if not on the correct page

    const getVariantsUrl = mainForm.getAttribute('data-variants-url');

    function initializeSelect2(scope = document) {
        if (typeof window.$ === 'undefined' || !window.$.fn || !window.$.fn.select2) return;

        window.$(scope).find(config.selectors.productSelect + ', ' + config.selectors.variantSelect).each(function () {
            const $el = window.$(this);
            if ($el.data('select2')) return;

            const isProduct = $el.hasClass('product-select');
            $el.select2({
                width: '100%',
                allowClear: true,
                placeholder: isProduct ? 'Select Product' : 'Select Variant'
            });
        });
    }

    // ----------------------------------------------------------------------
    // 1. Location Toggle Logic (Warehouse vs Store)
    // ----------------------------------------------------------------------
    function initLocationToggle() {
        const radios = $$(config.selectors.locationRadio);
        const whDiv = $(config.selectors.warehouseDiv);
        const stDiv = $(config.selectors.storeDiv);
        const whSelect = $(config.selectors.warehouseSelect);
        const stSelect = $(config.selectors.storeSelect);

        function toggle(targetValue) {
            if (targetValue === 'warehouse') {
                whDiv.style.display = '';
                stDiv.style.display = 'none';
                if (whSelect) whSelect.required = true;
                if (stSelect) {
                    stSelect.required = false;
                    stSelect.value = ""; // Clear value
                }
            } else {
                whDiv.style.display = 'none';
                stDiv.style.display = '';
                if (whSelect) {
                    whSelect.required = false;
                    whSelect.value = ""; // Clear value
                }
                if (stSelect) stSelect.required = true;
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', (e) => toggle(e.target.value));
            // Run on init if checked
            if (radio.checked) toggle(radio.value);
        });
    }

    // ----------------------------------------------------------------------
    // 2. Variant Fetching (AJAX)
    // ----------------------------------------------------------------------
    function fetchVariants(productId, variantSelect) {
        const $variant = (typeof window.$ !== 'undefined') ? window.$(variantSelect) : null;
        const hadSelect2 = !!($variant && $variant.data('select2'));

        if (hadSelect2) {
            try { $variant.select2('destroy'); } catch (e) { }
        }

        // Reset UI
        variantSelect.innerHTML = '<option value="">Loading...</option>';
        variantSelect.disabled = true;

        if (!productId) {
            variantSelect.innerHTML = '<option value="">Select Product First</option>';
            variantSelect.disabled = false;
            if (hadSelect2) initializeSelect2(variantSelect.parentElement || document);
            return;
        }

        if (!getVariantsUrl) {
            variantSelect.innerHTML = '<option value="">Error</option>';
            variantSelect.disabled = false;
            if (hadSelect2) initializeSelect2(variantSelect.parentElement || document);
            return;
        }

        fetch(`${getVariantsUrl}?product_id=${encodeURIComponent(productId)}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                let variants = [];
                if (Array.isArray(data)) {
                    variants = data;
                } else if (Array.isArray(data?.data)) {
                    variants = data.data;
                } else if (data && typeof data === 'object') {
                    // Also support object maps: {"1": {...}, "2": {...}}
                    variants = Object.values(data).filter(v => v && typeof v === 'object');
                }

                variantSelect.disabled = false;
                if (variants.length > 0) {
                    let options = '<option value="">Select Variant</option>';
                    // Get current locale from <html lang> or fallback
                    let locale = document.documentElement.lang || 'en';
                    variants.forEach(v => {
                        let name = v.variant_name;
                        if (typeof name === 'object' && name !== null) {
                            // Try to get translation for current locale
                            name = name[locale] || name['en'] || Object.values(name)[0] || '';
                        }
                        if (!name || String(name).trim() === '') {
                            name = v.name || v.title || v.sku || `Variant #${v.id}`;
                        }
                        options += `<option value="${v.id}">${name}</option>`;
                    });
                    variantSelect.innerHTML = options;
                } else {
                    variantSelect.innerHTML = '<option value="">No Variants Found</option>';
                }

                initializeSelect2(variantSelect.parentElement || document);
            })
            .catch(err => {
                console.error(err);
                variantSelect.innerHTML = '<option value="">Error</option>';
                variantSelect.disabled = false;
                initializeSelect2(variantSelect.parentElement || document);
            });
    }

    function initVariantChangeBinding() {
        function handleProductSelection(target) {
            if (!target || !target.classList || !target.classList.contains('product-select')) return;

            const productId = target.value;
            const row = target.closest('tr');
            const variantSelect = row
                ? row.querySelector(config.selectors.variantSelect)
                : mainForm.querySelector(config.selectors.variantSelect);

            if (!variantSelect) return;
            fetchVariants(productId, variantSelect);
        }

        mainForm.addEventListener('change', function (e) {
            handleProductSelection(e.target);
        });

        // Some Select2 setups fire plugin events more reliably than native change.
        if (typeof window.$ !== 'undefined') {
            window.$(mainForm).on('select2:select select2:clear', config.selectors.productSelect, function () {
                handleProductSelection(this);
            });
        }
    }

    // ----------------------------------------------------------------------
    // 3. Batch Table Logic (Add/Remove Rows)
    // ----------------------------------------------------------------------
    function initBatchTable() {
        const addBtn = $(config.selectors.addRowBtn);
        const tableBody = $(config.selectors.tableBody);
        const template = $(config.selectors.rowTemplate);

        if (!addBtn || !tableBody || !template) return;

        // Determine starting index based on existing rows (server rendered)
        let rowIdx = tableBody.querySelectorAll('tr').length;

        function addRow() {
            // Replace INDEX placeholder with unique ID
            let html = template.innerHTML.replace(/INDEX/g, rowIdx);
            tableBody.insertAdjacentHTML('beforeend', html);
            initializeSelect2(tableBody.lastElementChild || tableBody);
            rowIdx++;
        }

        // Add Row Event
        addBtn.addEventListener('click', addRow);

        // Remove Row Event (Delegation)
        tableBody.addEventListener('click', function (e) {
            if (e.target.closest(config.selectors.removeRowBtn)) {
                const rows = tableBody.querySelectorAll('tr');
                if (rows.length > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert("You must have at least one product in the list.");
                }
            }
        });

        // Init: Add first row if table is empty
        if (rowIdx === 0) {
            addRow();
        } else {
            initializeSelect2(tableBody);
        }
    }

    // --- Run Initialization ---
    initLocationToggle();
    initializeSelect2(mainForm);
    initVariantChangeBinding();
    initBatchTable();
});
