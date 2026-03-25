// dispatch-modal.js
// Extracted from create.blade.php
// Handles dispatch modal logic for admin dispatch create page

(function () {
    'use strict';

    // ── State ──────────────────────────────────────────────────────────────
    // allAllocations maps orderItemId → array of { inventory_id, quantity, locationLabel }
    const allAllocations = {};
    let currentItemId    = null;
    let currentRemaining = 0;
    let inventoryData    = [];

    const ITEM_INVENTORY_URL = window.ITEM_INVENTORY_URL || '';
    const CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

    // ── DOM refs ───────────────────────────────────────────────────────────
    const modalEl            = document.getElementById('dispatchModal');
    const bsModal            = modalEl ? new bootstrap.Modal(modalEl) : null;
    const modalSubtitle      = document.getElementById('modal-item-subtitle');
    const remainingDisplay   = document.getElementById('modal-remaining-display');
    const allocatedDisplay   = document.getElementById('modal-allocated-display');
    const stillNeededDisplay = document.getElementById('modal-still-needed-display');
    const loadingEl          = document.getElementById('modal-loading');
    const errorEl            = document.getElementById('modal-error');
    const splitContainer     = document.getElementById('split-rows-container');
    const addSplitBtn        = document.getElementById('add-split-btn');
    const validationMsg      = document.getElementById('modal-validation-msg');
    const footerHint         = document.getElementById('modal-footer-hint');
    const confirmBtn         = document.getElementById('confirm-dispatch-btn');
    const splitTemplate      = document.getElementById('split-row-template');
    const submitBtn          = document.getElementById('submitDispatchBtn');
    const hiddenWrap         = document.getElementById('dispatch-hidden-inputs');
    const statusMsg          = document.getElementById('allocation-status-msg');

    // ── Open modal on "Dispatch" button click ──────────────────────────────
    document.querySelectorAll('.open-dispatch-modal').forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentItemId    = parseInt(this.dataset.itemId, 10);
            currentRemaining = parseInt(this.dataset.remaining, 10);

            const product = this.dataset.product || 'Product';
            const variant = this.dataset.variant  || '';
            modalSubtitle.textContent = variant ? product + ' — ' + variant : product;

            // Reset UI
            splitContainer.innerHTML = '';
            remainingDisplay.textContent  = currentRemaining;
            allocatedDisplay.textContent  = 0;
            stillNeededDisplay.textContent = currentRemaining;
            stillNeededDisplay.className  = 'fw-bold fs-5 font-monospace text-danger';
            setError('');
            setValidation('');
            footerHint.textContent = '';
            confirmBtn.disabled    = true;
            addSplitBtn.classList.add('d-none');

            setLoading(true);
            bsModal.show();
            fetchInventory(currentItemId);
        });
    });

    // ── Fetch inventory via AJAX ───────────────────────────────────────────
    function fetchInventory(orderItemId) {
        fetch(ITEM_INVENTORY_URL + '?order_item_id=' + orderItemId, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            }
        })
        .then(function (r) {
            if (!r.ok) throw new Error('Server error (' + r.status + ')');
            return r.json();
        })
        .then(function (data) {
            inventoryData = data.inventory || [];
            setLoading(false);

            if (!inventoryData.length) {
                setError('No stock available at any location for this item.');
                return;
            }

            // Restore any previously saved allocation for this item
            const existing = allAllocations[currentItemId] || [];
            if (existing.length) {
                existing.forEach(function (a) { addSplitRow(a.inventory_id, a.quantity); });
            } else {
                addSplitRow();
            }

            addSplitBtn.classList.remove('d-none');
        })
        .catch(function (err) {
            setLoading(false);
            setError('Failed to load inventory: ' + err.message);
        });
    }

    // ── Add a split row ────────────────────────────────────────────────────
    function addSplitRow(preselectedInvId, preselectedQty) {
        const clone = splitTemplate.content.cloneNode(true);
        const row   = clone.querySelector('.split-row');

        const select    = row.querySelector('.split-location-select');
        const qtyInput  = row.querySelector('.split-qty-input');
        const hint      = row.querySelector('.location-stock-hint');
        const removeBtn = row.querySelector('.remove-split-btn');

        // Populate options
        inventoryData.forEach(function (inv) {
            const opt        = document.createElement('option');
            opt.value        = inv.id;
            opt.dataset.stock = inv.available_quantity;
            opt.dataset.label = inv.location_label;
            const icon       = inv.location_type === 'store' ? '🏪' : '🏭';
            opt.textContent  = icon + ' ' + inv.location_label + '  —  Available: ' + inv.available_quantity;
            if (inv.available_quantity <= 0) opt.disabled = true;
            select.appendChild(opt);
        });

        if (preselectedInvId) {
            select.value   = preselectedInvId;
            qtyInput.value = preselectedQty || 1;
            refreshHint(select, hint, qtyInput);
        }

        select.addEventListener('change', function () {
            refreshHint(this, hint, qtyInput);
            // Auto-suggest remaining needed, capped at stock
            if (!qtyInput.value || qtyInput.value == '0') {
                const stock  = parseInt((this.options[this.selectedIndex] || {}).dataset.stock || 0, 10);
                const needed = getRemainingNeeded();
                qtyInput.value = Math.min(needed > 0 ? needed : 1, stock);
            }
            revalidate();
        });

        qtyInput.addEventListener('input', revalidate);

        removeBtn.addEventListener('click', function () {
            if (splitContainer.querySelectorAll('.split-row').length <= 1) return;
            row.remove();
            revalidate();
        });

        splitContainer.appendChild(row);
        revalidate();
    }

    function refreshHint(select, hint, qtyInput) {
        const opt   = select.options[select.selectedIndex];
        const stock = parseInt((opt || {}).dataset.stock || 0, 10);
        if (!opt || !opt.value) { hint.textContent = ''; return; }
        hint.textContent = 'Available: ' + stock + ' units';
        hint.className   = 'small mt-1 location-stock-hint ' + (stock > 0 ? 'text-success' : 'text-danger');
        qtyInput.max     = stock;
    }

    addSplitBtn.addEventListener('click', function () { addSplitRow(); });

    // ── Running totals ─────────────────────────────────────────────────────
    function getAllocatedTotal() {
        let total = 0;
        splitContainer.querySelectorAll('.split-qty-input').forEach(function (inp) {
            total += parseInt(inp.value, 10) || 0;
        });
        return total;
    }

    function getRemainingNeeded() {
        return currentRemaining - getAllocatedTotal();
    }

    function updateCounterBar(allocated) {
        const needed = currentRemaining - allocated;
        allocatedDisplay.textContent    = allocated;
        stillNeededDisplay.textContent  = needed > 0 ? needed : 0;
        stillNeededDisplay.className    = 'fw-bold fs-5 font-monospace ' + (needed <= 0 ? 'text-success' : 'text-danger');
    }

    // ── Revalidate all rows ────────────────────────────────────────────────
    function revalidate() {
        const allocated = getAllocatedTotal();
        updateCounterBar(allocated);

        const errors = [];
        const usedIds = [];
        let totalAvailable = 0;
        // Build a map of inventory_id to available stock
        const stockMap = {};
        inventoryData.forEach(function(inv) {
            stockMap[inv.id] = parseInt(inv.available_quantity, 10) || 0;
            totalAvailable += parseInt(inv.available_quantity, 10) || 0;
        });

        // Track per-inventory allocations
        const perInventoryAllocated = {};

        splitContainer.querySelectorAll('.split-row').forEach(function (row, idx) {
            const select  = row.querySelector('.split-location-select');
            const qtyInp  = row.querySelector('.split-qty-input');
            const invId   = parseInt(select.value, 10) || null;
            const qty     = parseInt(qtyInp.value, 10) || 0;

            if (!invId) { errors.push('Row ' + (idx + 1) + ': please select a location.'); return; }
            if (qty <= 0) { errors.push('Row ' + (idx + 1) + ': quantity must be at least 1.'); return; }

            const opt   = select.options[select.selectedIndex] || {};
            const stock = parseInt(opt.dataset.stock || 0, 10);
            if (qty > stock) errors.push('Row ' + (idx + 1) + ': qty (' + qty + ') exceeds available stock (' + stock + ').');
            if (usedIds.includes(invId)) errors.push('Row ' + (idx + 1) + ': location already used — merge the quantities into one row.');
            usedIds.push(invId);

            // Track per-inventory allocation
            perInventoryAllocated[invId] = (perInventoryAllocated[invId] || 0) + qty;
            if (perInventoryAllocated[invId] > stockMap[invId]) {
                errors.push('Row ' + (idx + 1) + ': total allocated for this location (' + perInventoryAllocated[invId] + ') exceeds available stock (' + stockMap[invId] + ').');
            }
        });

        // Check total allocated does not exceed dispatch qty or total available
        if (allocated > currentRemaining) {
            errors.push('Total allocated (' + allocated + ') exceeds dispatch quantity (' + currentRemaining + ').');
        }
        if (allocated > totalAvailable) {
            errors.push('Total allocated (' + allocated + ') exceeds total available stock (' + totalAvailable + ').');
        }

        if (errors.length) { setValidation(errors.join('<br>'), 'danger'); confirmBtn.disabled = true; return; }
        if (allocated <= 0) { setValidation('Enter at least 1 unit to dispatch.', 'warning'); confirmBtn.disabled = true; return; }

        if (allocated < currentRemaining) {
            setValidation(
                '<i class="bi bi-info-circle me-1"></i> Partial dispatch: <strong>' + allocated +
                '</strong> of <strong>' + currentRemaining + '</strong> will be dispatched. The rest can be dispatched later.',
                'warning'
            );
            footerHint.textContent = 'Partial — ' + (currentRemaining - allocated) + ' remaining after this.';
        } else {
            setValidation('', '');
            footerHint.textContent = '';
        }

        confirmBtn.disabled = false;
    }

    // ── Confirm → save allocation, close modal ─────────────────────────────
    confirmBtn.addEventListener('click', function () {
        const splits = [];
        let valid    = true;

        splitContainer.querySelectorAll('.split-row').forEach(function (row) {
            const select = row.querySelector('.split-location-select');
            const qty    = parseInt(row.querySelector('.split-qty-input').value, 10) || 0;
            const invId  = parseInt(select.value, 10) || null;
            const label  = (select.options[select.selectedIndex] || {}).dataset.label || '—';

            if (!invId || qty <= 0) { valid = false; return; }
            splits.push({ inventory_id: invId, quantity: qty, locationLabel: label });
        });

        if (!valid || !splits.length) { setValidation('Please fix the errors before confirming.', 'danger'); return; }

        allAllocations[currentItemId] = splits;
        bsModal.hide();
        renderSummary(currentItemId, splits);
        rebuildHiddenInputs();
        updateSubmitBtn();
    });

    // ── Render allocation summary in the table row ─────────────────────────
    function renderSummary(itemId, splits) {
        const el = document.getElementById('allocation-summary-' + itemId);
        if (!el) return;

        const total = splits.reduce(function (s, x) { return s + x.quantity; }, 0);
        let html = '<div class="d-flex flex-column gap-1">';
        splits.forEach(function (s) {
            html += '<div class="d-flex align-items-center gap-2">'
                + '<span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 font-monospace">'
                + s.quantity + '×</span>'
                + '<span class="small text-dark">' + escHtml(s.locationLabel) + '</span></div>';
        });
        html += '<div class="small text-muted mt-1">Total: <strong class="text-dark">' + total + '</strong></div></div>';
        el.innerHTML = html;

        // Update the trigger button to "Edit"
        const triggerBtn = document.querySelector('.open-dispatch-modal[data-item-id="' + itemId + '"]');
        if (triggerBtn) {
            triggerBtn.innerHTML = '<i class="bi bi-pencil-square"></i> Edit';
            triggerBtn.classList.replace('btn-outline-primary', 'btn-outline-secondary');
        }
    }

    // ── Rebuild hidden inputs from allAllocations ──────────────────────────
    function rebuildHiddenInputs() {
        hiddenWrap.innerHTML = '';
        let i = 0;
        Object.keys(allAllocations).forEach(function (itemId) {
            (allAllocations[itemId] || []).forEach(function (split) {
                hiddenWrap.innerHTML +=
                    '<input type="hidden" name="dispatches[' + i + '][order_item_id]" value="' + itemId + '">' +
                    '<input type="hidden" name="dispatches[' + i + '][inventory_id]" value="' + split.inventory_id + '">' +
                    '<input type="hidden" name="dispatches[' + i + '][quantity]" value="' + split.quantity + '">';
                i++;
            });
        });
    }

    // ── Enable/disable main submit button ──────────────────────────────────
    function updateSubmitBtn() {
        const count = Object.keys(allAllocations).filter(function (id) {
            return allAllocations[id] && allAllocations[id].length > 0;
        }).length;

        submitBtn.disabled = count === 0;
        statusMsg.innerHTML = count > 0
            ? '<span class="text-success"><i class="bi bi-check-circle me-1"></i>' +
              count + ' item' + (count !== 1 ? 's' : '') + ' allocated — ready to dispatch.</span>'
            : 'Allocate at least one item to create a dispatch.';
    }

    // Sync method/remarks into hidden inputs on actual form submit
    document.getElementById('dispatchForm').addEventListener('submit', function () {
        document.getElementById('form_dispatch_method').value =
            document.getElementById('dispatch_method_select').value;
        document.getElementById('form_remarks').value =
            document.getElementById('remarks_textarea').value;
        rebuildHiddenInputs();
    });

    // ── Helpers ────────────────────────────────────────────────────────────
    function setLoading(on) {
        loadingEl.classList.toggle('d-none', !on);
        splitContainer.classList.toggle('d-none', on);
        addSplitBtn.classList.toggle('d-none', true); // always hide until loaded
    }

    function setError(msg) {
        errorEl.classList.toggle('d-none', !msg);
        errorEl.textContent = msg || '';
    }

    function setValidation(msg, type) {
        if (!msg) { validationMsg.classList.add('d-none'); validationMsg.innerHTML = ''; return; }
        validationMsg.className = 'alert alert-' + (type || 'warning') + ' mt-3 small py-2';
        validationMsg.innerHTML = msg;
    }

    function escHtml(str) {
        const d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }
})();

// Move Modal to Body on Load
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('dispatchModal');
    if (modal) {
        document.body.appendChild(modal);
    }
});
