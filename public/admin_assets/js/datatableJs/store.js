$(function () {
    var table = $("#store-table");
    var hasAction =
        table.data("hasAction") ||
        table.attr("data-has-action") == 1 ||
        table.attr("data-has-action") == "1";

    var columns = [
        {
            data: "DT_RowIndex",
            name: "DT_RowIndex",
            orderable: false,
            searchable: false,
        },
        {
            data: "store_logo",
            name: "store_logo",
            render: function (data) {
                if (data) {
                    return (
                        '<img src="' +
                        data +
                        '" height="50" width="50" style="object-fit:contain; border-radius:5px;">'
                    );
                }
                return '<span class="badge bg-secondary">No Logo</span>';
            },
        },
        // { data: "store_id", name: "store_id" },
        { data: "store_name", name: "store_name" },
        { data: "location", name: "location" },
        { data: "status", name: "status" },
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
