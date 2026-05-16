// showroom-datatable.js - Handles DataTable for showroom index

$(function () {
    var $table = $("#showroom-table");
    var hasAction =
        $table.data("hasAction") ||
        $table.attr("data-has-action") == 1 ||
        $table.attr("data-has-action") == "1";
    var dataUrl = $table.data("route");

    var columns = [
        {
            data: "DT_RowIndex",
            name: "DT_RowIndex",
            orderable: false,
            searchable: false,
        },
        {
            data: "cover_image",
            name: "cover_image",
            render: function (data) {
                return (
                    '<img src="' +
                    data +
                    '" height="50" width="50" style="object-fit:cover; border-radius:5px;">'
                );
            },
        },
        { data: "name", name: "name" },
        { data: "description", name: "description" },
        { data: "is_active", name: "status" },
    ];

    if (hasAction) {
        columns.push({
            data: "action",
            name: "action",
            orderable: false,
            searchable: false,
        });
    }

    $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            type: "GET",
        },
        columns: columns,
    });
});
