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

    var faqCategoriesTable = table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: table.data('route'),
            type: 'GET',
            data: function (d) {
                d.status = $("input[name='status']:checked").val() || 'all';
                d.start_date = $("input[name='start_date']").val() || '';
                d.end_date = $("input[name='end_date']").val() || '';
            }
        },
        columns: columns,
        order: [[1, 'asc']],
        pageLength: 10,
        responsive: true
    });

    window.faqCategoriesTable = faqCategoriesTable;
});
