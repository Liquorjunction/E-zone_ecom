document.addEventListener('DOMContentLoaded', function() {
    // --- Mobile Filter Modal Logic ---
    const filterBtn = document.getElementById('mobile-filter-btn');
    const filterModal = document.getElementById('mobile-filter-modal');
    const closeModalBtn = document.getElementById('close-modal');
    const closeModalBtnAlt = document.getElementById('close-modal-btn');
    const applyFiltersBtn = document.getElementById('apply-mobile-filters');

    function openModal() {
        if(filterModal) {
            const modalContent = document.getElementById('modal-content');
            filterModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden'); // Prevent background scrolling
            if (modalContent) {
                requestAnimationFrame(() => {
                    modalContent.classList.remove('translate-y-full');
                });
            }
        }
    }

    function closeModal() {
        if(filterModal) {
            const modalContent = document.getElementById('modal-content');
            if (modalContent) {
                modalContent.classList.add('translate-y-full');
                const onEnd = function() {
                    filterModal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    modalContent.removeEventListener('transitionend', onEnd);
                };
                modalContent.addEventListener('transitionend', onEnd);
            } else {
                filterModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }
    }

    if (filterBtn) {
        filterBtn.addEventListener('click', openModal);
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closeModal);
    }

    if (closeModalBtnAlt) {
        closeModalBtnAlt.addEventListener('click', closeModal);
    }

    // Close on backdrop click
    if (filterModal) {
        filterModal.addEventListener('click', function(e) {
            if (e.target === filterModal || e.target.closest('.fixed.inset-0.bg-gray-500')) {
                closeModal();
            }
        });
    }

    // Apply Filters (Simulate form submission if wrapped in form, or standard submit)
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            const form = document.querySelector('aside form') || document.querySelector('#mobile-filter-modal form');
            if (form) {
                // Trigger AJAX submit if handler present
                const event = new Event('submit', { cancelable: true });
                form.dispatchEvent(event);
            }
            closeModal();
        });
    }

    // --- View Toggle (Grid / List) ---
    const viewGridBtn = document.getElementById('view-grid-btn');
    const viewListBtn = document.getElementById('view-list-btn');
    const viewInput = document.getElementById('view-input');

    function setActiveView(view) {
        if (viewInput) viewInput.value = view;
        if (view === 'list') {
            if (viewGridBtn) { viewGridBtn.classList.remove('bg-primary','text-white'); viewGridBtn.classList.add('hover:bg-gray-100'); }
            if (viewListBtn) { viewListBtn.classList.add('bg-primary','text-white'); }
        } else {
            if (viewListBtn) { viewListBtn.classList.remove('bg-primary','text-white'); viewListBtn.classList.add('hover:bg-gray-100'); }
            if (viewGridBtn) { viewGridBtn.classList.add('bg-primary','text-white'); }
        }
    }

    // Initialize view buttons based on hidden input or URL
    if (viewInput) {
        setActiveView(viewInput.value || 'grid');
    }

    if (viewGridBtn) {
        viewGridBtn.addEventListener('click', function(e) {
            e.preventDefault();
            setActiveView('grid');
            // submit filters form (AJAX will handle)
            if (filterForm) filterForm.dispatchEvent(new Event('submit', { cancelable: true }));
        });
    }

    if (viewListBtn) {
        viewListBtn.addEventListener('click', function(e) {
            e.preventDefault();
            setActiveView('list');
            if (filterForm) filterForm.dispatchEvent(new Event('submit', { cancelable: true }));
        });
    }

    // --- Category / Subcategory Checkbox Logic ---

    // Auto-check parent category if a subcategory is checked
    const subCategoryCheckboxes = document.querySelectorAll('.subcategory-checkbox');
    subCategoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const categoryId = this.dataset.categoryId;
            const parentCheckbox = document.querySelector(`.category-checkbox[value="${categoryId}"]`);

            if (this.checked && parentCheckbox && !parentCheckbox.checked) {
                parentCheckbox.checked = true;
            }
        });
    });

    // Auto-check/uncheck all subcategories if parent is toggled
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const categoryId = this.value;
            const relatedSubCheckboxes = document.querySelectorAll(`.subcategory-checkbox[data-category-id="${categoryId}"]`);

            relatedSubCheckboxes.forEach(sub => {
                sub.checked = this.checked;
            });
        });
    });

    // Toggle subcategories dropdown when chevron/button is clicked
    const toggleButtons = document.querySelectorAll('.toggle-subcategories');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const cid = this.dataset.categoryId;
            const container = document.querySelector(`.subcategories-container[data-category-id="${cid}"]`);
            if (!container) return;
            container.classList.toggle('hidden');
            // rotate chevron
            const svg = this.querySelector('svg');
            if (svg) svg.classList.toggle('rotate-180');
        });
    });

    // --- AJAX Filter Submit ---
    const filterForm = document.getElementById('filter-form');
    const resultsContainer = document.getElementById('products-results');

    async function submitFiltersAjax(e) {
        if (!filterForm) return;
        // Allow normal non-AJAX submission if user disables JS, but intercept here
        if (e) e.preventDefault();
        // show spinner overlay
        const spinner = document.getElementById('filter-loading');
        if (spinner) spinner.classList.remove('hidden');

        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        for (const [key, value] of formData.entries()) {
            // Skip empty values so we don't submit blank query params (eg. min_price=)
            if (value === null || value === undefined || value === '') continue;
            params.append(key, value);
        }

        const url = `${filterForm.action}?${params.toString()}`;

        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Network response was not ok');
            const html = await res.text();

            // Parse response and extract new results container
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newResults = doc.getElementById('products-results');
            if (newResults && resultsContainer) {
                resultsContainer.innerHTML = newResults.innerHTML;

                // Update pagination links to submit via AJAX instead of full page loads
                document.querySelectorAll('#products-results .pagination a').forEach(a => {
                    a.addEventListener('click', function(ev) {
                        ev.preventDefault();
                        const pUrl = this.href;
                        // show spinner for pagination fetch
                        const spinnerPag = document.getElementById('filter-loading');
                        if (spinnerPag) spinnerPag.classList.remove('hidden');
                        fetch(pUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                            .then(r => r.text())
                            .then(t => {
                                const d = parser.parseFromString(t, 'text/html');
                                const nr = d.getElementById('products-results');
                                if (nr) resultsContainer.innerHTML = nr.innerHTML;
                                history.pushState({}, '', pUrl);
                            })
                            .catch(err => console.error(err))
                            .finally(() => { if (spinnerPag) spinnerPag.classList.add('hidden'); });
                    });
                });

                // Update browser URL
                history.pushState({}, '', url);
            }
        } catch (err) {
            console.error('Filter AJAX error:', err);
            // fallback to normal submit
            filterForm.submit();
        } finally {
            // hide spinner
            const spinnerFinal = document.getElementById('filter-loading');
            if (spinnerFinal) spinnerFinal.classList.add('hidden');
        }
    }

    if (filterForm) {
        filterForm.addEventListener('submit', submitFiltersAjax);
    }

    // Clear filters button behaviour
    const clearBtn = document.getElementById('clear-filters');
    if (clearBtn && filterForm) {
        clearBtn.addEventListener('click', function() {
            // reset all inputs inside the form
            filterForm.reset();
            // explicitly clear min/max so they don't submit as empty strings
            const min = filterForm.querySelector('input[name="min_price"]');
            const max = filterForm.querySelector('input[name="max_price"]');
            if (min) min.value = '';
            if (max) max.value = '';
            // fetch fresh results (AJAX) — submitFiltersAjax will update history
            submitFiltersAjax();
        });
    }
});
