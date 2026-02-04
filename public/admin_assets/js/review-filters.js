document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggle-filters-btn');
    const filtersCard = document.getElementById('filters-card');
    const filterBtnText = document.getElementById('filter-btn-text');
    const applyBtn = document.getElementById('apply-filters');
    const clearBtn = document.getElementById('clear-filters');
    const filterForm = document.getElementById('filter-form');

    if (!toggleBtn || !filtersCard) return;

    // Toggle filters visibility
    toggleBtn.addEventListener('click', function() {
        if (filtersCard.style.display === 'none') {
            filtersCard.style.display = 'block';
            if (filterBtnText) filterBtnText.textContent = 'Hide Filters';
        } else {
            filtersCard.style.display = 'none';
            if (filterBtnText) filterBtnText.textContent = 'Show Filters';
        }
    });

    // Apply filters - reload DataTable with new parameters
    if (applyBtn) {
        applyBtn.addEventListener('click', function() {
            if (window.reviewsDataTable) {
                window.reviewsDataTable.ajax.reload();
            }
        });
    }

    // Clear filters
    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            // Clear all filter inputs
            if (filterForm) {
                filterForm.reset();
            }

            // Reload table with cleared filters
            if (window.reviewsDataTable) {
                window.reviewsDataTable.ajax.reload();
            }
        });
    }

    // Allow Enter key to apply filters
    if (filterForm) {
        filterForm.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (window.reviewsDataTable) {
                    window.reviewsDataTable.ajax.reload();
                }
            }
        });
    }

    // Initialize - hide filters by default
    filtersCard.style.display = 'none';
});
