document.addEventListener('DOMContentLoaded', function() {

    document.body.addEventListener('change', function(e) {

        if (e.target && e.target.classList.contains('status-toggle')) {
            const input = e.target;
            const url = input.getAttribute('data-url');
            const isChecked = input.checked;
            const label = input.nextElementSibling;

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
        }
    });
});
