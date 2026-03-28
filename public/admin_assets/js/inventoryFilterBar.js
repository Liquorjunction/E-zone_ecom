document.addEventListener("DOMContentLoaded", () => {
    // ── Helpers ───────────────────────────────────────────────────
    function el(id) { return document.getElementById(id); }

    // ── Populate & show/hide location_id based on location_type ──
    function populateLocationDropdown(type, selectedId = '') {
        const wrapper = el('location-id-wrapper');
        const divider = el('location-id-divider');
        const locationSelect = el('location_id');

        if (!type) {
            // Hide child dropdown entirely
            wrapper.classList.add('d-none');
            divider.classList.add('d-none');
            locationSelect.innerHTML = '<option value="">All</option>';
            return;
        }

        const list = type === 'warehouse'
            ? (window.warehouseList || [])
            : (window.storeList || []);

        locationSelect.innerHTML = '<option value="">All</option>';
        list.forEach(item => {
            const opt = document.createElement('option');
            opt.value       = item.id;
            opt.textContent = item.name;
            if (String(item.id) === String(selectedId)) opt.selected = true;
            locationSelect.appendChild(opt);
        });

        // Show child dropdown
        wrapper.classList.remove('d-none');
        divider.classList.remove('d-none');
    }

    // ── On location_type change → repopulate child + reload ───────
    el('location_type')?.addEventListener('change', function () {
        populateLocationDropdown(this.value);
        if (window.inventoryTable) window.inventoryTable.ajax.reload();
    });

    // ── Init on page load (handles back-button / pre-filled state) ─
    const initType = el('location_type')?.value;
    const initId   = el('location_id')?.value || '';
    populateLocationDropdown(initType, initId);

    // ── Read active filters ───────────────────────────────────────
    function getActiveFilters() {
        const active = [];

        if (el('exclude_sold')?.checked)
            active.push({ key: 'exclude_sold', label: 'Excluding Sold' });

        const locType = el('location_type')?.value;
        const locId   = el('location_id')?.value;
        const locName = locId
            ? el('location_id')?.options[el('location_id')?.selectedIndex]?.text
            : '';

        if (locType === 'warehouse') active.push({ key: 'location_type', label: 'Warehouse' });
        if (locType === 'store')     active.push({ key: 'location_type', label: 'Store' });
        if (locId)                   active.push({ key: 'location_id',   label: `Location: ${locName}` });

        const dateFrom = el('date_from')?.value;
        const dateTo   = el('date_to')?.value;
        if (dateFrom) active.push({ key: 'date_from', label: `From: ${dateFrom}` });
        if (dateTo)   active.push({ key: 'date_to',   label: `To: ${dateTo}` });

        return active;
    }

    // ── Clear a filter by key ─────────────────────────────────────
    function clearFilter(key) {
        switch (key) {
            case 'exclude_sold':
                el('exclude_sold').checked = false;
                break;
            case 'location_type':
                el('location_type').value = '';
                populateLocationDropdown(''); // hides child too
                break;
            case 'location_id':
                el('location_id').value = '';
                break;
            case 'date_from':
                el('date_from').value = '';
                break;
            case 'date_to':
                el('date_to').value = '';
                break;
        }
    }

    // ── Auto-open panel if filters active on load ─────────────────
    if (getActiveFilters().length > 0) {
        const panel = el('filter-panel');
        if (panel) new bootstrap.Collapse(panel, { toggle: false }).show();
        // (removed: updateFilterUI)
    }

    // ── Live update on any filter input change ────────────────────
    // (removed: updateFilterUI on filter-input change/input)

    // ── Form submit ───────────────────────────────────────────────
    el('inventory-filter-bar')?.addEventListener('submit', function (e) {
        e.preventDefault();
        if (window.inventoryTable) window.inventoryTable.ajax.reload();
    });

    // ── Reset ─────────────────────────────────────────────────────
    el('reset-filter-btn')?.addEventListener('click', function () {
        el('inventory-filter-bar').reset();
        populateLocationDropdown(''); // clears + hides child dropdown
        if (window.inventoryTable) window.inventoryTable.ajax.reload();
    });
});
