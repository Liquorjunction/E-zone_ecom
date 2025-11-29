$(document).ready(function () {
    $('#tnc-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#tnc-table').data('route'),
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
