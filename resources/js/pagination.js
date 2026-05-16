class AjaxPagination {
    constructor(root) {
        this.root = root;
        this.resultsSelector = root.dataset.resultsTarget || '#products-results';
        this.paginationSelector = root.dataset.paginationTarget || '#products-pagination';
        this.loadingSelector = root.dataset.loadingTarget || '';
        this.method = root.dataset.method || 'GET';
        this.headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
        };

        this.handleClick = this.handleClick.bind(this);
        this.init();
    }

    get resultsEl() {
        return document.querySelector(this.resultsSelector);
    }

    get paginationEl() {
        return document.querySelector(this.paginationSelector);
    }

    get loadingEl() {
        return this.loadingSelector ? document.querySelector(this.loadingSelector) : null;
    }

    init() {
        document.addEventListener('click', this.handleClick);
    }

    handleClick(e) {
        const trigger = e.target.closest('.pagination-link, .pagination a');
        if (!trigger || !this.root.contains(trigger)) {
            return;
        }

        const page = trigger.dataset.page || this.extractPageFromHref(trigger.getAttribute('href'));
        if (!page) {
            return;
        }

        e.preventDefault();
        this.loadPage(page, trigger.getAttribute('href'));
    }

    extractPageFromHref(href) {
        if (!href) return null;
        try {
            return new URL(href, window.location.origin).searchParams.get('page');
        } catch (err) {
            console.error('Failed to parse pagination href', err);
            return null;
        }
    }

    async loadPage(page, href = '') {
        try {
            this.showLoading();

            const url = new URL(href || window.location.href);
            url.searchParams.set('page', page);

            const response = await fetch(url.toString(), {
                method: this.method,
                headers: this.headers,
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const newResults = doc.querySelector(this.resultsSelector);
            const newPagination = doc.querySelector(this.paginationSelector);

            if (newResults && this.resultsEl) {
                this.resultsEl.innerHTML = newResults.innerHTML;
            }

            if (newPagination && this.paginationEl) {
                this.paginationEl.innerHTML = newPagination.innerHTML;
            }

            window.history.pushState({}, '', url.toString());
            // Removed auto scroll to results
        } catch (error) {
            console.error('Pagination error:', error);
            this.showError();
        } finally {
            this.hideLoading();
        }
    }

    showLoading() {
        if (this.loadingEl) {
            this.loadingEl.classList.remove('hidden');
            return;
        }

        if (this.resultsEl) {
            this.resultsEl.classList.add('opacity-50', 'pointer-events-none');
        }
    }

    hideLoading() {
        if (this.loadingEl) {
            this.loadingEl.classList.add('hidden');
            return;
        }

        if (this.resultsEl) {
            this.resultsEl.classList.remove('opacity-50', 'pointer-events-none');
        }
    }

    showError() {
        if (typeof showUniversalPopup === 'function') {
            showUniversalPopup(trans('Failed to load page. Please try again.'), 'error', 3000);
        } else if (window.Toast && typeof window.Toast.error === 'function') {
            window.Toast.error({ title: 'Error', message: trans('Failed to load page. Please try again.'), duration: 4000 });
        } else {
            alert('Failed to load page. Please try again.');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-ajax-pagination]').forEach((root) => {
        root.__ajaxPagination = new AjaxPagination(root);
    });
});

window.AjaxPagination = AjaxPagination;
