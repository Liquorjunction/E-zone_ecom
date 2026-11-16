document.addEventListener('DOMContentLoaded', function () {
    function el(id) {
        return document.getElementById(id);
    }

    function reloadTable() {
        if (window.ordersTable) {
            window.ordersTable.ajax.reload();
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

    function populateStatusOptions() {
        var statusSelect = el('status');
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

                if (Array.isArray(statuses)) {
                    statuses.forEach(function (status) {
                        if (typeof status === 'string') {
                            var label = status.replace(/_/g, ' ').replace(/\b\w/g, function (c) { return c.toUpperCase(); });
                            appendOption(statusSelect, status, label);
                            return;
                        }

                        var value = status.value || status.id || status.key || status.name || '';
                        if (!value) {
                            return;
                        }

                        var text = status.label || status.name || String(value);
                        appendOption(statusSelect, String(value), text);
                    });
                } else if (statuses && typeof statuses === 'object') {
                    Object.keys(statuses).forEach(function (key) {
                        appendOption(statusSelect, key, statuses[key]);
                    });
                }

                setSelected(statusSelect);
            })
            .catch(function () {
                statusSelect.innerHTML = '';
                appendOption(statusSelect, '', 'All Statuses');
            });
    }

    function populatePaymentMethodsOptions() {
        var paymentMethodSelect = el('payment_method');
        var url = paymentMethodSelect ? paymentMethodSelect.dataset.methodsUrl : '';

        if (!paymentMethodSelect || !url) {
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
                var methods = response && response.payment_methods ? response.payment_methods : {};

                paymentMethodSelect.innerHTML = '';
                appendOption(paymentMethodSelect, '', 'All Methods');

                if (methods && typeof methods === 'object') {
                    Object.keys(methods).forEach(function (key) {
                        appendOption(paymentMethodSelect, key, methods[key]);
                    });
                }

                setSelected(paymentMethodSelect);
            })
            .catch(function () {
                paymentMethodSelect.innerHTML = '';
                appendOption(paymentMethodSelect, '', 'All Methods');
            });
    }

    function populatePaymentStatusOptions() {
        var paymentStatusSelect = el('payment_status');
        if (!paymentStatusSelect) {
            return;
        }

        paymentStatusSelect.innerHTML = '';
        appendOption(paymentStatusSelect, '', 'All Statuses');
        appendOption(paymentStatusSelect, 'pending', 'Pending');
        appendOption(paymentStatusSelect, 'paid', 'Paid');
        appendOption(paymentStatusSelect, 'cancelled', 'Cancelled');

        setSelected(paymentStatusSelect);
    }

    function resetFilters() {
        var statusSelect = el('status');
        var paymentMethodSelect = el('payment_method');
        var paymentStatusSelect = el('payment_status');
        var startDate = document.querySelector("input[name='start_date']");
        var endDate = document.querySelector("input[name='end_date']");

        if (statusSelect) {
            statusSelect.value = '';
        }
        if (paymentMethodSelect) {
            paymentMethodSelect.value = '';
        }
        if (paymentStatusSelect) {
            paymentStatusSelect.value = '';
        }
        if (startDate) {
            startDate.value = '';
        }
        if (endDate) {
            endDate.value = '';
        }

        reloadTable();
    }

    el('apply-filters-btn')?.addEventListener('click', function () {
        reloadTable();
    });

    el('reset-filters-btn')?.addEventListener('click', function () {
        resetFilters();
    });

    populateStatusOptions();
    populatePaymentMethodsOptions();
    populatePaymentStatusOptions();
});
