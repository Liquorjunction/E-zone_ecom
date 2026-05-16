document.addEventListener('DOMContentLoaded', function() {
    const filterPanel = document.getElementById('filter-panel');
    const applyBtn = document.getElementById('apply-filters-btn');
    const clearBtn = document.getElementById('reset-filters-btn');
    const filterForm = document.getElementById('review-filter-bar');
    const statusSelect = document.getElementById('status');
    const ratingSelect = document.getElementById('rating');

    function setLoadingOption(select, label) {
        if (!select) {
            return;
        }

        select.innerHTML = `<option value="">${label}</option>`;
    }

    function populateSelect(select, items, placeholderLabel, formatLabel) {
        if (!select) {
            return;
        }

        const currentValue = select.value;
        select.innerHTML = `<option value="">${placeholderLabel}</option>`;

        items.forEach(function(item) {
            const option = document.createElement('option');
            option.value = item.value;
            option.textContent = formatLabel(item.value);
            select.appendChild(option);
        });

        if (currentValue) {
            select.value = currentValue;
        }

        select.dataset.loaded = '1';
    }

    function loadDropdownOptions(select, url, itemsKey, placeholderLabel, formatLabel) {
        if (!select || select.dataset.loaded === '1' || !url) {
            return;
        }

        select.dataset.loaded = 'loading';
        setLoadingOption(select, 'Loading...');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Failed to load dropdown options.');
                }

                return response.json();
            })
            .then(function(payload) {
                const items = Array.isArray(payload[itemsKey]) ? payload[itemsKey] : [];
                populateSelect(select, items.map(function(value) {
                    return { value: value };
                }), placeholderLabel, formatLabel);
            })
            .catch(function() {
                setLoadingOption(select, placeholderLabel);
                select.dataset.loaded = '0';
            });
    }

    function formatStatusLabel(value) {
        return String(value)
            .replace(/[_-]+/g, ' ')
            .replace(/\b\w/g, function(letter) {
                return letter.toUpperCase();
            });
    }

    function formatRatingLabel(value) {
        const rating = Number(value);

        if (!Number.isFinite(rating)) {
            return String(value);
        }

        return rating === 1 ? '1 Star' : rating + ' Stars';
    }

    function handleLazyLoad(select) {
        if (!select) {
            return;
        }

        const url = select.dataset.listUrl;
        const placeholderLabel = select.id === 'rating' ? 'All Ratings' : 'All';
        const itemsKey = select.id === 'rating' ? 'ratings' : 'statuses';
        const formatter = select.id === 'rating' ? formatRatingLabel : formatStatusLabel;

        loadDropdownOptions(select, url, itemsKey, placeholderLabel, formatter);
    }

    function hasActiveFilters() {
        const status = document.getElementById('status')?.value || '';
        const dateFrom = document.getElementById('date_from')?.value || '';
        const dateTo = document.getElementById('date_to')?.value || '';
        const rating = document.getElementById('rating')?.value || '';

        return !!(status || dateFrom || dateTo || rating);
    }

    if (filterPanel && typeof bootstrap !== 'undefined' && hasActiveFilters()) {
        new bootstrap.Collapse(filterPanel, { toggle: false }).show();
    }

    if (statusSelect) {
        setLoadingOption(statusSelect, 'Loading statuses...');
        statusSelect.addEventListener('focus', function() {
            handleLazyLoad(statusSelect);
        });
        statusSelect.addEventListener('mousedown', function() {
            handleLazyLoad(statusSelect);
        });
    }

    if (ratingSelect) {
        setLoadingOption(ratingSelect, 'Loading ratings...');
        ratingSelect.addEventListener('focus', function() {
            handleLazyLoad(ratingSelect);
        });
        ratingSelect.addEventListener('mousedown', function() {
            handleLazyLoad(ratingSelect);
        });
    }

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
});
