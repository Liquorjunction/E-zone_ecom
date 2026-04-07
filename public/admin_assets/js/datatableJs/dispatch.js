$(document).ready(function () {
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

    window.dispatchTable = table;
});
