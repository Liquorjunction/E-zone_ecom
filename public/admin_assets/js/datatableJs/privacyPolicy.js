$(document).ready(function () {
    var table = $("#privacy-policy-table");
    var hasAction =
        table.attr('data-has-action') == 1 ||
        table.attr('data-has-action') == "1" ||
        table.data('hasAction') === 1 ||
        table.data('hasAction') === true;

    var columns = [
        {
            data: "DT_RowIndex",
            name: "DT_RowIndex",
            orderable: false,
            searchable: false,
        },
        { data: "title", name: "title" },
        { data: "description", name: "description" },
        { data: "updated_at", name: "updated_at" },
        { data: 'status', name: 'status', orderable: false, searchable: false },
    ];
    if (hasAction) {
        columns.push({
            data: "action",
            name: "action",
            orderable: false,
            searchable: false,
        });
    }

    table.DataTable({
        processing: true,
        serverSide: true,
        ajax: table.data("route"),
        columns: columns,
    });
});
