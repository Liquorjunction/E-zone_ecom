document.addEventListener("DOMContentLoaded", function () {
    var filterPanel = document.getElementById("filter-panel");
    var applyBtn = document.getElementById("apply-filters");
    var clearBtn = document.getElementById("clear-filters");
    var filterForm = document.getElementById("filter-form");

    function hasActiveFilters() {
        var status = document.getElementById("status-filter")?.value;
        var dateFrom = document.getElementById("date-from-filter")?.value;
        var dateTo = document.getElementById("date-to-filter")?.value;

        return !!(status || dateFrom || dateTo);
    }

    function reloadTable() {
        if (window.customersDataTable) {
            window.customersDataTable.ajax.reload();
        }
    }

    if (filterPanel && hasActiveFilters()) {
        new bootstrap.Collapse(filterPanel, { toggle: false }).show();
    }

    if (applyBtn) {
        applyBtn.addEventListener("click", function () {
            reloadTable();
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener("click", function () {
            var statusFilter = document.getElementById("status-filter");
            var dateFromFilter = document.getElementById("date-from-filter");
            var dateToFilter = document.getElementById("date-to-filter");

            if (statusFilter) statusFilter.value = "";
            if (dateFromFilter) dateFromFilter.value = "";
            if (dateToFilter) dateToFilter.value = "";

            reloadTable();
        });
    }

    if (filterForm) {
        filterForm.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                reloadTable();
            }
        });
    }
});
