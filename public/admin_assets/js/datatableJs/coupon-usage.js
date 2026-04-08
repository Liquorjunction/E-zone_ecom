$(function () {
    var $table = $("#coupon-usage-table");
    if (!$table.length) return;

    $table.DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,
        ajax: $table.data("source"),
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
            { data: "coupon_name", name: "coupon_name", searchable: false },
            { data: "coupon_code", name: "coupon_code", searchable: true },
            { data: "user_name", name: "user_name", searchable: false },
            { data: "user_email", name: "user_email", searchable: false },
            { data: "order_id", name: "order_id", searchable: false },
            { data: "used_at", name: "used_at", orderable: true },
        ],
        order: [[6, "desc"]],
    });
});
