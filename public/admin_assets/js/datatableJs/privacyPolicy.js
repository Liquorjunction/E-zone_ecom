$(document).ready(function () {
    $('#privacy-policy-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#privacy-policy-table').data('route'),
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'description', name: 'description' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
