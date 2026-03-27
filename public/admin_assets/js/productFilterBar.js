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

    // ── Update badge count + chips row ───────────────────────────
    function updateFilterUI() {
        const active  = getActiveFilters();
        const badge   = el("filter-active-badge");
        const chips   = el("active-filter-chips");
        const counter = el("filter-result-count");

        // Badge on toggle button
        if (active.length > 0) {
            badge.textContent = active.length;
            badge.classList.remove("d-none");
        } else {
            badge.classList.add("d-none");
        }

        // Chips row
        chips.innerHTML = "";
        if (active.length > 0) {
            chips.classList.remove("d-none");
            counter.classList.remove("d-none");

            active.forEach((f) => {
                const chip = document.createElement("span");
                chip.className =
                    "badge bg-primary-subtle text-primary border border-primary-subtle d-inline-flex align-items-center gap-1 px-2 py-1";
                chip.style.fontSize = "0.75rem";
                chip.innerHTML = `${f.label} <i class="fas fa-times ms-1" style="cursor:pointer" data-key="${f.key}"></i>`;
                chips.appendChild(chip);
            });

            // Dismiss individual chip
            chips.querySelectorAll("[data-key]").forEach((icon) => {
                icon.addEventListener("click", function () {
                    clearFilter(this.dataset.key);
                    updateFilterUI();
                    if (window.productsTable)
                        window.productsTable.ajax.reload();
                });
            });
        } else {
            chips.classList.add("d-none");
            counter.classList.add("d-none");
        }
    }

    // ── Auto-open panel if filters present on page load ──────────
    if (getActiveFilters().length > 0) {
        const panel = el("filter-panel");
        if (panel) new bootstrap.Collapse(panel, { toggle: false }).show();
        updateFilterUI();
    }

    // ── Live update badge/chips on any input change ───────────────
    document.querySelectorAll(".filter-input").forEach((input) => {
        input.addEventListener("change", updateFilterUI);
        input.addEventListener("input",  updateFilterUI);
    });

    // ── Form submit → prevent reload, reload DataTable ───────────
    el("product-filter-bar")?.addEventListener("submit", function (e) {
        e.preventDefault();
        updateFilterUI();
        if (window.productsTable) window.productsTable.ajax.reload();
    });

    // ── Reset button ─────────────────────────────────────────────
    el("reset-filter-btn")?.addEventListener("click", function () {
        el("product-filter-bar").reset();
        updateFilterUI();
        if (window.productsTable) window.productsTable.ajax.reload();
    });
});
