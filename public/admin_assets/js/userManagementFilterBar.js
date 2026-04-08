document.addEventListener('DOMContentLoaded', function () {
    const filterPanel = document.getElementById('filter-panel');
    const filterForm = document.getElementById('user-filter-bar');
    const applyBtn = document.getElementById('apply-filters-btn');
    const resetBtn = document.getElementById('reset-filters-btn');
    const roleSelect = document.getElementById('role_id');
    const statusCheckboxes = document.querySelectorAll('.user-status-filter');
    const dateFromInput = document.getElementById('date_from');
    const dateToInput = document.getElementById('date_to');

    function reloadTable() {
        if (window.userListTable) {
            window.userListTable.ajax.reload();
        }
    }

    function hasActiveFilters() {
        return !!(
            document.querySelector('.user-status-filter:checked') ||
            (roleSelect && roleSelect.value) ||
            (dateFromInput && dateFromInput.value) ||
            (dateToInput && dateToInput.value)
        );
    }

    function loadRoles() {
        if (!roleSelect || roleSelect.dataset.loaded === '1') {
            return;
        }

        const url = roleSelect.dataset.listUrl;
        if (!url) {
            return;
        }

        roleSelect.dataset.loaded = 'loading';

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Failed to load roles');
                }
                return response.json();
            })
            .then(function (payload) {
                const selected = roleSelect.value || '';
                const roles = payload && payload.data ? payload.data : {};

                roleSelect.innerHTML = '<option value="">All Roles</option>';

                if (Array.isArray(roles)) {
                    roles.forEach(function (role) {
                        const option = document.createElement('option');
                        option.value = role.id;
                        option.textContent = role.name;
                        roleSelect.appendChild(option);
                    });
                } else {
                    Object.keys(roles).forEach(function (id) {
                        const option = document.createElement('option');
                        option.value = id;
                        option.textContent = roles[id];
                        roleSelect.appendChild(option);
                    });
                }

                if (selected) {
                    roleSelect.value = selected;
                }

                roleSelect.dataset.loaded = '1';
            })
            .catch(function () {
                roleSelect.dataset.loaded = '0';
            });
    }

    // Keep status single-select while keeping checkbox UI.
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

    if (roleSelect) {
        roleSelect.addEventListener('focus', loadRoles);
        roleSelect.addEventListener('mousedown', loadRoles);
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

            statusCheckboxes.forEach(function (checkbox) {
                checkbox.checked = false;
            });

            if (roleSelect) {
                roleSelect.value = '';
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
