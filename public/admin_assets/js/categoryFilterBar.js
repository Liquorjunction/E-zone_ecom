document.addEventListener("DOMContentLoaded", function () {
    // ── Helpers ───────────────────────────────────────────────────
    function el(id) { return document.getElementById(id); }

    // ── Read active filters ───────────────────────────────────────
    function getActiveFilters() {
        const active = [];
        const status = document.querySelector("input[name='status']:checked")?.value;
        if (status === "1") active.push({ key: "status", label: "Active" });
        if (status === "0") active.push({ key: "status", label: "Inactive" });
        return active;
    }

    // ── Clear a filter by key ─────────────────────────────────────
    function clearFilter(key) {
        if (key === "status") {
            const allRadio = document.querySelector("input[name='status'][value='']");
            if (allRadio) allRadio.checked = true;
        }
    }

    // ── Auto-open panel if filters active on load ─────────────────
    if (getActiveFilters().length > 0) {
        const panel = el("filter-panel");
        if (panel) new bootstrap.Collapse(panel, { toggle: false }).show();
    }

    // ── Form submit → prevent reload, reload DataTable ───────────
    const form = el("category-filter-bar");
    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            if (window.categoryTable) window.categoryTable.ajax.reload();
        });
    }

    // ── Reset button ─────────────────────────────────────────────
    const resetBtn = el("reset-filter-btn");
    if (resetBtn) {
        resetBtn.addEventListener("click", function () {
            clearFilter("status");
            if (window.categoryTable) window.categoryTable.ajax.reload();
        });
    }
});
