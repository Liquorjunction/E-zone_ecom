// store.js - Handles DataTable for store index

$(function () {
    var table = $('#store-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#store-table').data('route'),
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'store_logo', name: 'store_logo', render: function(data) {
                if (data) {
                    return '<img src="'+data+'" height="50" width="50" style="object-fit:contain; border-radius:5px;">';
                }
                return '<span class="badge bg-secondary">No Logo</span>';
            }},
            {data: 'store_id', name: 'store_id'},
            {data: 'store_name', name: 'store_name'},
            {data: 'status', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
});
