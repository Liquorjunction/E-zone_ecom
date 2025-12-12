$(function () {
    $('#inventory-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#inventory-table').data('route'),
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'product', name: 'product' },
            { data: 'warehouse', name: 'warehouse' },
            { data: 'quantity', name: 'quantity' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
