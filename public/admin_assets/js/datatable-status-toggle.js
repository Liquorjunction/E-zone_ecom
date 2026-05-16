document.addEventListener('DOMContentLoaded', function() {

    document.body.addEventListener('change', function(e) {

        if (e.target && e.target.classList.contains('status-toggle')) {
            const input = e.target;
            const url = input.getAttribute('data-url');
            const isChecked = input.checked;
            const label = input.nextElementSibling;

            // Check if axios is available, otherwise use fetch
            if (typeof axios !== 'undefined') {
                // Use axios if available
                axios.patch(url)
                    .then(function (response) {
                        if (window.showToast) {
                            window.showToast(response.data.message || 'Status updated successfully!', 'success');
                        }
                        const $table = $(input).closest('table');
                        if ($table.length && $.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().ajax.reload(null, false);
                        }
                    })
                    .catch(function (error) {
                        input.checked = !isChecked;
                        if (label) {
                            label.textContent = !isChecked ? 'Active' : 'Inactive';
                        }

                        console.error(error);
                        if (window.showToast) {
                            window.showToast('Failed to update status. Please try again.', 'error');
                        }
                    });
            } else {
                // Fallback to fetch if axios is not available
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(function (data) {
                        if (window.showToast) {
                            window.showToast(data.message || 'Status updated successfully!', 'success');
                        }
                        const $table = $(input).closest('table');
                        if ($table.length && $.fn.DataTable.isDataTable($table)) {
                            $table.DataTable().ajax.reload(null, false);
                        }
                    })
                    .catch(function (error) {
                        input.checked = !isChecked;
                        if (label) {
                            label.textContent = !isChecked ? 'Active' : 'Inactive';
                        }

                        console.error(error);
                        if (window.showToast) {
                            window.showToast('Failed to update status. Please try again.', 'error');
                        }
                    });
            }
        }
    });
});
