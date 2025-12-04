$(document).ready(function() {
    $('#product-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#product-table').data('route'),
        columns: [
            { data: 'id', name: 'id' },
            {
                data: 'cover_image',
                name: 'cover_image',
                orderable: false,
                searchable: false,
                render: function(data) {
                    return '<img src="' + data + '" alt="Image" width="50" height="50" class="img-thumbnail">';
                }
            },
            { data: 'name', name: 'name' },
            {
                data: 'status',
                name: 'status',
                render: function(data) {
                    return data === 'active' ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>';
                }
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    var actions = '';
                    actions += '<a href="' + row.show_url + '" class="btn btn-sm btn-info">View</a> ';
                    actions += '<a href="' + row.edit_url + '" class="btn btn-sm btn-warning">Edit</a> ';
                    actions += '<button class="btn btn-sm btn-danger delete-btn" data-id="' + row.id + '" data-name="' + row.name + '">Delete</button>';
                    return actions;
                }
            }
        ]
    });
});
