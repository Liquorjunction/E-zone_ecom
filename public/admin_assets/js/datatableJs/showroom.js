// showroom-datatable.js - Handles DataTable for showroom index

$(function () {
    var table = $('#showroom-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#showroom-table').data('route'),
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'cover_image', name: 'cover_image', render: function(data) {
                return '<img src="'+data+'" height="50" width="50" style="object-fit:cover; border-radius:5px;">';
            }},
            {data: 'name', name: 'name'},
            {data: 'description', name: 'description'},
            {data: 'is_active', name: 'status'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
});
