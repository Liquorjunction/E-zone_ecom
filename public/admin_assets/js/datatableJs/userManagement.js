$(document).ready(function () {
    var $userListTable = $("#user-list-table");
    var dataUrl = $userListTable.data("url");

    window.userListTable = $userListTable.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            type: "GET",
            data: function (d) {
                d.status = $('.user-status-filter:checked').val() || '';
                d.role_id = $('#role').val() || '';
                d.date_from = $('#date_from').val() || '';
                d.date_to = $('#date_to').val() || '';
            }
        },
        columns: [
            { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
            {
                data: "profile_image",
                name: "profile_image",
                orderable: false,
                searchable: false,
            },
            { data: "name", name: "name" },
            { data: "role", name: "role" },
            { data: "email", name: "email" },
            {
                data: "status",
                name: "status",
            },
            {
                data: "created_at",
                name: "created_at",
            },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
            },
        ],
        dom: '<"row align-items-center"<"col-md-6" l><"col-md-6" f>><"table-responsive border-bottom my-3" rt><"row align-items-center" <"col-md-6" i><"col-md-6" p>><"clear">',
        pageLength: 10,
        responsive: true,
    });
});
