document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggle-filters-btn');
    const filtersCard = document.getElementById('filters-card');
    const filterBtnText = document.getElementById('filter-btn-text');
    const applyBtn = document.getElementById('apply-filters');
    const clearBtn = document.getElementById('clear-filters');
    const resetBtn = document.getElementById('reset-filters');
    const filterForm = document.getElementById('filter-form');

    if (!toggleBtn || !filtersCard) return;

    // Toggle filters on button click
    toggleBtn.addEventListener('click', function() {
        if (filtersCard.style.display === 'none') {
            filtersCard.style.display = 'block';
            if (filterBtnText) filterBtnText.textContent = 'Hide Filters';
        } else {
            filtersCard.style.display = 'none';
            if (filterBtnText) filterBtnText.textContent = 'Show Filters';
        }
    });

    // Apply filters
    if (applyBtn) {
        applyBtn.addEventListener('click', function() {
            if (window.customersDataTable) {
                window.customersDataTable.ajax.reload();
            }
        });
    }

    // Clear filters
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            // Clear each input explicitly
            document.getElementById('status-filter').value = '';
            document.getElementById('date-from-filter').value = '';
            document.getElementById('date-to-filter').value = '';

            // Reload table with cleared filters
            if (window.customersDataTable) {
                window.customersDataTable.ajax.reload();
            }
        });
    }

    // Reset filters and reload table
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            if (filterForm) {
                filterForm.reset();
            }
            if (window.customersDataTable) {
                window.customersDataTable.ajax.reload();
            }
        });
    }

    // Allow Enter key to apply filters
    if (filterForm) {
        filterForm.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (window.customersDataTable) {
                    window.customersDataTable.ajax.reload();
                }
            }
        });
    }

    // Initialize - hide by default
    filtersCard.style.display = 'none';
});
