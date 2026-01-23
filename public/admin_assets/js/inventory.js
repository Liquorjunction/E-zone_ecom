document.addEventListener('DOMContentLoaded', function () {
    "use strict";

    // --- Configuration & Selectors ---
    const config = {
        selectors: {
            batchForm: '#batch-form',
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

    const mainForm = $(config.selectors.batchForm);
    if (!mainForm) return; // Exit if not on the correct page

    const getVariantsUrl = mainForm.getAttribute('data-variants-url');

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
        // Reset UI
        variantSelect.innerHTML = '<option value="">Loading...</option>';
        variantSelect.disabled = true;

        if (!productId) {
            variantSelect.innerHTML = '<option value="">Select Product First</option>';
            return;
        }

        fetch(`${getVariantsUrl}?product_id=${productId}`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                variantSelect.disabled = false;
                if (data.length > 0) {
                    let options = '<option value="">Select Variant</option>';
                    // Get current locale from <html lang> or fallback
                    let locale = document.documentElement.lang || 'en';
                    data.forEach(v => {
                        let name = v.variant_name;
                        if (typeof name === 'object' && name !== null) {
                            // Try to get translation for current locale
                            name = name[locale] || name['en'] || Object.values(name)[0] || '';
                        }
                        options += `<option value="${v.id}">${name}</option>`;
                    });
                    variantSelect.innerHTML = options;
                } else {
                    variantSelect.innerHTML = '<option value="">No Variants Found</option>';
                }
            })
            .catch(err => {
                console.error(err);
                variantSelect.innerHTML = '<option value="">Error</option>';
            });
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

        // Variant Fetching Event (Delegation for dynamic rows)
        tableBody.addEventListener('change', function (e) {
            if (e.target.classList.contains('product-select')) {
                const productId = e.target.value;
                const row = e.target.closest('tr');
                const variantSelect = row.querySelector(config.selectors.variantSelect);

                fetchVariants(productId, variantSelect);
            }
        });

        // Init: Add first row if table is empty
        if (rowIdx === 0) {
            addRow();
        }
    }

    // --- Run Initialization ---
    initLocationToggle();
    initBatchTable();
});
