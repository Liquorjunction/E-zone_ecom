document.addEventListener('DOMContentLoaded', function () {
    const filterPanel = document.getElementById('filter-panel');
    const filterForm = document.getElementById('contact-us-filter-bar');
    const applyBtn = document.getElementById('apply-filters-btn');
    const resetBtn = document.getElementById('reset-filters-btn');
    const subjectSelect = document.getElementById('subject');

    function reloadTable() {
        if (window.contactUsTable) {
            window.contactUsTable.ajax.reload();
        }
    }

    function hasActiveFilters() {
        const status = document.querySelector('input[name="status"]:checked')?.value || 'all';
        const subject = subjectSelect?.value || '';
        const dateFrom = document.getElementById('date_from')?.value || '';
        const dateTo = document.getElementById('date_to')?.value || '';

        return !!(status !== 'all' || subject || dateFrom || dateTo);
    }

    function loadSubjects() {
        if (!subjectSelect || subjectSelect.dataset.loaded === '1') {
            return;
        }

        const url = subjectSelect.dataset.listUrl;
        if (!url) {
            return;
        }

        subjectSelect.dataset.loaded = 'loading';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Failed to load subjects');
                }
                return response.json();
            })
            .then(function (payload) {
                const selected = subjectSelect.value || '';
                const subjects = Array.isArray(payload.data) ? payload.data : [];

                subjectSelect.innerHTML = '<option value="">All Subjects</option>';

                subjects.forEach(function (subject) {
                    const option = document.createElement('option');
                    option.value = subject;
                    option.textContent = String(subject).replace(/_/g, ' ').replace(/\b\w/g, function (char) {
                        return char.toUpperCase();
                    });
                    subjectSelect.appendChild(option);
                });

                if (selected) {
                    subjectSelect.value = selected;
                }

                subjectSelect.dataset.loaded = '1';
            })
            .catch(function () {
                subjectSelect.dataset.loaded = '0';
            });
    }

    if (subjectSelect) {
        subjectSelect.addEventListener('focus', loadSubjects);
        subjectSelect.addEventListener('mousedown', loadSubjects);
    }

    if (applyBtn) {
        applyBtn.addEventListener('click', function () {
            reloadTable();
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            if (filterForm) {
                filterForm.reset();
            }

            const statusAll = document.getElementById('status_all');
            if (statusAll) {
                statusAll.checked = true;
            }

            if (subjectSelect) {
                subjectSelect.value = '';
            }

            reloadTable();
        });
    }

    if (filterForm) {
        filterForm.addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                reloadTable();
            }
        });
    }

    if (filterPanel && typeof bootstrap !== 'undefined' && hasActiveFilters()) {
        new bootstrap.Collapse(filterPanel, { toggle: false }).show();
    }
});
