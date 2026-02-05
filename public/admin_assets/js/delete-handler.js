$(document).ready(function() {
    // Generic delete action handler with SweetAlert
    $(document).on('click', '.delete-action', function(e) {
        e.preventDefault();
        var deleteUrl = $(this).data('delete-url');
        var deleteId = $(this).data('delete-id');

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: "Once you delete this item it cannot be reverted?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    performDelete(deleteUrl, deleteId);
                }
            });
        } else {
            if (confirm('Are you sure you want to delete this item?')) {
                performDelete(deleteUrl, deleteId);
            }
        }
    });

    function performDelete(deleteUrl, deleteId) {
        $.ajax({
            url: deleteUrl,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: deleteId
            },
            success: function (response) {
                console.log('Delete response:', response);
                
                if (response.success === true || response.success === 'true') {
                    // Show success message
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: response.message || 'Item deleted successfully.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        alert(response.message || 'Item deleted successfully.');
                    }
                    
                    // Reload DataTable after a short delay
                    setTimeout(function() {
                        reloadDataTable();
                    }, 500);
                } else {
                    // Handle failure response
                    const message = response.message || 'Failed to delete item.';
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: message
                        });
                    } else {
                        alert('Failed to delete: ' + message);
                    }
                }
            },
            error: function (xhr) {
                console.error('Delete error:', xhr);
                let message = 'An error occurred while deleting the item.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                } else if (xhr.statusText) {
                    message = xhr.statusText;
                }
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: message
                    });
                } else {
                    alert('Error: ' + message);
                }
            }
        });
    }

    function reloadDataTable() {
        // Try to reload any active DataTable on the page
        try {
            var tables = $.fn.dataTable.fnTables();
            if (tables && tables.length > 0) {
                // Reload first DataTable found
                $(tables[0]).DataTable().ajax.reload(null, false);
            } else {
                // Fallback: reload page if no DataTable found
                location.reload();
            }
        } catch (e) {
            console.log('DataTable reload failed, reloading page:', e);
            location.reload();
        }
    }
});
