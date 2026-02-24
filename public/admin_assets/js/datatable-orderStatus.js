$(document).ready(function() {
    // Listen for clicks on the dropdown items
    $(document).on('click', '.change-order-status-action', function(e) {
        e.preventDefault();

        var $btn = $(this);
        var orderId = $btn.data('order-id');
        var newStatus = $btn.data('status');
        var updateUrl = $btn.data('url'); // Grab the route injected from Blade
        var statusText = $btn.text().trim();

        if (!orderId || !newStatus || !updateUrl) return;

        Swal.fire({
            title: 'Change Order Status',
            text: 'Are you sure you want to change the order status to ' + statusText + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, change it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: updateUrl,
                    method: 'POST',
                    data: {
                        id: orderId,
                        status: newStatus,
                        _token: $('meta[name="csrf-token"]').attr('content') // Requires CSRF token in app.blade layout
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('Success', response.message, 'success');

                            // Safely reload Yajra DataTable
                            if ($.fn.DataTable.isDataTable('.dataTable')) {
                                $('.dataTable').DataTable().ajax.reload(null, false);
                            } else {
                                location.reload();
                            }
                        } else {
                            Swal.fire('Error', response.message || 'Failed to update status', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to update status: ' + (xhr.responseJSON?.message || 'Unknown error'), 'error');
                    }
                });
            }
        });
    });
});
