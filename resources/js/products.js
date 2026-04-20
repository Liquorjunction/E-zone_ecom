// Client products page: AJAX filters, search, sort, view toggle
const filterForm = document.getElementById('filter-form');
const resultsContainer = document.getElementById('products-results');
const paginationContainer = document.getElementById('products-pagination');
const loadingOverlay = document.getElementById('filter-loading');
const searchInput = document.getElementById('products-search');
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
    for (const [key, value] of formData.entries()) {
        if (value === null || value === undefined || value === '') continue;
        params.append(key, value);
    }
    return params;
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

        history.pushState({}, '', url);
        // Removed auto scroll to results
    } catch (error) {
        console.error('Filter AJAX error:', error);
        window.location.href = url; // fallback to full navigation
    } finally {
        hideLoading();
    }
};

const submitFilters = (event) => {
    if (!filterForm) return;
    if (event) event.preventDefault();
    const params = serializeForm(filterForm);
    const url = `${filterForm.action}?${params.toString()}`;
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

// Mobile filter modal controls
const openModal = () => {
    if (!filterModal) return;
    const modalContent = document.getElementById('modal-content');
    filterModal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    if (modalContent) {
        requestAnimationFrame(() => modalContent.classList.remove('translate-y-full'));
    }
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
    closeModalBtn.addEventListener('click', closeModal);
}
if (closeModalBtnAlt) {
    closeModalBtnAlt.addEventListener('click', closeModal);
}
if (filterModal) {
    filterModal.addEventListener('click', (e) => {
        if (e.target === filterModal) {
            closeModal();
        }
    });
}

if (searchInput) {
    searchInput.addEventListener('input', debounce(() => submitFilters(), 350));
}

if (sortSelect) {
    sortSelect.addEventListener('change', submitFilters);
}

if (clearFiltersBtn && filterForm) {
    clearFiltersBtn.addEventListener('click', () => {
        filterForm.reset();
        if (searchInput) searchInput.value = '';
        if (sortSelect) sortSelect.value = 'newest';
        const min = filterForm.querySelector('input[name="min_price"]');
        const max = filterForm.querySelector('input[name="max_price"]');
        if (min) min.value = '';
        if (max) max.value = '';
        submitFilters();
    });
}

if (applyFiltersBtn && filterForm) {
    applyFiltersBtn.addEventListener('click', (e) => {
        e.preventDefault();
        submitFilters();
        closeModal();
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

// Category/subcategory helpers
const subCategoryCheckboxes = document.querySelectorAll('.subcategory-checkbox');
subCategoryCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', function () {
        const categoryId = this.dataset.categoryId;
        const parentCheckbox = document.querySelector(`.category-checkbox[value="${categoryId}"]`);
        if (this.checked && parentCheckbox && !parentCheckbox.checked) {
            parentCheckbox.checked = true;
        }
    });
});

const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
categoryCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', function () {
        const categoryId = this.value;
        const relatedSubCheckboxes = document.querySelectorAll(`.subcategory-checkbox[data-category-id="${categoryId}"]`);
        relatedSubCheckboxes.forEach((sub) => {
            sub.checked = this.checked;
        });
    });
});

const toggleButtons = document.querySelectorAll('.toggle-subcategories');
toggleButtons.forEach((btn) => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        const cid = this.dataset.categoryId;
        const container = document.querySelector(`.subcategories-container[data-category-id="${cid}"]`);
        if (!container) return;
        container.classList.toggle('hidden');
        const svg = this.querySelector('svg');
        if (svg) svg.classList.toggle('rotate-180');
    });
});
