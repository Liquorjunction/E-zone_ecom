$(document).ready(function () {
    var table = $("#faqcategories-table");
    var hasAction = table.data('has-actions') || table.attr('data-has-actions') == 1 || table.attr('data-has-actions') == '1';

    var columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'status', name: 'status' },
    ];

    if (hasAction) {
        columns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
    }

    table.DataTable({
        processing: true,
        serverSide: true,
        ajax: table.data('route'),
        columns: columns,
    });
});
