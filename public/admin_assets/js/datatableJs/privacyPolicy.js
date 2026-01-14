$(document).ready(function () {
    $('#privacy-policy-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#privacy-policy-table').data('route'),
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'description', name: 'description' },
            { data: 'updated_at', name: 'updated_at' },
            // { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
