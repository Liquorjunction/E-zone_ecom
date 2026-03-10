// Client products page: AJAX filters, search, sort, view toggle
const getFilterForm = () => {
    // Get the visible filter form (in modal or sidebar)
    const modalForm = document.querySelector('#mobile-filter-modal #filter-form');
    const sidebarForm = document.querySelector('aside #filter-form');
    return modalForm && !document.getElementById('mobile-filter-modal').classList.contains('hidden') ? modalForm : (sidebarForm || document.getElementById('filter-form'));
};

const syncFormsData = () => {
    // Sync data between all filter forms
    const forms = document.querySelectorAll('#filter-form');
    if (forms.length <= 1) return;

    const primaryForm = forms[0];
    const formData = new FormData(primaryForm);

    forms.forEach(form => {
        if (form === primaryForm) return;

        // Clear all inputs in secondary form
        form.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(input => {
            input.checked = false;
        });
        form.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
            input.value = '';
        });

        // Copy data from primary form
        for (const [key, value] of formData.entries()) {
            const inputs = form.querySelectorAll(`[name="${key}"]`);
            inputs.forEach(input => {
                if (input.type === 'checkbox' || input.type === 'radio') {
                    if (input.value === value) input.checked = true;
                } else {
                    input.value = value;
                }
            });
        }
    });
};

const updateSidebarActiveState = (url) => {
    const urlObj = new URL(url, window.location.origin);
    const params = urlObj.searchParams;

    // Remove active state from all categories and subcategories
    document.querySelectorAll('[data-category-link]').forEach(link => {
        link.classList.remove('!font-bold', '!text-primary', 'bg-primary/10');
    });

    // Add active state to current filters
    const categories = params.getAll('category[]');
    categories.forEach(cat => {
        const link = document.querySelector(`[data-category-link="${cat}"]`);
        if (link) {
            link.classList.add('!font-bold', '!text-primary', 'bg-primary/10');
        }
    });
};

const filterForm = document.getElementById('filter-form');
const resultsContainer = document.getElementById('products-results');
const activeFiltersContainer = document.getElementById('active-filters-container');
const paginationContainer = document.getElementById('products-pagination');
const loadingOverlay = document.getElementById('filter-loading');
const searchInput = document.getElementById('products-search');
const clearSearchBtn = document.getElementById('clear-search');
const sortSelect = document.getElementById('products-sort');
const viewGridBtn = document.getElementById('view-grid-btn');
const viewListBtn = document.getElementById('view-list-btn');
const viewInput = document.getElementById('view-input');
const clearFiltersBtn = document.getElementById('clear-filters');
const applyFiltersBtn = document.getElementById('apply-mobile-filters');
const filterBtn = document.getElementById('mobile-filter-btn');
const filterModal = document.getElementById('mobile-filter-modal');
const closeModalBtn = document.getElementById('close-modal');
const closeModalBtnAlt = document.getElementById('close-modal-btn');

const debounce = (fn, delay = 300) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
};

const showLoading = () => {
    if (loadingOverlay) {
        loadingOverlay.classList.remove('hidden');
    } else if (resultsContainer) {
        resultsContainer.classList.add('opacity-50', 'pointer-events-none');
    }
};

const hideLoading = () => {
    if (loadingOverlay) {
        loadingOverlay.classList.add('hidden');
    } else if (resultsContainer) {
        resultsContainer.classList.remove('opacity-50', 'pointer-events-none');
    }
};

const serializeForm = (form) => {
    const formData = new FormData(form);
    const params = new URLSearchParams();
    const priceRange = form.querySelector('[data-price-range]');
    const minRangeInput = priceRange ? priceRange.querySelector('[data-range="min"]') : null;
    const maxRangeInput = priceRange ? priceRange.querySelector('[data-range="max"]') : null;

    // Fall back to slider bounds when data attributes are missing/stale in production caches.
    const parsedDefaultMin = priceRange ? parseFloat(priceRange.dataset.defaultMin) : NaN;
    const parsedDefaultMax = priceRange ? parseFloat(priceRange.dataset.defaultMax) : NaN;
    const fallbackDefaultMin = minRangeInput ? parseFloat(minRangeInput.min) : NaN;
    const fallbackDefaultMax = maxRangeInput ? parseFloat(maxRangeInput.max) : NaN;
    const defaultMin = Number.isFinite(parsedDefaultMin) ? parsedDefaultMin : fallbackDefaultMin;
    const defaultMax = Number.isFinite(parsedDefaultMax) ? parsedDefaultMax : fallbackDefaultMax;
    const isPriceDirty = priceRange && priceRange.dataset.priceDirty === 'true';
    const isNear = (a, b, tolerance = 0.01) => Math.abs(a - b) <= tolerance;

    for (const [key, value] of formData.entries()) {
        if (value === null || value === undefined || value === '') continue;

        if (key === 'min_price' || key === 'max_price') {
            const numericValue = parseFloat(value);
            if (!Number.isFinite(numericValue)) continue;

            const isDefaultMin = key === 'min_price' && Number.isFinite(defaultMin) && isNear(numericValue, defaultMin);
            const isDefaultMax = key === 'max_price' && Number.isFinite(defaultMax) && isNear(numericValue, defaultMax);

            // Never submit unchanged price bounds even if dirty flag desyncs.
            if (isDefaultMin || isDefaultMax || !isPriceDirty) {
                continue;
            }
        }

        params.append(key, value);
    }
    return params;
};

const getSubcategoryLabel = (value) => {
    const input = document.querySelector(`input[name="subcategory[]"][value="${value}"]`);
    if (!input) return value;
    const label = input.closest('label');
    if (!label) return value;
    const labelText = label.querySelector('span.ml-3');
    return labelText ? labelText.textContent.trim() : value;
};

const updateActiveFilters = (url) => {
    const urlObj = new URL(url, window.location.origin);
    const params = urlObj.searchParams;
    let html = '<span class="text-sm font-medium text-gray-700">Active Filters:</span>';
    let hasFilters = false;

    // Brand filters
    const brands = params.getAll('brand[]');
    brands.forEach(brand => {
        html += `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-brand="${brand}">
            <span class="capitalize">${brand.replace(/-/g, ' ')}</span>
            <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="brand" data-filter-value="${brand}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </span>`;
        hasFilters = true;
    });

    // Category filters
    const categories = params.getAll('category[]');
    categories.forEach(cat => {
        html += `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary/10 text-primary hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-category="${cat}">
            <span class="capitalize">${cat.replace(/-/g, ' ')}</span>
            <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="category" data-filter-value="${cat}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </span>`;
        hasFilters = true;
    });

    // Subcategory filters
    const subcategories = params.getAll('subcategory[]');
    subcategories.forEach(subcat => {
        const label = getSubcategoryLabel(subcat);
        html += `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-indigo-100 text-indigo-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-subcategory="${subcat}">
            <span>Subcategory: ${label}</span>
            <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="subcategory" data-filter-value="${subcat}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </span>`;
        hasFilters = true;
    });

    // Availability filters
    const availability = params.getAll('availability[]');
    availability.forEach(avail => {
        html += `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-availability="${avail}">
            <span class="capitalize">${avail.replace(/_/g, ' ')}</span>
            <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="availability" data-filter-value="${avail}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </span>`;
        hasFilters = true;
    });

    // Rating filters
    const ratings = params.getAll('rating[]');
    ratings.forEach(rating => {
        html += `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-yellow-100 text-yellow-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-rating="${rating}">
            <span>${rating} Star${rating != '1' ? 's' : ''} and up</span>
            <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="rating" data-filter-value="${rating}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </span>`;
        hasFilters = true;
    });

    // Discount filters
    const discounts = params.getAll('discount[]');
    discounts.forEach(disc => {
        html += `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-red-100 text-red-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-discount="${disc}">
            <span>${disc}% off or more</span>
            <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="discount" data-filter-value="${disc}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </span>`;
        hasFilters = true;
    });

    // Price range filters
    const minPrice = params.get('min_price');
    const maxPrice = params.get('max_price');
    if (minPrice || maxPrice) {
        html += `<span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-purple-100 text-purple-700 hover:cursor-pointer hover:opacity-80 transition-opacity" data-filter-price="true">
            <span>$${minPrice || 0} - $${maxPrice || 5000}</span>
            <button type="button" class="ml-2 hover:opacity-70 remove-filter" data-filter-type="price">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </span>`;
        hasFilters = true;
    }

    if (activeFiltersContainer) {
        if (hasFilters) {
            activeFiltersContainer.innerHTML = html;
            activeFiltersContainer.style.display = 'flex';
            attachRemoveFilterListeners();
        } else {
            activeFiltersContainer.innerHTML = '';
            activeFiltersContainer.style.display = 'none';
        }
    }
};

const fetchAndRender = async (url) => {
    if (!resultsContainer) return;
    try {
        showLoading();
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) throw new Error(`Request failed: ${res.status}`);
        const html = await res.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newResults = doc.getElementById('products-results');
        const newPagination = doc.getElementById('products-pagination');

        if (newResults && resultsContainer) {
            resultsContainer.innerHTML = newResults.innerHTML;
        }
        if (newPagination && paginationContainer) {
            paginationContainer.innerHTML = newPagination.innerHTML;
        }

        updateActiveFilters(url);
        updateSidebarActiveState(url);
        history.pushState({}, '', url);
        resultsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } catch (error) {
        console.error('Filter AJAX error:', error);
        window.location.href = url;
    } finally {
        hideLoading();
    }
};

const submitFilters = (event) => {
    const form = getFilterForm();
    if (!form) return;
    if (event) event.preventDefault();
    syncFormsData();
    autoExpandCheckedCategories(); // Auto-expand after syncing forms
    const params = serializeForm(form);
    const url = `${form.action}?${params.toString()}`;
    fetchAndRender(url);
};

const setActiveView = (view) => {
    if (!viewInput) return;
    viewInput.value = view;
    if (view === 'list') {
        if (viewListBtn) {
            viewListBtn.classList.add('bg-primary', 'text-white');
            viewListBtn.classList.remove('hover:bg-gray-100');
        }
        if (viewGridBtn) {
            viewGridBtn.classList.remove('bg-primary', 'text-white');
            viewGridBtn.classList.add('hover:bg-gray-100');
        }
    } else {
        if (viewGridBtn) {
            viewGridBtn.classList.add('bg-primary', 'text-white');
            viewGridBtn.classList.remove('hover:bg-gray-100');
        }
        if (viewListBtn) {
            viewListBtn.classList.remove('bg-primary', 'text-white');
            viewListBtn.classList.add('hover:bg-gray-100');
        }
    }
};

const attachRemoveFilterListeners = () => {
    document.querySelectorAll('.remove-filter').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const filterType = btn.dataset.filterType;
            const filterValue = btn.dataset.filterValue;
            const forms = document.querySelectorAll('#filter-form');

            forms.forEach(form => {
                if (filterType === 'price') {
                    const minInput = form.querySelector('input[name="min_price"]');
                    const maxInput = form.querySelector('input[name="max_price"]');
                    if (minInput) minInput.value = '';
                    if (maxInput) maxInput.value = '';
                } else {
                    const inputs = form.querySelectorAll(`input[name="${filterType}[]"]`);
                    inputs.forEach(input => {
                        if (input.value === filterValue) {
                            input.checked = false;
                        }
                    });
                }
            });
            submitFilters();
        });
    });
};

// Bind events
if (filterForm) {
    filterForm.addEventListener('submit', submitFilters);

    // Auto-submit when any filter input changes
    filterForm.addEventListener('change', (e) => {
        const target = e.target;
        if (!target) return;
        if (target.matches('input, select')) {
            submitFilters();
        }
    });
}

// Sync all forms when any change
document.querySelectorAll('#filter-form input, #filter-form select').forEach(input => {
    input.addEventListener('change', syncFormsData);
});

// Mobile filter modal controls
const openModal = () => {
    if (!filterModal) return;
    syncFormsData();
    const modalContent = document.getElementById('modal-content');
    filterModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    if (modalContent) {
        requestAnimationFrame(() => {
            modalContent.classList.remove('translate-y-full');
        });
    }
    // Auto-expand checked categories after modal is visible and forms are synced
    setTimeout(() => autoExpandCheckedCategories(), 50);
};

const closeModal = () => {
    if (!filterModal) return;
    const modalContent = document.getElementById('modal-content');
    if (modalContent) {
        modalContent.classList.add('translate-y-full');
        const onEnd = () => {
            filterModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            modalContent.removeEventListener('transitionend', onEnd);
        };
        modalContent.addEventListener('transitionend', onEnd);
    } else {
        filterModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
};

if (filterBtn) {
    filterBtn.addEventListener('click', openModal);
}
if (closeModalBtn) {
    closeModalBtn.addEventListener('click', (e) => {
        e.preventDefault();
        closeModal();
    });
}
if (closeModalBtnAlt) {
    closeModalBtnAlt.addEventListener('click', (e) => {
        e.preventDefault();
        closeModal();
    });
}
if (filterModal) {
    filterModal.addEventListener('click', (e) => {
        if (e.target === filterModal) {
            closeModal();
        }
    });
}

if (searchInput) {
    searchInput.addEventListener('input', debounce(() => {
        // Toggle clear button visibility
        if (clearSearchBtn) {
            clearSearchBtn.classList.toggle('hidden', !searchInput.value);
        }
        submitFilters();
    }, 350));
}

if (clearSearchBtn) {
    clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        clearSearchBtn.classList.add('hidden');
        submitFilters();
    });
}

if (sortSelect) {
    sortSelect.addEventListener('change', submitFilters);
}

if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener('click', () => {
        document.querySelectorAll('#filter-form').forEach(form => {
            form.reset();
        });
        document.querySelectorAll('.subcategories-container').forEach(container => {
            container.classList.add('hidden');
        });
        if (searchInput) searchInput.value = '';
        if (clearSearchBtn) clearSearchBtn.classList.add('hidden');
        if (sortSelect) sortSelect.value = 'newest';
        const priceRange = document.getElementById('price-range');
        if (priceRange) priceRange.value = 0;
        document.querySelectorAll('[data-input="min"], [data-input="max"]').forEach(input => {
            input.value = '';
            input.removeAttribute('name');
        });
        document.querySelectorAll('[data-price-range]').forEach(container => {
            container.dataset.priceDirty = 'false';
        });
        submitFilters();
    });
}

if (applyFiltersBtn) {
    applyFiltersBtn.addEventListener('click', (e) => {
        e.preventDefault();
        submitFilters();
        setTimeout(() => closeModal(), 300);
    });
}

if (viewGridBtn) {
    viewGridBtn.addEventListener('click', (e) => {
        e.preventDefault();
        setActiveView('grid');
        submitFilters();
    });
}

if (viewListBtn) {
    viewListBtn.addEventListener('click', (e) => {
        e.preventDefault();
        setActiveView('list');
        submitFilters();
    });
}

// Initialize view state from hidden input
if (viewInput) {
    setActiveView(viewInput.value || 'grid');
}

// Ensure loading overlay is hidden on page load
if (loadingOverlay) {
    loadingOverlay.classList.add('hidden');
}

// Category/subcategory helpers
const subCategoryCheckboxes = document.querySelectorAll('.subcategory-checkbox');
subCategoryCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', function () {
        const categorySlug = this.dataset.categorySlug;
        const parentCheckbox = document.querySelector(`.category-checkbox[data-category-slug="${categorySlug}"]`);
        const allSubcategories = document.querySelectorAll(`.subcategory-checkbox[data-category-slug="${categorySlug}"]`);
        const checkedSubcategories = document.querySelectorAll(`.subcategory-checkbox[data-category-slug="${categorySlug}"]:checked`);

        if (!parentCheckbox) return;

        if (checkedSubcategories.length === allSubcategories.length && allSubcategories.length > 0) {
            parentCheckbox.checked = true;
        } else {
            parentCheckbox.checked = false;
        }
    });
});

const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
categoryCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', function () {
        const categorySlug = this.dataset.categorySlug;
        const relatedSubCheckboxes = document.querySelectorAll(`.subcategory-checkbox[data-category-slug="${categorySlug}"]`);
        relatedSubCheckboxes.forEach((sub) => {
            sub.checked = this.checked;
        });

        const container = document.querySelector(`.subcategories-container[data-category-slug="${categorySlug}"]`);
        if (container) {
            if (this.checked) {
                container.classList.remove('hidden');
                const toggleBtn = document.querySelector(`.toggle-subcategories[data-category-slug="${categorySlug}"] svg`);
                if (toggleBtn) toggleBtn.classList.add('rotate-180');
            } else {
                container.classList.add('hidden');
                const toggleBtn = document.querySelector(`.toggle-subcategories[data-category-slug="${categorySlug}"] svg`);
                if (toggleBtn) toggleBtn.classList.remove('rotate-180');
            }
        }
    });
});

const toggleButtons = document.querySelectorAll('.toggle-subcategories');
toggleButtons.forEach((btn) => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const categorySlug = this.dataset.categorySlug;
        const container = document.querySelector(`.subcategories-container[data-category-slug="${categorySlug}"]`);
        if (!container) return;
        container.classList.toggle('hidden');
        const svg = this.querySelector('svg');
        if (svg) svg.classList.toggle('rotate-180');
    });
});

// Initialize remove filter buttons
attachRemoveFilterListeners();

// Sidebar sync: Intercept sidebar category/subcategory clicks and sync with filter form
document.querySelectorAll('.category-sidebar-link').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const categorySlug = link.dataset.categoryLink;
        const forms = document.querySelectorAll('#filter-form');

        // First, uncheck all categories in all forms
        forms.forEach(form => {
            form.querySelectorAll('input[name="category[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        });

        // Then check the selected category and its subcategories
        forms.forEach(form => {
            const categoryCheckbox = form.querySelector(`input[name="category[]"][value="${categorySlug}"]`);
            if (categoryCheckbox) {
                categoryCheckbox.checked = true;
            }

            form.querySelectorAll(`input[name="subcategory[]"][data-category-slug="${categorySlug}"]`).forEach(checkbox => {
                checkbox.checked = true;
            });
        });

        // Submit the filter form
        setTimeout(() => submitFilters(), 100);
    });
});

document.querySelectorAll('.subcategory-sidebar-link').forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const subcategorySlug = link.dataset.subcategorySlug;
        const categorySlug = link.dataset.categorySlug;
        const forms = document.querySelectorAll('#filter-form');

        // First, uncheck all categories and subcategories
        forms.forEach(form => {
            form.querySelectorAll('input[name="category[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
            form.querySelectorAll('input[name="subcategory[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        });

        // Check the selected category and subcategory
        forms.forEach(form => {
            const categoryCheckbox = form.querySelector(`input[name="category[]"][value="${categorySlug}"]`);
            if (categoryCheckbox) {
                categoryCheckbox.checked = true;
            }

            const subcategoryCheckbox = form.querySelector(`input[name="subcategory[]"][value="${subcategorySlug}"]`);
            if (subcategoryCheckbox) {
                subcategoryCheckbox.checked = true;
            }
        });

        // Submit the filter form
        setTimeout(() => submitFilters(), 100);
    });
});

// Auto-expand subcategories based on checked checkboxes
function autoExpandCheckedCategories(container = document) {
    // Auto-expand if any subcategory is checked
    container.querySelectorAll('.subcategory-checkbox:checked').forEach(checkbox => {
        const categorySlug = checkbox.dataset.categorySlug;
        const subcatContainers = container.querySelectorAll(`.subcategories-container[data-category-slug="${categorySlug}"]`);
        subcatContainers.forEach(subcatContainer => {
            subcatContainer.classList.remove('hidden');
        });
        const toggleBtns = container.querySelectorAll(`.toggle-subcategories[data-category-slug="${categorySlug}"] svg`);
        toggleBtns.forEach(btn => {
            btn.classList.add('rotate-180');
        });
    });

    // Auto-expand if parent category is checked
    container.querySelectorAll('.category-checkbox:checked').forEach(checkbox => {
        const categorySlug = checkbox.dataset.categorySlug;
        const subcatContainers = container.querySelectorAll(`.subcategories-container[data-category-slug="${categorySlug}"]`);
        subcatContainers.forEach(subcatContainer => {
            subcatContainer.classList.remove('hidden');
        });
        const toggleBtns = container.querySelectorAll(`.toggle-subcategories[data-category-slug="${categorySlug}"] svg`);
        toggleBtns.forEach(btn => {
            btn.classList.add('rotate-180');
        });
    });
}

// Run auto-expand on page load
autoExpandCheckedCategories();
