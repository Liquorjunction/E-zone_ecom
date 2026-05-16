$(function() {
    var table = $('#napsLogTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#napsLogTable').data('source'),
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'order_id', name: 'order_id' },
            { data: 'api_type', name: 'api_type' },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ]
    });
});

