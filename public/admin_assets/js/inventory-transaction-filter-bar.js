document.addEventListener('DOMContentLoaded', function () {
    function el(id) {
        return document.getElementById(id);
    }

    function reloadTable() {
        if (window.inventoryTransactionsTable) {
            window.inventoryTransactionsTable.ajax.reload();
        }
    }

    function applyFilters() {
        reloadTable();
    }

    function resetFilters() {
        const form = el('filter-form');

        if (!form) {
            return;
        }

        form.reset();

        const typeSelect = el('filter-type');
        if (typeSelect) {
            const selected = typeSelect.dataset.selected || '';
            typeSelect.value = selected;
        }

        reloadTable();
    }

    el('filter-form')?.addEventListener('submit', function (event) {
        event.preventDefault();
    });

    el('apply-filters-btn')?.addEventListener('click', function () {
        applyFilters();
    });

    el('reset-filters-btn')?.addEventListener('click', function () {
        resetFilters();
    });

});
