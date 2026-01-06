$(function () {
    var $table = $("#coupons-table");
    if (!$table.length) return;

    $table.DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,
        ajax: $table.data("source"),
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
            { data: "name", name: "name" },
            { data: "code", name: "code" },
            { data: "coupon_type", name: "coupon_type" },
            { data: "start_date", name: "start_date" },
            { data: "end_date", name: "end_date" },
            { data: "status", name: "status", orderable: false, searchable: false },
            { data: "action", name: "action", orderable: false, searchable: false },
        ],
        order: [[1, "desc"]],
    });
});
