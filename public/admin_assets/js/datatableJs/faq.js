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

    var faqsTable = table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: table.data('route'),
            type: 'GET',
            data: function (d) {
                d.status = $("input[name='status']:checked").val() || 'all';
                d.category_id = $("#faq-category-filter").val() || '';
                d.date_from = $("input[name='date_from']").val() || '';
                d.date_to = $("input[name='date_to']").val() || '';
            }
        },
        columns: columns,
    });

    window.faqsTable = faqsTable;
});
