class AjaxPagination {
    constructor(options = {}) {
        this.container = options.container || '#products-container';
        this.paginationContainer = options.paginationContainer || '.pagination-container';
        this.loadingClass = options.loadingClass || 'opacity-50 pointer-events-none';
        this.url = options.url || window.location.href;
        this.method = options.method || 'GET';
        this.headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ...options.headers
        };

        this.init();
    }

    init() {
        this.bindEvents();
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('.pagination-link')) {
                e.preventDefault();
                const page = e.target.closest('.pagination-link').dataset.page;
                this.loadPage(page);
            }
        });
    }

    async loadPage(page) {
        try {
            // Show loading state
            this.showLoading();

            const url = new URL(this.url);
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

            // Update content
            const newContainer = doc.querySelector(this.container);
            const newPagination = doc.querySelector(this.paginationContainer);

            if (newContainer) {
                document.querySelector(this.container).innerHTML = newContainer.innerHTML;
            }

            if (newPagination) {
                document.querySelector(this.paginationContainer).innerHTML = newPagination.innerHTML;
            }

            // Update URL without reload
            window.history.pushState({}, '', url.toString());

            // Scroll to top of container
            document.querySelector(this.container).scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });

        } catch (error) {
            console.error('Pagination error:', error);
            this.showError();
        } finally {
            this.hideLoading();
        }
    }

    showLoading() {
        const container = document.querySelector(this.container);
        if (container) {
            container.classList.add(this.loadingClass);
        }
    }

    hideLoading() {
        const container = document.querySelector(this.container);
        if (container) {
            container.classList.remove(this.loadingClass);
        }
    }

    showError() {
        // Simple error handling - you can customize this
        alert('Failed to load page. Please try again.');
    }
}

// Auto-initialize if elements exist
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('.pagination-link')) {
        window.ajaxPagination = new AjaxPagination();
    }
});

// Export for manual initialization
window.AjaxPagination = AjaxPagination;
