$(document).ready(function () {
    var $table = $('#about-us-table');
    if (!$table.length) {
        return;
    }

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

    var dataTable = $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $table.data('route'),
            type: 'GET',
            data: function (d) {
                d.status = $("input[name='status']:checked").val() || 'all';
                d.date_from = $("input[name='date_from']").val() || '';
                d.date_to = $("input[name='date_to']").val() || '';
            }
        },
        columns: columns,
        pageLength: 10,
        responsive: true
    });

    window.aboutUsDataTable = dataTable;
});
