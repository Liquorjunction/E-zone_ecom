document.addEventListener('DOMContentLoaded', function () {
    function el(id) {
        return document.getElementById(id);
    }

    function reloadTable() {
        if (window.dispatchTable) {
            window.dispatchTable.ajax.reload();
        }
    }

    function appendOption(selectElement, value, label) {
        selectElement.append(new Option(label, value));
    }

    function setSelected(selectElement) {
        var selected = selectElement.dataset.selected || '';
        if (selected) {
            selectElement.value = selected;
        }
    }

    function loadStatuses() {
        var statusSelect = el('filter_status');
        var url = statusSelect ? statusSelect.dataset.statusesUrl : '';

        if (!statusSelect || !url) {
            return;
        }

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (response) {
                var statuses = response && response.statuses ? response.statuses : [];

                statusSelect.innerHTML = '';
                appendOption(statusSelect, '', 'All Statuses');

                statuses.forEach(function (status) {
                    if (typeof status !== 'string' || !status) {
                        return;
                    }

                    var label = status.replace(/_/g, ' ').replace(/\b\w/g, function (c) {
                        return c.toUpperCase();
                    });

                    appendOption(statusSelect, status, label);
                });

                setSelected(statusSelect);
            })
            .catch(function () {
                statusSelect.innerHTML = '';
                appendOption(statusSelect, '', 'All Statuses');
            });
    }

    function resetFilters() {
        var statusSelect = el('filter_status');
        var orderNumber = el('filter_order_number');

        if (statusSelect) {
            statusSelect.value = '';
        }

        if (orderNumber) {
            orderNumber.value = '';
        }

        reloadTable();
    }

    function openFiltersIfNeeded() {
        var panel = el('filter-panel');
        var statusSelect = el('filter_status');
        var orderNumber = el('filter_order_number');

        if (!panel || typeof bootstrap === 'undefined') {
            return;
        }

        var hasStatus = statusSelect && statusSelect.value;
        var hasOrderNumber = orderNumber && orderNumber.value && orderNumber.value.trim() !== '';

        if (!hasStatus && !hasOrderNumber) {
            return;
        }

        new bootstrap.Collapse(panel, { toggle: false }).show();
    }

    el('apply-filters-btn')?.addEventListener('click', function () {
        reloadTable();
    });

    el('reset-filters-btn')?.addEventListener('click', function (event) {
        event.preventDefault();
        resetFilters();
    });

    el('filter_status')?.addEventListener('click', loadStatuses);
    el('filter_status')?.addEventListener('focus', loadStatuses);

    openFiltersIfNeeded();
});
