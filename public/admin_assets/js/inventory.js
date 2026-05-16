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

    function initializeSelect2(scope = document) {
        if (typeof window.initSelect2Ajax === 'function') {
            window.initSelect2Ajax(scope);
        }
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
    // 2. Row Parent-Child Wiring for Step 2
    // ----------------------------------------------------------------------
    function wireRowDependencies(scope = mainForm) {
        const rows = $$('.item-row', scope);
        rows.forEach((row, index) => {
            const productSelect = $(config.selectors.productSelect, row) || row.querySelector('select[name*="[product_id]"]');
            const variantSelect = $(config.selectors.variantSelect, row) || row.querySelector('select[name*="[product_variant_id]"]');

            if (!productSelect || !variantSelect) return;

            if (!productSelect.id) {
                productSelect.id = `item_product_row_${index}`;
            }

            variantSelect.dataset.dependsOn = `#${productSelect.id}`;
            variantSelect.dataset.dependencyParam = 'product_id';
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
            const newRow = tableBody.lastElementChild || tableBody;
            wireRowDependencies(newRow);
            initializeSelect2(newRow);
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
            wireRowDependencies(tableBody);
            initializeSelect2(tableBody);
        }
    }

    // --- Run Initialization ---
    initLocationToggle();
    wireRowDependencies(mainForm);
    initializeSelect2(mainForm);
    initBatchTable();
});
