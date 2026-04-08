document.addEventListener('DOMContentLoaded', function () {
    const filterPanel = document.getElementById('filter-panel');
    const filterForm = document.getElementById('discount-filter-bar');
    const applyBtn = document.getElementById('apply-filters-btn');
    const resetBtn = document.getElementById('reset-filters-btn');
    const discountTypeSelect = document.getElementById('discount_type');

    function reloadTable() {
        if (window.discountsTable) {
            window.discountsTable.ajax.reload();
        }
    }

    function hasActiveFilters() {
        const status = document.querySelector('input[name="status"]:checked')?.value || 'all';
        const validity = document.querySelector('input[name="validity"]:checked')?.value || 'all';
        const type = discountTypeSelect?.value || '';
        const dateFrom = document.getElementById('date_from')?.value || '';
        const dateTo = document.getElementById('date_to')?.value || '';

        return !!(status !== 'all' || validity !== 'all' || type || dateFrom || dateTo);
    }

    function loadDiscountTypes() {
        if (!discountTypeSelect || discountTypeSelect.dataset.loaded === '1') {
            return;
        }

        const url = discountTypeSelect.dataset.listUrl;
        if (!url) {
            return;
        }

        discountTypeSelect.dataset.loaded = 'loading';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Failed to fetch discount types');
                }
                return response.json();
            })
            .then(function (payload) {
                const types = Array.isArray(payload.data) ? payload.data : [];
                const selected = discountTypeSelect.value || '';

                discountTypeSelect.innerHTML = '<option value="">All</option>';

                types.forEach(function (type) {
                    const option = document.createElement('option');
                    option.value = type;
                    option.textContent = String(type).replace(/\b\w/g, function (char) {
                        return char.toUpperCase();
                    });
                    discountTypeSelect.appendChild(option);
                });

                if (selected) {
                    discountTypeSelect.value = selected;
                }

                discountTypeSelect.dataset.loaded = '1';
            })
            .catch(function () {
                discountTypeSelect.dataset.loaded = '0';
            });
    }

    if (discountTypeSelect) {
        discountTypeSelect.addEventListener('focus', loadDiscountTypes);
        discountTypeSelect.addEventListener('mousedown', loadDiscountTypes);
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            reloadTable();
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (filterForm) {
                filterForm.reset();
            }

            const statusAll = document.getElementById('status_all');
            const validityAll = document.getElementById('validity_all');
            if (statusAll) {
                statusAll.checked = true;
            }
            if (validityAll) {
                validityAll.checked = true;
            }
            if (discountTypeSelect) {
                discountTypeSelect.value = '';
            }

            reloadTable();
        });
    }

    if (filterForm) {
        filterForm.addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                reloadTable();
            }
        });
    }

    if (filterPanel && typeof bootstrap !== 'undefined' && hasActiveFilters()) {
        new bootstrap.Collapse(filterPanel, { toggle: false }).show();
    }
});
