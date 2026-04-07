$(document).ready(function () {
    var table = $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $('#orders-table').data('route'),
            type: 'GET',
            data: function (d) {
                d.status = $('#status').val() || '';
                d.payment_method = $('#payment_method').val() || '';
                d.payment_status = $('#payment_status').val() || '';
                d.start_date = $("input[name='start_date']").val() || '';
                d.end_date = $("input[name='end_date']").val() || '';
            }
        },
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

    window.ordersTable = table;
});
