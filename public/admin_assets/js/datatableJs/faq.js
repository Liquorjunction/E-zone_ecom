$(document).ready(function () {
    var table = $("#faqs-table");
    var hasAction = table.data('hasAction') || table.attr('data-has-action') == 1 || table.attr('data-has-action') == '1';

    var columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'question', name: 'question' },
        { data: 'answer', name: 'answer' },
        { data: 'category', name: 'category' },
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
