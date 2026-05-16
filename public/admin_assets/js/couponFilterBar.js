document.addEventListener('DOMContentLoaded', function () {
    const filterPanel = document.getElementById('filter-panel');
    const applyBtn = document.getElementById('apply-filters-btn');
    const resetBtn = document.getElementById('reset-filters-btn');
    const filterForm = document.getElementById('coupon-filter-bar');
    const couponTypeSelect = document.getElementById('coupon_type');

    function loadCouponTypes() {
        if (!couponTypeSelect || couponTypeSelect.dataset.loaded === '1') {
            return;
        }

        const url = couponTypeSelect.dataset.listUrl;
        if (!url) {
            return;
        }

        couponTypeSelect.dataset.loaded = 'loading';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Failed to load coupon types');
                }

                return response.json();
            })
            .then(function (payload) {
                const types = Array.isArray(payload.data) ? payload.data : [];
                const currentValue = couponTypeSelect.value || '';

                couponTypeSelect.innerHTML = '<option value="">All Types</option>';

                types.forEach(function (type) {
                    const option = document.createElement('option');
                    option.value = type;
                    option.textContent = String(type).replace(/\b\w/g, function (char) {
                        return char.toUpperCase();
                    });
                    couponTypeSelect.appendChild(option);
                });

                if (currentValue) {
                    couponTypeSelect.value = currentValue;
                }

                couponTypeSelect.dataset.loaded = '1';
            })
            .catch(function () {
                couponTypeSelect.dataset.loaded = '0';
            });
    }

    function hasActiveFilters() {
        const status = document.querySelector('input[name="status"]:checked')?.value || 'all';
        const dateFrom = document.getElementById('date_from')?.value || '';
        const dateTo = document.getElementById('date_to')?.value || '';
        const usageSort = document.getElementById('sort_by_usage_count')?.value || '';
        const couponType = document.getElementById('coupon_type')?.value || '';

        return !!(status !== 'all' || dateFrom || dateTo || usageSort || couponType);
    }

    function reloadTable() {
        if (window.couponsTable) {
            window.couponsTable.ajax.reload();
        }
    }

    if (filterPanel && typeof bootstrap !== 'undefined' && hasActiveFilters()) {
        new bootstrap.Collapse(filterPanel, { toggle: false }).show();
    }

    if (couponTypeSelect) {
        couponTypeSelect.addEventListener('focus', loadCouponTypes);
        couponTypeSelect.addEventListener('mousedown', loadCouponTypes);
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

            const allStatusRadio = document.getElementById('status_all');
            if (allStatusRadio) {
                allStatusRadio.checked = true;
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
});
