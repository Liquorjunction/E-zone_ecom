 $(document).ready(function() {
    var $rolePermissionTable = $("#roles-table");
    var dataUrl = $rolePermissionTable.data("url");

    $rolePermissionTable.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            type: "GET",
        },
        columns: [
           { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
            { data: "name", name: "name" },
            { data: "guard_name", name: "guard_name" },
            { data: "action", name: "action", orderable: false, searchable: false },
        ],
        responsive: true,
        order: [1, "asc"],
    });
});
