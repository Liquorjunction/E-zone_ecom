// Dispatch Panel JS
// Handles location selection, qty validation, and dynamic UI updates

document.addEventListener('DOMContentLoaded', function () {
    // Location dropdown logic
    document.querySelectorAll('.location-type-select').forEach(function(select) {
        select.addEventListener('change', function() {
            var itemId = this.getAttribute('data-item-id');
            var storeDropdown = document.querySelector('select.store-dropdown[data-item-id="' + itemId + '"]');
            var warehouseDropdown = document.querySelector('select.warehouse-dropdown[data-item-id="' + itemId + '"]');
            var qtyInput = document.querySelector('input.dispatch-qty-input[data-item-id="' + itemId + '"]');
            var maxQty = 0;
            if (this.value === 'store') {
                storeDropdown.classList.remove('d-none');
                warehouseDropdown.classList.add('d-none');
                warehouseDropdown.value = '';
                var selected = storeDropdown.options[storeDropdown.selectedIndex];
                var match = selected.textContent.match(/Qty: (\d+)/);
                if (match) maxQty = parseInt(match[1]);
            } else if (this.value === 'warehouse') {
                warehouseDropdown.classList.remove('d-none');
                storeDropdown.classList.add('d-none');
                storeDropdown.value = '';
                var selected = warehouseDropdown.options[warehouseDropdown.selectedIndex];
                var match = selected.textContent.match(/Qty: (\d+)/);
                if (match) maxQty = parseInt(match[1]);
            } else {
                storeDropdown.classList.add('d-none');
                warehouseDropdown.classList.add('d-none');
                storeDropdown.value = '';
                warehouseDropdown.value = '';
            }
            // Set max for qty input
            if (qtyInput && maxQty > 0) {
                var remaining = parseInt(document.querySelector('.remaining-qty[data-item-id="' + itemId + '"]').textContent);
                qtyInput.max = Math.min(maxQty, remaining);
                if (parseInt(qtyInput.value) > qtyInput.max) qtyInput.value = qtyInput.max;
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
            if (isNaN(val) || val < min) {
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
});
