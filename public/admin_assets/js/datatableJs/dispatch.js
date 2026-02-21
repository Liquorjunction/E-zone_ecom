$(document).ready(function () {
    // Initialize Yajra DataTable
    var ajaxUrl = $('#dispatchTable').data('url');
    var table = $('#dispatchTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: ajaxUrl,
            data: function (d) {
                d.status = $('#filter_status').val();
                d.order_number = $('#filter_order_number').val();
            }
        },
        columns: [
            { data: 'dispatch_id', name: 'id' },
            { data: 'order_number', name: 'order.order_number' },
            { data: 'product', name: 'product', orderable: false, searchable: false },
            { data: 'location', name: 'location', orderable: false, searchable: false },
            { data: 'status_badge', name: 'status' },
            { data: 'date', name: 'dispatched_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-end' }
        ],
        order: [[0, 'desc']],
        language: {
            searchPlaceholder: 'Search Dispatch ID...',
            paginate: {
                previous: '<i class="bi bi-chevron-left"></i>',
                next: '<i class="bi bi-chevron-right"></i>'
            }
        }
    });

    // Custom Filters Event Listeners (Triggers an AJAX reload)
    $('#filter_status, #filter_order_number').on('keyup change', function () {
        table.draw();
    });
    
    // Clear Filters Button
    $('#clear_filters').on('click', function(e) {
        e.preventDefault();
        $('#filter_status').val('');
        $('#filter_order_number').val('');
        table.search(''); // clear global search
        table.draw();
    });
});