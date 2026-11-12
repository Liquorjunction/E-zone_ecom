$(document).ready(function () {
    var table = $('#orders-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        dom:
            "<'row align-items-center g-2 mb-3'" +
                "<'col-md-6 d-flex align-items-center gap-2 dt-toolbar-left'l>" +
                "<'col-md-6 dt-toolbar-right'f>" +
            ">" +
            "<'table-responsive'rt>" +
            "<'row align-items-center g-2 mt-3'" +
                "<'col-md-6'i>" +
                "<'col-md-6 d-flex justify-content-end'p>" +
            ">",
        ajax: {
            url: $('#orders-table').data('route'),
            type: 'GET',
            data: function (d) {
                d.status = $('#status').val() || '';
                d.client_type = $('#client_type').val() || '';
                d.payment_method = $('#payment_method').val() || '';
                d.payment_status = $('#payment_status').val() || '';
                d.start_date = $("input[name='start_date']").val() || '';
                d.end_date = $("input[name='end_date']").val() || '';
            }
        },
        columns: [
            { data: 'order_number', name: 'order_number', className: 'text-nowrap' },
            { data: 'customer_name', name: 'customer_name', className: 'text-nowrap' },
            { data: 'client_type', name: 'client_type', className: 'text-center text-nowrap' },
            { data: 'email', name: 'email', className: 'text-nowrap' },
            { data: 'total', name: 'total', className: 'text-nowrap' },
            { data: 'created_at', name: 'created_at', className: 'text-nowrap' },
            { data: 'status', name: 'status', className: 'text-nowrap' },
            { data: 'cancelled_by', name: 'cancelled_by', className: 'text-nowrap' },
            { data: 'payment_method', name: 'payment_method', className: 'text-nowrap' },
            { data: 'payment_status', name: 'payment_status', className: 'text-nowrap' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center text-nowrap' }
        ]
    });

    window.ordersTable = table;
});
