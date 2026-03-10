$(document).ready(function () {
    $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#orders-table').data('route'),
        columns: [
            { data: 'order_number', name: 'order_number' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'email', name: 'email' },
            { data: 'total', name: 'total' },
            { data: 'created_at', name: 'created_at' },
            { data: 'status', name: 'status' },
            { data: 'cancelled_by', name: 'cancelled_by' },
            { data: 'payment_method', name: 'payment_method' },
            { data: 'payment_status', name: 'payment_status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
