$(document).ready(function () {
    $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#orders-table').data('route'),
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'order_number', name: 'order_number' },
            { data: 'created_at', name: 'created_at' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'total', name: 'total' },
            { data: 'status', name: 'status' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
