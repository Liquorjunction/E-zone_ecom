$(function() {
    var table = $('#reviews-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: reviewDataUrl,
        columns: [
            { data: 'id', name: 'id' },
            { data: 'product', name: 'product.name', orderable: false, searchable: false },
            { data: 'user', name: 'user.name', orderable: false, searchable: false },
            { data: 'order', name: 'order.order_number', orderable: false, searchable: false },
            { data: 'rating', name: 'rating', orderable: false, searchable: false },
            { data: 'review_text', name: 'review_text', orderable: false, searchable: false },
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
            if (confirm('Are you sure you want to approve this review?')) {
                approveReview(reviewId, route, table);
            }
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to approve this review?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, approve it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                approveReview(reviewId, route, table);
            }
        });
    });

    // Reject Review
    $(document).on('click', '.decline-review-btn', function() {
        const reviewId = $(this).data('review-id');
        const route = $(this).data('route');
        
        if (typeof Swal === 'undefined') {
            if (confirm('Are you sure you want to reject this review?')) {
                rejectReview(reviewId, route, table);
            }
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to reject this review?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, reject it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                rejectReview(reviewId, route, table);
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
