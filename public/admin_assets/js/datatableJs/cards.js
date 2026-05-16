$(document).ready(function () {
    var $table = $('#cards-table');
    if (!$table.length) {
        return;
    }

    var hasActions = $table.data('has-actions') == 1;
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

    var table = $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $table.data('route'),
            type: 'GET',
            data: function (d) {
                d.status = $("input[name='status']:checked").val() || 'all';
            }
        },
        columns: columns,
        order: [[1, 'asc']]
    });

    window.cardsDataTable = table;
});
