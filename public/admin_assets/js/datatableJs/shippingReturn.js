$(document).ready(function () {
    var $table = $('#shipping-return-table');
    var dataRoute = $table.data('route');
    var hasAction = $table.data('hasAction') || $table.attr('data-has-action') == 1 || $table.attr('data-has-action') == '1';

    var columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'title', name: 'title' },
        { data: 'content', name: 'content' },
        { data: 'updated_at', name: 'updated_at' }
    ];

    if (hasAction) {
        columns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
    }

    $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: dataRoute,
        columns: columns,
        initComplete: function () {
            var info = this.api().page.info();
            if (info.recordsTotal >= 1) {
                $('.add-button').hide();
            } else {
                $('.add-button').show();
            }
        },
        drawCallback: function () {
            var info = this.api().page.info();
            if (info.recordsTotal >= 1) {
                $('.add-button').hide();
            } else {
                $('.add-button').show();
            }
        }
    });
});

