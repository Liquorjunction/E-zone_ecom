document.addEventListener("DOMContentLoaded", function () {
    function el(id) {
        return document.getElementById(id);
    }

    var filterPanel = el("filter-panel");
    var filterForm = el("privacy-policy-filter-bar");
    var applyBtn = el("apply-filters-btn");
    var resetBtn = el("reset-filters-btn");

    function getSelectedStatus() {
        return document.querySelector("input[name='status']:checked")?.value || "all";
    }

    function hasActiveFilters() {
        var dateFrom = filterForm?.querySelector("input[name='date_from']")?.value;
        var dateTo = filterForm?.querySelector("input[name='date_to']")?.value;
        return getSelectedStatus() !== "all" || !!dateFrom || !!dateTo;
    }

    function reloadTable() {
        if (window.privacyPolicyDataTable) {
            window.privacyPolicyDataTable.ajax.reload();
        }
    }

    if (filterPanel && hasActiveFilters()) {
        new bootstrap.Collapse(filterPanel, { toggle: false }).show();
    }

    if (applyBtn) {
        applyBtn.addEventListener("click", function (e) {
            if (window.privacyPolicyDataTable) {
                e.preventDefault();
                reloadTable();
            }
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener("click", function () {
            var statusAll = el("status_all");
            var dateFrom = filterForm?.querySelector("input[name='date_from']");
            var dateTo = filterForm?.querySelector("input[name='date_to']");

            if (statusAll) {
                statusAll.checked = true;
            }
            if (dateFrom) {
                dateFrom.value = "";
            }
            if (dateTo) {
                dateTo.value = "";
            }

            if (window.privacyPolicyDataTable) {
                reloadTable();
                return;
            }

            window.location.href = window.location.pathname;
        });
    }
});
