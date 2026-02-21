
// Dispatch Panel JS (merged with create logic)
// Handles location selection, qty validation, dynamic UI updates, and form-level validation

document.addEventListener('DOMContentLoaded', function () {
    // Location dropdown logic
    document.querySelectorAll('.location-type-select').forEach(function(select) {
        select.addEventListener('change', function() {
            var itemId = this.getAttribute('data-item-id');
            var storeDropdown = document.querySelector('select.store-dropdown[data-item-id="' + itemId + '"]');
            var warehouseDropdown = document.querySelector('select.warehouse-dropdown[data-item-id="' + itemId + '"]');
            var qtyInput = document.querySelector('input.dispatch-qty-input[data-item-id="' + itemId + '"]');
            var remaining = parseInt(document.querySelector('.remaining-qty[data-item-id="' + itemId + '"]').textContent);
            // Reset dropdowns
            storeDropdown.value = '';
            warehouseDropdown.value = '';
            var maxQty = remaining;
            if (this.value === 'store') {
                storeDropdown.classList.remove('d-none');
                warehouseDropdown.classList.add('d-none');
                // Set maxQty based on first store option
                if (storeDropdown.options.length > 1) {
                    var selected = storeDropdown.options[storeDropdown.selectedIndex];
                    var match = selected.textContent.match(/Qty: (\d+)/);
                    if (match) maxQty = Math.min(parseInt(match[1]), remaining);
                }
            } else if (this.value === 'warehouse') {
                warehouseDropdown.classList.remove('d-none');
                storeDropdown.classList.add('d-none');
                // Set maxQty based on first warehouse option
                if (warehouseDropdown.options.length > 1) {
                    var selected = warehouseDropdown.options[warehouseDropdown.selectedIndex];
                    var match = selected.textContent.match(/Qty: (\d+)/);
                    if (match) maxQty = Math.min(parseInt(match[1]), remaining);
                }
            } else {
                storeDropdown.classList.add('d-none');
                warehouseDropdown.classList.add('d-none');
            }
            // Set max for qty input
            if (qtyInput) {
                qtyInput.max = maxQty;
                if (parseInt(qtyInput.value) > maxQty) qtyInput.value = maxQty;
            }
        });
    });
    // Update max qty on store/warehouse dropdown change
    document.querySelectorAll('.store-dropdown, .warehouse-dropdown').forEach(function(drop) {
        drop.addEventListener('change', function() {
            var itemId = this.getAttribute('data-item-id');
            var qtyInput = document.querySelector('input.dispatch-qty-input[data-item-id="' + itemId + '"]');
            var selected = this.options[this.selectedIndex];
            var match = selected.textContent.match(/Qty: (\d+)/);
            var maxQty = match ? parseInt(match[1]) : 0;
            var remaining = parseInt(document.querySelector('.remaining-qty[data-item-id="' + itemId + '"]').textContent);
            qtyInput.max = Math.min(maxQty, remaining);
            if (parseInt(qtyInput.value) > qtyInput.max) qtyInput.value = qtyInput.max;
        });
    });
    // Validate qty input
    document.querySelectorAll('.dispatch-qty-input').forEach(function(input) {
        input.addEventListener('input', function() {
            var itemId = this.getAttribute('data-item-id');
            var min = parseInt(this.min);
            var max = parseInt(this.max);
            var val = parseInt(this.value);
            var errorDiv = document.getElementById('qty-error-' + itemId);
            // If empty or not integer, set to min
            if (this.value === '' || isNaN(val) || !Number.isInteger(val)) {
                this.value = min;
                val = min;
            }
            if (val < min) {
                errorDiv.textContent = 'Quantity must be at least ' + min;
                errorDiv.classList.remove('d-none');
                this.classList.add('is-invalid');
            } else if (val > max) {
                errorDiv.textContent = 'Cannot dispatch more than available (' + max + ')';
                errorDiv.classList.remove('d-none');
                this.classList.add('is-invalid');
            } else {
                errorDiv.textContent = '';
                errorDiv.classList.add('d-none');
                this.classList.remove('is-invalid');
            }
        });
    });

    // Form-level validation before submission
    var form = document.getElementById('dispatchForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        var valid = true;
        // Validate all dispatch qty inputs
        document.querySelectorAll('.dispatch-qty-input').forEach(function(input) {
            var min = parseInt(input.min);
            var max = parseInt(input.max);
            var val = parseInt(input.value);
            var itemId = input.getAttribute('data-item-id');
            var errorDiv = document.getElementById('qty-error-' + itemId);
            if (isNaN(val) || val < min) {
                errorDiv.textContent = 'Quantity must be at least ' + min;
                errorDiv.classList.remove('d-none');
                input.classList.add('is-invalid');
                valid = false;
            } else if (val > max) {
                errorDiv.textContent = 'Cannot dispatch more than available (' + max + ')';
                errorDiv.classList.remove('d-none');
                input.classList.add('is-invalid');
                valid = false;
            } else {
                errorDiv.textContent = '';
                errorDiv.classList.add('d-none');
                input.classList.remove('is-invalid');
            }
        });

        // Validate location selection for each item
        document.querySelectorAll('.location-type-select').forEach(function(select) {
            var itemId = select.getAttribute('data-item-id');
            var type = select.value;
            var storeDropdown = document.querySelector('select.store-dropdown[data-item-id="' + itemId + '"]');
            var warehouseDropdown = document.querySelector('select.warehouse-dropdown[data-item-id="' + itemId + '"]');
            if (type === 'store') {
                if (!storeDropdown.value) {
                    storeDropdown.classList.add('is-invalid');
                    valid = false;
                } else {
                    storeDropdown.classList.remove('is-invalid');
                }
            } else if (type === 'warehouse') {
                if (!warehouseDropdown.value) {
                    warehouseDropdown.classList.add('is-invalid');
                    valid = false;
                } else {
                    warehouseDropdown.classList.remove('is-invalid');
                }
            } else {
                if (storeDropdown) storeDropdown.classList.remove('is-invalid');
                if (warehouseDropdown) warehouseDropdown.classList.remove('is-invalid');
            }
        });

        if (!valid) {
            e.preventDefault();
        }
    });
});
