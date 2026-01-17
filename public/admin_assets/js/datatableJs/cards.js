$(document).ready(function () {
    var hasActions = $('#cards-table').data('has-actions') === 1;
    var columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'image', name: 'image', orderable: false, searchable: false },
        { data: 'title', name: 'title' },
        { data: 'description', name: 'description' },
        { data: 'status', name: 'status' },
    ];

    if (hasActions) {
        columns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
    }

    $('#cards-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#cards-table').data('route'),
        columns: columns,
        order: [[1, 'asc']]
    });
});
