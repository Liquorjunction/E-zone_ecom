/**
 * Global Status Toggle Handler for DataTables
 * Handles AJAX PATCH requests for toggling active/inactive status.
 */
document.addEventListener('DOMContentLoaded', function() {

    // Use Event Delegation to handle toggles inside DataTables (even after pagination/sorting)
    document.body.addEventListener('change', function(e) {

        if (e.target && e.target.classList.contains('status-toggle')) {
            const input = e.target;
            const url = input.getAttribute('data-url');
            const isChecked = input.checked;
            const label = input.nextElementSibling; // Assuming label is immediately after input

            // 1. Optimistic UI Update (Make it feel instant)
            // if (label) {
            //     label.textContent = isChecked ? 'Active' : 'Inactive';
            // }

            // 2. Send AJAX Request
            axios.patch(url)
                .then(function (response) {
                    // Success Feedback
                    if (window.showToast) {
                        window.showToast(response.data.message || 'Status updated successfully!', 'success');
                    }
                    
                    // Reload DataTable to reflect the change from database
                    const $table = $(input).closest('table');
                    if ($table.length && $.fn.DataTable.isDataTable($table)) {
                        $table.DataTable().ajax.reload(null, false);
                    }
                })
                .catch(function (error) {
                    // Revert UI on Failure
                    input.checked = !isChecked; // Toggle back
                    if (label) {
                        label.textContent = !isChecked ? 'Active' : 'Inactive';
                    }

                    // Error Feedback
                    console.error(error);
                    if (window.showToast) {
                        window.showToast('Failed to update status. Please try again.', 'error');
                    }
                });
        }
    });
});
