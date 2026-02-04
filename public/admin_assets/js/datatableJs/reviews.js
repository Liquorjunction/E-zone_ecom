$(function() {
    window.reviewsDataTable = $('#reviews-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: reviewDataUrl,
            data: function(d) {
                // Add filter parameters
                d.status = $('#status').val();
                d.date_from = $('#date_from').val();
                d.date_to = $('#date_to').val();
                d.rating = $('#rating').val();
            }
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'product', name: 'product.name', orderable: false, searchable: false },
            { data: 'review_text', name: 'review_text', orderable: false, searchable: false },
            { data: 'user', name: 'user.name', orderable: false, searchable: false },
            { data: 'order', name: 'order.order_number', orderable: false, searchable: false },
            { data: 'rating', name: 'rating', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'approval_actions', name: 'approval_actions', orderable: false, searchable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ],
        order: [[0, 'desc']],
        responsive: true,
        autoWidth: false,
    });

    // Approve Review
    $(document).on('click', '.approve-review-btn', function() {
        const reviewId = $(this).data('review-id');
        const route = $(this).data('route');

        if (typeof Swal === 'undefined') {
            if (confirm('Are you sure Once you approve this review it can not be reverted?')) {
                approveReview(reviewId, route, window.reviewsDataTable);
            }
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "Once you approve this review it can not be reverted?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, approve it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                approveReview(reviewId, route, window.reviewsDataTable);
            }
        });
    });

    // Reject Review
    $(document).on('click', '.decline-review-btn', function() {
        const reviewId = $(this).data('review-id');
        const route = $(this).data('route');

        if (typeof Swal === 'undefined') {
            if (confirm('Are you sure Once you reject this review it can not be reverted?')) {
                rejectReview(reviewId, route, window.reviewsDataTable);
            }
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "Once you reject this review it can not be reverted?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, reject it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                rejectReview(reviewId, route, window.reviewsDataTable);
            }
        });
    });
});

function approveReview(reviewId, route, table) {
    $.ajax({
        url: route,
        type: 'POST',
        data: {
            id: reviewId,
            _token: csrfToken
        },
        success: function(response) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert(response.message);
            }
            table.ajax.reload(null, false);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Failed to approve review.';
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

function rejectReview(reviewId, route, table) {
    $.ajax({
        url: route,
        type: 'POST',
        data: {
            id: reviewId,
            _token: csrfToken
        },
        success: function(response) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert(response.message);
            }
            table.ajax.reload(null, false);
        },
        error: function(xhr) {
            const message = xhr.responseJSON?.message || 'Failed to reject review.';
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
