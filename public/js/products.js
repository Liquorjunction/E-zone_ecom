// Client products page: AJAX filters, search, sort, view toggle
// ─── FIX: "Clear All" used a hard navigation <a> tag, which bypassed JS state.
//          We now handle it in JS so we can properly reset price sliders too.

// ─────────────────────────────────────────────
// FORM HELPERS
// ─────────────────────────────────────────────

const getFilterForm = () => {
    const modal = document.getElementById("mobile-filter-modal");
    const modalForm = modal
        ? modal.querySelector("#filter-form")
        : null;
    const sidebarForm = document.querySelector("aside #filter-form");
    return modalForm && !modal.classList.contains("hidden")
        ? modalForm
        : sidebarForm || document.getElementById("filter-form");
};

const getFilterForms = () =>
    Array.from(document.querySelectorAll("#filter-form"));

// ─── FIX: syncFormsData also re-initialises the price slider visuals in the
//          target form after copying values, so the track highlight stays correct.
const syncFormsData = (sourceForm = getFilterForm()) => {
    const forms = getFilterForms();
    if (forms.length <= 1 || !sourceForm) return;

    const formData = new FormData(sourceForm);

    forms.forEach((form) => {
        if (form === sourceForm) return;

        // Clear checkboxes / radios
        form
            .querySelectorAll('input[type="checkbox"], input[type="radio"]')
            .forEach((input) => (input.checked = false));

        // Clear text / number inputs
        form
            .querySelectorAll('input[type="text"], input[type="number"]')
            .forEach((input) => (input.value = ""));

        // Copy values from source form
        for (const [key, value] of formData.entries()) {
            const inputs = form.querySelectorAll(`[name="${key}"]`);
            inputs.forEach((input) => {
                if (input.type === "checkbox" || input.type === "radio") {
                    if (input.value === value) input.checked = true;
                } else {
                    input.value = value;
                }
            });
        }

        // Sync price-range dirty flag
        const sourcePriceRange = sourceForm.querySelector("[data-price-range]");
        const targetPriceRange = form.querySelector("[data-price-range]");
        if (sourcePriceRange && targetPriceRange) {
            targetPriceRange.dataset.priceDirty =
                sourcePriceRange.dataset.priceDirty;

            // Sync number input values into the target slider container,
            // then call its reset/repaint API if available.
            const sourceMin = sourcePriceRange.querySelector('[data-input="min"]');
            const sourceMax = sourcePriceRange.querySelector('[data-input="max"]');
            const targetMin = targetPriceRange.querySelector('[data-input="min"]');
            const targetMax = targetPriceRange.querySelector('[data-input="max"]');
            if (sourceMin && targetMin) targetMin.value = sourceMin.value;
            if (sourceMax && targetMax) targetMax.value = sourceMax.value;

            // Use public API to repaint the custom slider in the target form
            if (typeof targetPriceRange._sliderCommitFromInputs === "function") {
                targetPriceRange._sliderCommitFromInputs({ silent: true });
            }
        }
    });
};

// refreshSliderVisuals removed — new custom slider manages its own paint()
// via container._sliderReset() public API.

// ─────────────────────────────────────────────
// ACTIVE FILTER BADGE HELPERS
// ─────────────────────────────────────────────

const updateSidebarActiveState = (url) => {
    const params = new URL(url, window.location.origin).searchParams;

    document.querySelectorAll("[data-category-link]").forEach((link) =>
        link.classList.remove("!font-bold", "!text-primary", "bg-primary/10")
    );

    params.getAll("category[]").forEach((cat) => {
        const link = document.querySelector(`[data-category-link="${cat}"]`);
        if (link)
            link.classList.add("!font-bold", "!text-primary", "bg-primary/10");
    });
};

const getSubcategoryLabel = (value) => {
    const input = document.querySelector(
        `input[name="subcategory[]"][value="${value}"]`
    );
    if (!input) return value;
    const label = input.closest("label");
    if (!label) return value;
    const span = label.querySelector("span.ml-3");
    return span ? span.textContent.trim() : value;
};

const getCategoryLabel = (value) => {
    const input = document.querySelector(
        `input[name="category[]"][value="${value}"]`
    );
    if (!input) return value.replace(/-/g, " ");
    const label = input.closest("label");
    if (!label) return value.replace(/-/g, " ");
    const span = label.querySelector("span.ml-3, span[data-localized]");
    return span ? span.textContent.trim() : label.textContent.trim();
};

const updateActiveFilters = (url) => {
    if (!activeFiltersContainer) return;

    const params = new URL(url, window.location.origin).searchParams;
    let html = `<span class="text-sm font-medium text-gray-700">${trans("Active Filters:")}</span>`;
    let hasFilters = false;

    const makeChip = (label, color, type, value = "") => {
        hasFilters = true;
        return `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm ${color} hover:cursor-pointer hover:opacity-80 transition-opacity">
            <span>${label}</span>
            <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="${type}" ${value ? `data-filter-value="${value}"` : ""}>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </span>`;
    };

    params.getAll("brand[]").forEach((v) =>
        (html += makeChip(`<span class="capitalize">${v.replace(/-/g, " ")}</span>`, "bg-blue-100 text-blue-700", "brand", v))
    );

    params.getAll("category[]").forEach((v) =>
        (html += makeChip(`<span class="capitalize">${getCategoryLabel(v)}</span>`, "bg-primary/10 text-primary", "category", v))
    );

    params.getAll("subcategory[]").forEach((v) =>
        (html += makeChip(`Subcategory: ${getSubcategoryLabel(v)}`, "bg-indigo-100 text-indigo-700", "subcategory", v))
    );

    params.getAll("availability[]").forEach((v) =>
        (html += makeChip(`<span class="capitalize">${v.replace(/_/g, " ")}</span>`, "bg-green-100 text-green-700", "availability", v))
    );

    params.getAll("rating[]").forEach((v) =>
        (html += makeChip(`${v} Star${v !== "1" ? "s" : ""} and up`, "bg-yellow-100 text-yellow-700", "rating", v))
    );

    params.getAll("discount[]").forEach((v) =>
        (html += makeChip(`${v}% off or more`, "bg-red-100 text-red-700", "discount", v))
    );

    // ─── FIX: use window.CURRENCY_SYMBOL with safe fallback
    const currency = window.CURRENCY_SYMBOL || "";
    const minPrice = params.get("min_price");
    const maxPrice = params.get("max_price");
    if (minPrice || maxPrice) {
        html += makeChip(
            `${currency}${minPrice || 0} – ${currency}${maxPrice || "max"}`,
            "bg-purple-100 text-purple-700",
            "price"
        );
    }

    if (hasFilters) {
        activeFiltersContainer.innerHTML = html;
        activeFiltersContainer.style.display = "flex";
        attachRemoveFilterListeners();
    } else {
        activeFiltersContainer.innerHTML = "";
        activeFiltersContainer.style.display = "none";
    }
};

// ─────────────────────────────────────────────
// LOADING STATE
// ─────────────────────────────────────────────

const filterForms             = getFilterForms();
const resultsContainer        = document.getElementById("products-results");
const activeFiltersContainer  = document.getElementById("active-filters-container");
const paginationContainer     = document.getElementById("products-pagination");
const loadingOverlay          = document.getElementById("filter-loading");
const searchInput             = document.getElementById("products-search");
const clearSearchBtn          = document.getElementById("clear-search");
const sortSelect              = document.getElementById("products-sort");
const viewGridBtn             = document.getElementById("view-grid-btn");
const viewListBtn             = document.getElementById("view-list-btn");
const clearFiltersBtn         = document.getElementById("clear-filters");
const applyFiltersBtn         = document.getElementById("apply-mobile-filters");
const filterBtn               = document.getElementById("mobile-filter-btn");
const filterModal             = document.getElementById("mobile-filter-modal");
const closeModalBtn           = document.getElementById("close-modal");
const closeModalBtnAlt        = document.getElementById("close-modal-btn");

const debounce = (fn, delay = 300) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
};

const showLoading = () => {
    if (loadingOverlay) loadingOverlay.classList.remove("hidden");
    else if (resultsContainer)
        resultsContainer.classList.add("opacity-50", "pointer-events-none");
};

const hideLoading = () => {
    if (loadingOverlay) loadingOverlay.classList.add("hidden");
    else if (resultsContainer)
        resultsContainer.classList.remove("opacity-50", "pointer-events-none");
};

// ─────────────────────────────────────────────
// FORM SERIALISATION
// ─────────────────────────────────────────────

// ─── FIX: FormData only includes inputs that HAVE a name attribute.
//          price-range-slider.js strips name="" when slider is untouched,
//          which already prevents price params from leaking. serializeForm
//          no longer needs to double-check — it just trusts what FormData gives.
const serializeForm = (form) => {
    const formData = new FormData(form);
    const params   = new URLSearchParams();

    for (const [key, value] of formData.entries()) {
        if (value === null || value === undefined || value === "") continue;
        params.append(key, value);
    }
    return params;
};

// ─────────────────────────────────────────────
// FETCH & RENDER
// ─────────────────────────────────────────────

const fetchAndRender = async (url) => {
    if (!resultsContainer) return;
    try {
        showLoading();
        const res = await fetch(url, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        if (!res.ok) throw new Error(`Request failed: ${res.status}`);

        const html   = await res.text();
        const parser = new DOMParser();
        const doc    = parser.parseFromString(html, "text/html");

        const newResults    = doc.getElementById("products-results");
        const newPagination = doc.getElementById("products-pagination");

        if (newResults)    resultsContainer.innerHTML    = newResults.innerHTML;
        if (newPagination && paginationContainer)
            paginationContainer.innerHTML = newPagination.innerHTML;

        updateActiveFilters(url);
        updateSidebarActiveState(url);
        history.pushState({}, "", url);
        // Removed auto scroll to results
    } catch (error) {
        console.error("Filter AJAX error:", error);
        // ─── FIX: fall back gracefully to a hard navigation instead of silent failure
        window.location.href = url;
    } finally {
        hideLoading();
    }
};

const submitFilters = (event) => {
    const sourceForm = event?.target?.closest("#filter-form") || getFilterForm();
    const form       = sourceForm || getFilterForm();
    if (!form) return;
    if (event) event.preventDefault();
    syncFormsData(form);
    autoExpandCheckedCategories();
    const params = serializeForm(form);
    const url    = `${form.action}?${params.toString()}`;
    fetchAndRender(url);
};

// ─────────────────────────────────────────────
// VIEW TOGGLE
// ─────────────────────────────────────────────

const setActiveView = (view) => {
    getFilterForms().forEach((form) => {
        const input = form.querySelector('input[name="view"]');
        if (input) input.value = view;
    });

    if (view === "list") {
        viewListBtn?.classList.add("bg-primary", "text-white");
        viewListBtn?.classList.remove("hover:bg-gray-100");
        viewGridBtn?.classList.remove("bg-primary", "text-white");
        viewGridBtn?.classList.add("hover:bg-gray-100");
    } else {
        viewGridBtn?.classList.add("bg-primary", "text-white");
        viewGridBtn?.classList.remove("hover:bg-gray-100");
        viewListBtn?.classList.remove("bg-primary", "text-white");
        viewListBtn?.classList.add("hover:bg-gray-100");
    }
};

// ─────────────────────────────────────────────
// REMOVE FILTER CHIPS
// ─────────────────────────────────────────────

const attachRemoveFilterListeners = () => {
    document.querySelectorAll(".remove-filter").forEach((btn) => {
        // ─── FIX: clone-replace to avoid duplicate event listeners being stacked
        const fresh = btn.cloneNode(true);
        btn.replaceWith(fresh);
        fresh.addEventListener("click", (e) => {
            e.preventDefault();
            const filterType  = fresh.dataset.filterType;
            const filterValue = fresh.dataset.filterValue;

            document.querySelectorAll("#filter-form").forEach((form) => {
                if (filterType === "price") {
                    const priceRange = form.querySelector("[data-price-range]");
                    if (priceRange && typeof priceRange._sliderReset === "function") {
                        priceRange._sliderReset();
                    }
                } else {
                    form.querySelectorAll(`input[name="${filterType}[]"]`).forEach((input) => {
                        if (input.value === filterValue) {
                            input.checked = false;
                            // ─── FIX: if removing a subcategory, update parent checkbox state
                            if (filterType === "subcategory") {
                                updateParentCategoryCheckbox(input.dataset.categorySlug, form);
                            }
                            // ─── FIX: if removing a category, uncheck all its subcategories too
                            if (filterType === "category") {
                                form.querySelectorAll(
                                    `input[name="subcategory[]"][data-category-slug="${filterValue}"]`
                                ).forEach((sub) => (sub.checked = false));
                            }
                        }
                    });
                }
            });
            submitFilters();
        });
    });
};

// ─────────────────────────────────────────────
// CATEGORY / SUBCATEGORY LOGIC
// ─────────────────────────────────────────────

// ─── FIX: extracted helper so any code path can call it
function updateParentCategoryCheckbox(categorySlug, container = document) {
    if (!categorySlug) return;
    const parentCheckbox = container.querySelector(
        `.category-checkbox[data-category-slug="${categorySlug}"]`
    );
    if (!parentCheckbox) return;
    const allSubs     = container.querySelectorAll(`.subcategory-checkbox[data-category-slug="${categorySlug}"]`);
    const checkedSubs = container.querySelectorAll(`.subcategory-checkbox[data-category-slug="${categorySlug}"]:checked`);
    // ─── FIX: use indeterminate visually; fully checked only when ALL subs checked
    parentCheckbox.checked       = allSubs.length > 0 && checkedSubs.length === allSubs.length;
    parentCheckbox.indeterminate = checkedSubs.length > 0 && checkedSubs.length < allSubs.length;
}

const subCategoryCheckboxes = document.querySelectorAll(".subcategory-checkbox");
subCategoryCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
        updateParentCategoryCheckbox(this.dataset.categorySlug);
    });
});

const categoryCheckboxes = document.querySelectorAll(".category-checkbox");
categoryCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", function () {
        const slug    = this.dataset.categorySlug;
        const related = document.querySelectorAll(`.subcategory-checkbox[data-category-slug="${slug}"]`);

        // ─── FIX: when unchecking a category, also uncheck ALL its subcategories
        related.forEach((sub) => (sub.checked = this.checked));

        const container = document.querySelector(`.subcategories-container[data-category-slug="${slug}"]`);
        if (container) {
            container.classList.toggle("hidden", !this.checked);
            const toggleSvg = document.querySelector(`.toggle-subcategories[data-category-slug="${slug}"] svg`);
            if (toggleSvg) toggleSvg.classList.toggle("rotate-180", this.checked);
        }
    });
});

const toggleButtons = document.querySelectorAll(".toggle-subcategories");
toggleButtons.forEach((btn) => {
    btn.addEventListener("click", function (e) {
        e.preventDefault();
        const slug = this.dataset.categorySlug;
        const container = document.querySelector(`.subcategories-container[data-category-slug="${slug}"]`);
        if (!container) return;
        container.classList.toggle("hidden");
        this.querySelector("svg")?.classList.toggle("rotate-180");
    });
});

// ─────────────────────────────────────────────
// AUTO-EXPAND CHECKED CATEGORIES
// ─────────────────────────────────────────────

function autoExpandCheckedCategories(container = document) {
    // expand if any sub is checked
    container.querySelectorAll(".subcategory-checkbox:checked").forEach((cb) => {
        const slug = cb.dataset.categorySlug;
        container.querySelectorAll(`.subcategories-container[data-category-slug="${slug}"]`)
            .forEach((el) => el.classList.remove("hidden"));
        container.querySelectorAll(`.toggle-subcategories[data-category-slug="${slug}"] svg`)
            .forEach((svg) => svg.classList.add("rotate-180"));
    });

    // expand if parent category is checked
    container.querySelectorAll(".category-checkbox:checked").forEach((cb) => {
        const slug = cb.dataset.categorySlug;
        container.querySelectorAll(`.subcategories-container[data-category-slug="${slug}"]`)
            .forEach((el) => el.classList.remove("hidden"));
        container.querySelectorAll(`.toggle-subcategories[data-category-slug="${slug}"] svg`)
            .forEach((svg) => svg.classList.add("rotate-180"));
    });
}

// ─────────────────────────────────────────────
// FORM EVENT BINDINGS
// ─────────────────────────────────────────────

if (filterForms.length) {
    filterForms.forEach((form) => {
        form.addEventListener("submit", submitFilters);

        form.addEventListener("change", (e) => {
            if (!e.target) return;
            // Ignore changes on data-input elements mid-typing (blur/Enter handled by slider)
            if (e.target.matches('[data-input="min"], [data-input="max"]')) return;
            if (e.target.matches("input, select")) {
                syncFormsData(form);
                submitFilters(e);
            }
        });
    });
}

// ─── FIX: removed the duplicate per-input change listeners that were also
//         calling syncFormsData — the form-level listener above is enough.

// ─────────────────────────────────────────────
// MOBILE FILTER MODAL
// ─────────────────────────────────────────────

const openModal = () => {
    if (!filterModal) return;
    syncFormsData(getFilterForm());
    filterModal.classList.remove("hidden");
    document.body.classList.add("overflow-hidden");
    const modalContent = document.getElementById("modal-content");
    if (modalContent) {
        requestAnimationFrame(() => modalContent.classList.remove("translate-y-full"));
    }
    // Repaint slider thumbs AFTER the modal is visible (layout box is available)
    setTimeout(() => {
        filterModal.querySelectorAll("[data-price-range]").forEach((pr) => {
            if (typeof pr._sliderCommitFromInputs === "function") pr._sliderCommitFromInputs({ silent: true });
        });
        autoExpandCheckedCategories(filterModal);
    }, 60);
};

const closeModal = () => {
    if (!filterModal) return;
    const modalContent = document.getElementById("modal-content");
    if (modalContent) {
        modalContent.classList.add("translate-y-full");
        const onEnd = () => {
            filterModal.classList.add("hidden");
            document.body.classList.remove("overflow-hidden");
            modalContent.removeEventListener("transitionend", onEnd);
        };
        modalContent.addEventListener("transitionend", onEnd);
    } else {
        filterModal.classList.add("hidden");
        document.body.classList.remove("overflow-hidden");
    }
};

filterBtn?.addEventListener("click", openModal);
closeModalBtn?.addEventListener("click", (e) => { e.preventDefault(); closeModal(); });
closeModalBtnAlt?.addEventListener("click", (e) => { e.preventDefault(); closeModal(); });
filterModal?.addEventListener("click", (e) => { if (e.target === filterModal) closeModal(); });

// ─────────────────────────────────────────────
// SEARCH
// ─────────────────────────────────────────────

if (searchInput) {
    searchInput.addEventListener("input", debounce(() => {
        clearSearchBtn?.classList.toggle("hidden", !searchInput.value);
        submitFilters();
    }, 350));
}

clearSearchBtn?.addEventListener("click", () => {
    searchInput.value = "";
    clearSearchBtn.classList.add("hidden");
    submitFilters();
});

// ─────────────────────────────────────────────
// SORT
// ─────────────────────────────────────────────

sortSelect?.addEventListener("change", submitFilters);

// ─────────────────────────────────────────────
// CLEAR ALL FILTERS
// ─────────────────────────────────────────────

// ─── FIX: the old "Clear All" was an <a> tag that did a full page reload,
//         losing JS state. We intercept the button here and do a clean reset.
if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener("click", (e) => {
        e.preventDefault();

        document.querySelectorAll("#filter-form").forEach((form) => {
            form.reset();

            // Reset custom price slider via its public API
            form.querySelectorAll("[data-price-range]").forEach((pr) => {
                if (typeof pr._sliderReset === "function") pr._sliderReset();
            });
        });

        // Collapse all subcategory containers
        document.querySelectorAll(".subcategories-container").forEach((c) =>
            c.classList.add("hidden")
        );
        document.querySelectorAll(".toggle-subcategories svg").forEach((svg) =>
            svg.classList.remove("rotate-180")
        );

        if (searchInput) searchInput.value = "";
        clearSearchBtn?.classList.add("hidden");
        if (sortSelect) sortSelect.value = "newest";

        submitFilters();
    });
}

// ─────────────────────────────────────────────
// APPLY (MOBILE)
// ─────────────────────────────────────────────

applyFiltersBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    submitFilters();
    setTimeout(closeModal, 300);
});

// ─────────────────────────────────────────────
// VIEW BUTTONS
// ─────────────────────────────────────────────

viewGridBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    setActiveView("grid");
    submitFilters();
});

viewListBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    setActiveView("list");
    submitFilters();
});

// ─────────────────────────────────────────────
// SIDEBAR CATEGORY / SUBCATEGORY LINKS
// ─────────────────────────────────────────────

document.querySelectorAll(".category-sidebar-link").forEach((link) => {
    link.addEventListener("click", (e) => {
        e.preventDefault();
        const categorySlug = link.dataset.categoryLink;
        const forms = document.querySelectorAll("#filter-form");

        forms.forEach((form) => {
            form.querySelectorAll('input[name="category[]"]').forEach((cb) => (cb.checked = false));
            form.querySelectorAll('input[name="subcategory[]"]').forEach((cb) => (cb.checked = false));

            const cb = form.querySelector(`input[name="category[]"][value="${categorySlug}"]`);
            if (cb) cb.checked = true;

            // ─── FIX: also check all subcategories of the selected category
            form.querySelectorAll(
                `input[name="subcategory[]"][data-category-slug="${categorySlug}"]`
            ).forEach((sub) => (sub.checked = true));
        });

        setTimeout(submitFilters, 50);
    });
});

document.querySelectorAll(".subcategory-sidebar-link").forEach((link) => {
    link.addEventListener("click", (e) => {
        e.preventDefault();
        const subcategorySlug = link.dataset.subcategorySlug;
        const categorySlug    = link.dataset.categorySlug;
        const forms           = document.querySelectorAll("#filter-form");

        forms.forEach((form) => {
            form.querySelectorAll('input[name="category[]"]').forEach((cb) => (cb.checked = false));
            form.querySelectorAll('input[name="subcategory[]"]').forEach((cb) => (cb.checked = false));

            const catCb = form.querySelector(`input[name="category[]"][value="${categorySlug}"]`);
            if (catCb) catCb.checked = true;

            const subCb = form.querySelector(`input[name="subcategory[]"][value="${subcategorySlug}"]`);
            if (subCb) subCb.checked = true;
        });

        setTimeout(submitFilters, 50);
    });
});

// ─────────────────────────────────────────────
// INITIALISE ON LOAD
// ─────────────────────────────────────────────

// Wire remove-filter buttons that are already in the DOM
attachRemoveFilterListeners();

// Set correct view-toggle visual state
const initialViewInput = document.querySelector('#filter-form input[name="view"]');
if (initialViewInput) setActiveView(initialViewInput.value || "grid");

// Hide loading overlay (safety net)
if (loadingOverlay) loadingOverlay.classList.add("hidden");

// Auto-expand categories that are already checked (e.g. from URL params)
autoExpandCheckedCategories();

// Initialise indeterminate state for partially-checked categories
document.querySelectorAll(".category-checkbox").forEach((cb) =>
    updateParentCategoryCheckbox(cb.dataset.categorySlug)
);
