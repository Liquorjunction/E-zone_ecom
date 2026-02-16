document.addEventListener('DOMContentLoaded', function () {
    function initSelect2ForDropdowns(scope = document) {
        const $jq = window.jQuery || window.$;
        if (!$jq || typeof $jq.fn.select2 !== 'function') {
            console.warn('Select2 is not loaded. Dropdown search will not be available.');
            return;
        }
        $jq('.product-select', scope).select2({
            width: '100%',
            placeholder: 'Select Product',
            allowClear: true
        });
        $jq('.variant-select', scope).select2({
            width: '100%',
            placeholder: 'Select Variant',
            allowClear: true
        });
    }

    "use strict";

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
    if (!mainForm) return;

    const getVariantsUrl = mainForm.getAttribute('data-variants-url');

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

    function fetchVariants(productId, variantSelect) {
        const $jq = window.jQuery || window.$;
        if (!$jq) {
            console.error('jQuery is required for dropdowns to work.');
            return;
        }
        const $variantSelect = $jq(variantSelect);

        if ($variantSelect.hasClass('select2-hidden-accessible')) {
            $variantSelect.select2('destroy');
        }

        $variantSelect.empty().append('<option value="">Loading...</option>');
        $variantSelect.prop('disabled', true);
        $variantSelect.trigger('change.select2');

        if (!productId) {
            $variantSelect.empty().append('<option value="">Select Product First</option>');
            $variantSelect.prop('disabled', false);
            $variantSelect.trigger('change.select2');
            if (typeof $variantSelect.select2 === 'function') {
                $variantSelect.select2({ width: '100%', placeholder: 'Select Variant', allowClear: true });
            }
            return;
        }

        fetch(`${getVariantsUrl}?product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                $variantSelect.empty();
                $variantSelect.prop('disabled', false);

                if (data.length > 0) {
                    $variantSelect.append('<option value="">Select Variant</option>');

                    let locale = document.documentElement.lang || 'en';

                    data.forEach(v => {
                        let name = v.variant_name;
                        if (typeof name === 'object' && name !== null) {
                            name = name[locale] || name['en'] || Object.values(name)[0] || '';
                        }
                        const newOption = new Option(name, v.id, false, false);
                        $variantSelect.append(newOption);
                    });
                } else {
                    $variantSelect.append('<option value="">No Variants Found</option>');
                }

                if (typeof $variantSelect.select2 === 'function') {
                    $variantSelect.select2({ width: '100%', placeholder: 'Select Variant', allowClear: true });
                }
                $variantSelect.trigger('change.select2');
            })
            .catch(err => {
                console.error(err);
                $variantSelect.empty().append('<option value="">Error Loading</option>');
                if (typeof $variantSelect.select2 === 'function') {
                    $variantSelect.select2({ width: '100%', placeholder: 'Select Variant', allowClear: true });
                }
                $variantSelect.trigger('change.select2');
            });
    }

    function initBatchTable() {
        const addBtn = $(config.selectors.addRowBtn);
        const tableBody = $(config.selectors.tableBody);
        const template = $(config.selectors.rowTemplate);

        if (!addBtn || !tableBody || !template) return;

        let rowIdx = tableBody.querySelectorAll('tr').length;

        function addRow() {
            let html = template.innerHTML.replace(/INDEX/g, rowIdx);
            tableBody.insertAdjacentHTML('beforeend', html);
            rowIdx++;
            const lastRow = tableBody.querySelector('tr:last-child');
            if (lastRow) {
                initSelect2ForDropdowns(lastRow);
            }
        }

        addBtn.addEventListener('click', addRow);

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

        function handleProductSelectChange(e) {
            if (e.target.classList.contains('product-select')) {
                const productId = e.target.value;
                const row = e.target.closest('tr');
                const variantSelect = row.querySelector(config.selectors.variantSelect);
                fetchVariants(productId, variantSelect);
            }
        }
        tableBody.addEventListener('change', handleProductSelectChange);

        const $jq = window.jQuery || window.$;
        if ($jq) {
            $jq(tableBody).on('select2:select', '.product-select', function (e) {
                const productId = $jq(this).val();
                const row = this.closest('tr');
                const variantSelect = row.querySelector(config.selectors.variantSelect);
                fetchVariants(productId, variantSelect);
            });
        }

        if (rowIdx === 0) {
            addRow();
        }
        initSelect2ForDropdowns(tableBody);
    }

    initLocationToggle();
    initBatchTable();
    initSelect2ForDropdowns(document);
});
