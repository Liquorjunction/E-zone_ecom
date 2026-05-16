(function () {
    function onReady(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }
        callback();
    }

    onReady(function () {
        var filterForm = document.getElementById('faq-category-filter-bar');
        if (!filterForm) {
            return;
        }

        var applyButton = filterForm.querySelector("button[type='submit']");
        var resetButton = document.getElementById('reset-filters-btn');
        var filterBadge = document.getElementById('filter-active-badge');
        var filterResultCount = document.getElementById('filter-result-count');
        var filterPanelElement = document.getElementById('filter-panel');

        function getTable() {
            return window.faqCategoriesTable || null;
        }

        function getStatusValue() {
            var checked = filterForm.querySelector("input[name='status']:checked");
            return checked ? checked.value : 'all';
        }

        function getDateValue(name) {
            var input = filterForm.querySelector("input[name='" + name + "']");
            return input ? input.value : '';
        }

        function countActiveFilters() {
            var count = 0;

            if (getStatusValue() !== 'all') {
                count += 1;
            }

            if (getDateValue('start_date') || getDateValue('end_date')) {
                count += 1;
            }

            return count;
        }

        function updateFilterUI() {
            var activeCount = countActiveFilters();

            if (filterBadge) {
                filterBadge.textContent = String(activeCount);
                filterBadge.classList.toggle('d-none', activeCount === 0);
            }

            if (filterResultCount) {
                filterResultCount.classList.toggle('d-none', activeCount === 0);
            }
        }

        function openFiltersIfNeeded() {
            var activeCount = countActiveFilters();
            if (activeCount === 0 || !filterPanelElement || typeof bootstrap === 'undefined') {
                return;
            }
            var collapse = new bootstrap.Collapse(filterPanelElement, { toggle: false });
            collapse.show();
        }

        function reloadTable() {
            var dataTable = getTable();
            if (!dataTable) {
                return;
            }
            dataTable.ajax.reload();
        }

        if (applyButton) {
            applyButton.addEventListener('click', function (event) {
                event.preventDefault();
                updateFilterUI();
                reloadTable();
            });
        }

        if (resetButton) {
            resetButton.addEventListener('click', function () {
                var allOption = filterForm.querySelector("input[name='status'][value='all']");
                if (allOption) {
                    allOption.checked = true;
                }

                var startDate = filterForm.querySelector("input[name='start_date']");
                var endDate = filterForm.querySelector("input[name='end_date']");
                if (startDate) {
                    startDate.value = '';
                }
                if (endDate) {
                    endDate.value = '';
                }

                updateFilterUI();
                reloadTable();
            });
        }

        filterForm.addEventListener('change', function (event) {
            if (!event.target.classList.contains('filter-input')) {
                return;
            }
            updateFilterUI();
        });

        updateFilterUI();
        openFiltersIfNeeded();
    });
})();
