$(document).ready(function () {
    var $table = $('#about-us-table');
    var hasAction = $table.data('hasAction') || $table.attr('data-has-action') == 1 || $table.attr('data-has-action') == '1';
    var columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'title', name: 'title' },
        { data: 'description', name: 'description' },
        { data: 'created_at', name: 'created_at' },
        // { data: 'status', name: 'status' },
    ];
    if (hasAction) {
        columns.push({ data: 'actions', name: 'actions', orderable: false, searchable: false });
    }
    $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: $table.data('route'),
        columns: columns
    });
});
