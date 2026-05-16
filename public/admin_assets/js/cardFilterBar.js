document.addEventListener("DOMContentLoaded", function () {
    function el(id) {
        return document.getElementById(id);
    }

    var filterPanel = el("filter-panel");
    var filterForm = el("card-filter-bar");
    var resetBtn = el("reset-filters-btn");

    function getSelectedStatus() {
        return document.querySelector("input[name='status']:checked")?.value || "all";
    }

    function hasActiveFilters() {
        return getSelectedStatus() !== "all";
    }

    function reloadTable() {
        if (window.cardsDataTable) {
            window.cardsDataTable.ajax.reload();
        }
    }

    if (filterPanel && hasActiveFilters()) {
        new bootstrap.Collapse(filterPanel, { toggle: false }).show();
    }

    if (filterForm) {
        filterForm.addEventListener("submit", function (e) {
            if (window.cardsDataTable) {
                e.preventDefault();
                reloadTable();
            }
        });

        filterForm.addEventListener("keydown", function (e) {
            if (e.key === "Enter" && window.cardsDataTable) {
                e.preventDefault();
                reloadTable();
            }
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener("click", function () {
            var statusAll = el("status_all");
            if (statusAll) {
                statusAll.checked = true;
            }

            if (window.cardsDataTable) {
                reloadTable();
                return;
            }

            window.location.href = window.location.pathname;
        });
    }
});
