document.addEventListener("DOMContentLoaded", () => {
    function el(id) { return document.getElementById(id); }

    function getActiveFilters() {
        const active = [];
        const status = document.querySelector("input[name='status']:checked")?.value;
        if (status === "1") active.push({ key: "status", label: "Active" });
        if (status === "0") active.push({ key: "status", label: "Inactive" });
        return active;
    }

    function clearFilter(key) {
        if (key === "status") {
            const allRadio = document.querySelector("input[name='status'][value='']");
            if (allRadio) allRadio.checked = true;
        }
    }

    if (getActiveFilters().length > 0) {
        const panel = el("filter-panel");
        if (panel) new bootstrap.Collapse(panel, { toggle: false }).show();
    }

    var filterForm = el("brand-filter-bar");
    if (filterForm) {
        filterForm.addEventListener("submit", function (e) {
            e.preventDefault();
            if (window.storeTable) window.storeTable.ajax.reload();
        });
    }

    var resetBtn = el("reset-filter-btn");
    if (resetBtn) {
        resetBtn.addEventListener("click", function () {
            clearFilter("status");
            if (window.storeTable) window.storeTable.ajax.reload();
        });
    }
});
