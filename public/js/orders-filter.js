document.addEventListener('DOMContentLoaded', function() {
    const cancelModal = document.getElementById('cancel-order-modal');
    if (cancelModal && cancelModal.parentNode !== document.body) {
        document.body.appendChild(cancelModal);
    }
    if (cancelModal) {
        cancelModal.classList.add('bg-black', 'bg-opacity-50');
        cancelModal.style.backgroundColor = 'rgba(0,0,0,0.5)';
    }
    const cancelForm = document.getElementById('cancel-order-form');
    const cancelIdInput = document.getElementById('cancel-order-id');
    const cancelOrderNumberSpan = document.getElementById('cancel-order-number');
    const openCancelBtns = document.querySelectorAll('.open-cancel-modal-btn');
    const closeCancelBtns = document.querySelectorAll('.close-cancel-modal-btn');
    if (openCancelBtns && cancelForm && cancelModal) {
        openCancelBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const orderId = this.getAttribute('data-order-id');
                const orderNumber = this.getAttribute('data-order-number');
                cancelIdInput.value = orderId;
                cancelOrderNumberSpan.textContent = orderNumber;
                cancelForm.action = '/client/orders/cancel';
                cancelModal.classList.remove('hidden');
                cancelModal.classList.add('flex');
            });
        });
    }
    if (closeCancelBtns && cancelForm && cancelModal) {
        closeCancelBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                cancelModal.classList.add('hidden');
                cancelModal.classList.remove('flex');
                cancelForm.reset();
            });
        });
    }
    if (cancelModal && cancelForm) {
        cancelModal.addEventListener('click', function(e){
            if(e.target === this) {
                cancelModal.classList.add('hidden');
                cancelModal.classList.remove('flex');
                cancelForm.reset();
            }
        });
        cancelForm.addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    }

    const form = document.getElementById('orders-filter-form');
    if (!form) return;

    function ajaxLoad(url) {
        const container = document.getElementById('orders-list-container');
        if (container) {
            container.style.opacity = '0.5';
            container.style.pointerEvents = 'none';
        }

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.json();
            })
            .then(data => {
                if (data && data.html) {
                    container.innerHTML = data.html;
                    container.style.opacity = '1';
                    container.style.pointerEvents = 'auto';
                    window.history.pushState({}, '', url);
                    attachPaginationHandler();
                    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                } else {
                    window.location.reload();
                }
            })
            .catch(() => {
                window.location.href = url;
            });
    }

    // Always AJAX for all filter submits, never reload
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        const params = new URLSearchParams();
        for (const [key, value] of formData.entries()) {
            if (value) params.append(key, value);
        }
        const queryString = params.toString();
        const url = window.location.pathname + (queryString ? '?' + queryString : '');
        ajaxLoad(url);
    });

    const toggleBtn = document.getElementById('toggle-advanced-filters');
    const advancedFilters = document.getElementById('advanced-filters');
    const filterIcon = document.getElementById('filter-icon');
    const filterToggleText = document.getElementById('filter-toggle-text');

    if (toggleBtn && advancedFilters) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const isHidden = advancedFilters.classList.contains('hidden') ||
                           advancedFilters.style.display === 'none';

            if (isHidden) {
                advancedFilters.classList.remove('hidden');
                advancedFilters.style.display = '';
                toggleBtn.setAttribute('aria-expanded', 'true');
                if (filterToggleText) filterToggleText.textContent = 'Hide Advanced Filters';
                if (filterIcon) filterIcon.style.transform = 'rotate(45deg)';
            } else {
                advancedFilters.classList.add('hidden');
                advancedFilters.style.display = 'none';
                toggleBtn.setAttribute('aria-expanded', 'false');
                if (filterToggleText) filterToggleText.textContent = 'Advanced Filters';
                if (filterIcon) filterIcon.style.transform = 'rotate(0deg)';
            }
        });
    }

    // Consistent AJAX filter: all select/date/search auto-submit, no reloads, no Enter required
    // All filter controls (select, date, text) trigger AJAX, no reloads
    // Fix: always allow repeated search input AJAX submits
    // Debounced AJAX for all filter types (text, select, date)
    const autoSubmitElements = form.querySelectorAll('select, input[type=date], input[type="text"]');
    autoSubmitElements.forEach(el => {
        // Use a property on the element to store its timeout
        el._filterTimeout = null;
        if (el.type === 'text') {
            el.addEventListener('input', function() {
                if (this._filterTimeout) clearTimeout(this._filterTimeout);
                this._filterTimeout = setTimeout(() => {
                    form.requestSubmit();
                }, 400);
            });
            el.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (this._filterTimeout) clearTimeout(this._filterTimeout);
                    form.requestSubmit();
                }
            });
        } else {
            el.addEventListener('change', function() {
                if (this._filterTimeout) clearTimeout(this._filterTimeout);
                this._filterTimeout = setTimeout(() => {
                    form.requestSubmit();
                }, 400);
            });
        }
    });

    const clearSearchBtn = document.querySelector('[onclick*="getElementById(\'search\')"]');
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const searchField = document.getElementById('search');
            if (searchField) {
                searchField.value = '';
                form.requestSubmit();
            }
        });
    }

    function attachPaginationHandler() {
        const paginationLinks = document.querySelectorAll('#orders-list-container .pagination a');
        paginationLinks.forEach(link => {
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            newLink.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                if (url && url !== '#') {
                    ajaxLoad(url);
                }
            });
        });
    }

    attachPaginationHandler();

    window.addEventListener('popstate', function() {
        ajaxLoad(window.location.href);
    });

    function updateFilterCount() {
        const activeFilters = [];
        if (searchInput && searchInput.value) activeFilters.push('search');
        const statusSelect = document.getElementById('status');
        if (statusSelect && statusSelect.value) activeFilters.push('status');
        const sortSelect = document.getElementById('sort');
        if (sortSelect && sortSelect.value !== 'latest') activeFilters.push('sort');
        const dateFrom = document.getElementById('date_from');
        if (dateFrom && dateFrom.value) activeFilters.push('date_from');
        const dateTo = document.getElementById('date_to');
        if (dateTo && dateTo.value) activeFilters.push('date_to');
        const filterCountElements = document.querySelectorAll('[data-filter-count]');
        filterCountElements.forEach(el => {
            el.textContent = activeFilters.length;
            el.style.display = activeFilters.length > 0 ? '' : 'none';
        });
    }

    form.addEventListener('change', updateFilterCount);
    if (searchInput) {
        searchInput.addEventListener('input', updateFilterCount);
    }
    updateFilterCount();

    const clearAllBtn = document.querySelector('a[href*="current()"]');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function() {
        });
    }

    function showLoadingIndicator() {
        const applyButton = form.querySelector('button[type="submit"]');
        if (applyButton) {
            const originalText = applyButton.innerHTML;
            applyButton.innerHTML = `
                <svg class="animate-spin w-4 h-4 inline-block mr-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Loading...
            `;
            applyButton.disabled = true;
            setTimeout(() => {
                applyButton.innerHTML = originalText;
                applyButton.disabled = false;
            }, 2000);
        }
    }

    form.addEventListener('submit', showLoadingIndicator);
});
