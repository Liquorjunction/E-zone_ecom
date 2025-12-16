document.addEventListener('DOMContentLoaded', function () {
    "use strict";

    // --- Configuration ---
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

    const $ = (selector, scope = document) => scope.querySelector(selector);
    const $$ = (selector, scope = document) => scope.querySelectorAll(selector);

    const mainForm = $(config.selectors.batchForm);
    if (!mainForm) return; // Exit if not on the form page

    const getVariantsUrl = mainForm.getAttribute('data-variants-url');

    // 1. Location Toggle Logic
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
                    stSelect.value = "";
                }
            } else {
                whDiv.style.display = 'none';
                stDiv.style.display = '';
                if (whSelect) {
                    whSelect.required = false;
                    whSelect.value = "";
                }
                if (stSelect) stSelect.required = true;
            }
        }

        radios.forEach(radio => {
            radio.addEventListener('change', (e) => toggle(e.target.value));
            if (radio.checked) toggle(radio.value);
        });
    }

    // 2. Variant Fetching
    function fetchVariants(productId, variantSelect) {
        variantSelect.innerHTML = '<option value="">Loading...</option>';
        variantSelect.disabled = true;

        if (!productId) {
            variantSelect.innerHTML = '<option value="">Select Product First</option>';
            return;
        }

        fetch(`${getVariantsUrl}?product_id=${productId}`)
            .then(r => r.json())
            .then(data => {
                variantSelect.disabled = false;
                if (data.length > 0) {
                    let options = '<option value="">Select Variant</option>';
                    data.forEach(v => {
                        options += `<option value="${v.id}">${v.variant_name}</option>`;
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

    // 3. Table Logic
    function initBatchTable() {
        const addBtn = $(config.selectors.addRowBtn);
        const tableBody = $(config.selectors.tableBody);
        const template = $(config.selectors.rowTemplate);

        // Count existing rows (edit mode or validation fail)
        let rowIdx = tableBody.querySelectorAll('tr').length;

        function addRow() {
            let html = template.innerHTML.replace(/INDEX/g, rowIdx);
            tableBody.insertAdjacentHTML('beforeend', html);
            rowIdx++;
        }

        addBtn.addEventListener('click', addRow);

        tableBody.addEventListener('click', function (e) {
            if (e.target.closest(config.selectors.removeRowBtn)) {
                if (tableBody.querySelectorAll('tr').length > 1) {
                    e.target.closest('tr').remove();
                } else {
                    alert("At least one item is required.");
                }
            }
        });

        // Delegate change event for Product Selects
        tableBody.addEventListener('change', function (e) {
            if (e.target.classList.contains('product-select')) {
                const row = e.target.closest('tr');
                const variantSelect = row.querySelector(config.selectors.variantSelect);
                fetchVariants(e.target.value, variantSelect);
            }
        });

        // Add first row if empty
        if (rowIdx === 0) addRow();
    }

    // Initialize
    initLocationToggle();
    initBatchTable();
});
