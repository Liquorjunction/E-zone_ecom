document.addEventListener('DOMContentLoaded', function () {
    const filterPanel = document.getElementById('filter-panel');
    const filterForm = document.getElementById('newsletter-filter-bar');
    const resetBtn = document.getElementById('reset-filters-btn');
    const statusCheckboxes = document.querySelectorAll('.newsletter-status-filter');
    const dateFromInput = document.getElementById('filter_date_from');
    const dateToInput = document.getElementById('filter_date_to');

    function reloadTable() {
        if (window.newsletterTable) {
            window.newsletterTable.ajax.reload();
        }
    }

    function hasActiveFilters() {
        return !!(
            document.querySelector('.newsletter-status-filter:checked') ||
            (dateFromInput && dateFromInput.value) ||
            (dateToInput && dateToInput.value)
        );
    }

    // Keep status filter single-select while using checkbox UI.
    statusCheckboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                statusCheckboxes.forEach(function (other) {
                    if (other !== checkbox) {
                        other.checked = false;
                    }
                });
            }
        });
    });

    if (filterForm) {
        filterForm.addEventListener('submit', function (event) {
            event.preventDefault();
            reloadTable();
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (filterForm) {
                filterForm.reset();
            }

            statusCheckboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });
            reloadTable();
        });
    }

    if (filterPanel && typeof bootstrap !== 'undefined' && hasActiveFilters()) {
        new bootstrap.Collapse(filterPanel, { toggle: false }).show();
    }
});
