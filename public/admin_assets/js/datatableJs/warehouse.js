$(function () {
    var table = $('#warehouses-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#warehouses-table').data('route'),
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'warehouse_name', name: 'warehouse_name'},
            {data: 'warehouse_id', name: 'warehouse_id'},
            {data: 'location', name: 'location'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });
});
