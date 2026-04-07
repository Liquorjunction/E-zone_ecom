document.addEventListener("DOMContentLoaded", function () {
    var filterPanel = document.getElementById("filter-panel");
    var filterForm = document.getElementById("customer-filter-bar");
    var clearBtn = document.getElementById("reset-filters");

    function hasActiveFilters() {
        var status = filterForm?.querySelector('input[name="status"]:checked')?.value;
        var dateFrom = filterForm?.querySelector('input[name="start_date"]')?.value;
        var dateTo = filterForm?.querySelector('input[name="end_date"]')?.value;
        var emailVerified = filterForm?.querySelector('input[name="email_verified"]')?.checked;

        return !!(status && status !== "all") || !!dateFrom || !!dateTo || !!emailVerified;
    }

    function reloadTable() {
        if (window.customersDataTable) {
            window.customersDataTable.ajax.reload();
        }
    }

    if (filterPanel && hasActiveFilters()) {
        new bootstrap.Collapse(filterPanel, { toggle: false }).show();
    }

    if (clearBtn) {
        clearBtn.addEventListener("click", function () {
            var statusAll = document.getElementById("status_all");
            var dateFromFilter = filterForm?.querySelector('input[name="start_date"]');
            var dateToFilter = filterForm?.querySelector('input[name="end_date"]');
            var emailVerified = document.getElementById("email_verified");

            if (statusAll) statusAll.checked = true;
            if (dateFromFilter) dateFromFilter.value = "";
            if (dateToFilter) dateToFilter.value = "";
            if (emailVerified) emailVerified.checked = false;

            if (window.customersDataTable) {
                reloadTable();
                return;
            }

            window.location.href = window.location.pathname;
        });
    }

    if (filterForm) {
        filterForm.addEventListener("submit", function (e) {
            if (window.customersDataTable) {
                e.preventDefault();
                reloadTable();
            }
        });

        filterForm.addEventListener("keydown", function (e) {
            if (e.key === "Enter") {
                if (window.customersDataTable) {
                    e.preventDefault();
                    reloadTable();
                }
            }
        });
    }
});
