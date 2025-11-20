$(document).ready(function () {
    // 1. Categories DataTable
    var categoryTable = $("#categories-table");
    if (categoryTable.length) {
        categoryTable.DataTable({
            processing: true,
            serverSide: true,
            // Fetch URL from the HTML data-url attribute
            ajax: categoryTable.data("url"),
            columns: [
                { data: "DT_RowIndex", orderable: false, searchable: false },
                { data: "category-name", name: "category-name" },
                {
                    data: "category-icon",
                    name: "category-icon",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "status",
                    name: "status",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                },
            ],
        });
    }

    // 2. Subcategories DataTable
    var subCategoryTable = $("#subcategories-table");
    if (subCategoryTable.length) {
        subCategoryTable.DataTable({
            processing: true,
            serverSide: true,
            // Fetch URL from the HTML data-url attribute
            ajax: subCategoryTable.data("url"),
            columns: [
                { data: "DT_RowIndex", orderable: false, searchable: false },
                { data: "sub-category-name", name: "sub-category-name" },
                {
                    data: "category.category-name",
                    name: "category.category-name",
                    defaultContent: "N/A",
                },
                {
                    data: "sub-category-icon",
                    name: "sub-category-icon",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "status",
                    name: "status",
                    orderable: false,
                    searchable: false,
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                },
            ],
        });
    }
});
