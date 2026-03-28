document.addEventListener("DOMContentLoaded", () => {
    // Move modal to body
    const modal = document.getElementById("importModal");
    if (modal && modal.parentElement !== document.body) {
        document.body.appendChild(modal);
    }

    // ── Helpers ───────────────────────────────────────────────────
    function el(id) {
        return document.getElementById(id);
    }
    function qs(selector) {
        return document.querySelector(selector);
    }

    // ── Read all active filters ───────────────────────────────────
    // Keys must match exactly what the repo checks: isset($request['...'])
    function getActiveFilters() {
        const active = [];

        const search = el("search")?.value.trim();
        if (search)
            active.push({ key: "search", label: `Search: "${search}"` });

        if (el("bestseller")?.checked)
            active.push({ key: "best_seller", label: "Bestseller only" });

        if (qs("input[name='in_stock']")?.checked)
            active.push({ key: "in_stock", label: "In Stock" });

        if (qs("input[name='out_of_stock']")?.checked)
            active.push({ key: "out_of_stock", label: "Out of Stock" });

        // Status: Active/Inactive (checkboxes)
        const statusActive = qs("input[name='status'][value='active']")?.checked;
        const statusInactive = qs("input[name='status'][value='inactive']")?.checked;
        if (statusActive)
            active.push({ key: "status_active", label: "Active" });
        if (statusInactive)
            active.push({ key: "status_inactive", label: "Inactive" });

        const priceMin = qs("input[name='price_min']")?.value;
        const priceMax = qs("input[name='price_max']")?.value;
        if (priceMin)
            active.push({ key: "price_min", label: `Min: ${priceMin}` });
        if (priceMax)
            active.push({ key: "price_max", label: `Max: ${priceMax}` });

        const dateFrom = qs("input[name='date_from']")?.value;
        const dateTo   = qs("input[name='date_to']")?.value;
        if (dateFrom)
            active.push({ key: "date_from", label: `From: ${dateFrom}` });
        if (dateTo)
            active.push({ key: "date_to", label: `To: ${dateTo}` });

        return active;
    }

    // ── Clear a single filter by key ─────────────────────────────
    function clearFilter(key) {
        switch (key) {
            case "search":
                el("search").value = "";
                break;
            case "best_seller":
                el("bestseller").checked = false;
                break;
            case "in_stock":
                qs("input[name='in_stock']").checked = false;
                break;
            case "out_of_stock":
                qs("input[name='out_of_stock']").checked = false;
                break;
            case "status_active":
                qs("input[name='status'][value='active']").checked = false;
                break;
            case "status_inactive":
                qs("input[name='status'][value='inactive']").checked = false;
                break;
            case "price_min":
                qs("input[name='price_min']").value = "";
                break;
            case "price_max":
                qs("input[name='price_max']").value = "";
                break;
            case "date_from":
                qs("input[name='date_from']").value = "";
                break;
            case "date_to":
                qs("input[name='date_to']").value = "";
                break;
        }
    }

    // (updateFilterUI and filter chip UI removed)

    // ── Auto-open panel if filters present on page load ──────────
    if (getActiveFilters().length > 0) {
        const panel = el("filter-panel");
        if (panel) new bootstrap.Collapse(panel, { toggle: false }).show();
        // (removed: updateFilterUI)
    }

    // ── Live update badge/chips on any input change ───────────────
    // (removed: updateFilterUI on filter-input change/input)

    // ── Form submit → prevent reload, reload DataTable ───────────
    el("product-filter-bar")?.addEventListener("submit", function (e) {
        e.preventDefault();
        if (window.productsTable) window.productsTable.ajax.reload();
    });

    // ── Reset button ─────────────────────────────────────────────
    el("reset-filter-btn")?.addEventListener("click", function () {
        el("product-filter-bar").reset();
        if (window.productsTable) window.productsTable.ajax.reload();
    });
});
