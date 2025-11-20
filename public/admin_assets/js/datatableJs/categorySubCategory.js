/**
 * categorySubCategory.js
 * Handles DataTables for Categories and SubCategories index page.
 */

$(document).ready(function() {
    // --------------------------------------------------------------
    // 1. Initialize Category DataTable
    // --------------------------------------------------------------
    var catTableSelector = '#categories-table';

    if ($(catTableSelector).length) {
        // Destroy if exists to prevent duplication
        if ($.fn.DataTable.isDataTable(catTableSelector)) {
            $(catTableSelector).DataTable().destroy();
        }

        var catTable = $(catTableSelector).DataTable({
            processing: true,
            serverSide: true,
            ajax: $(catTableSelector).data('route'),
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                // Matches DB column 'category-name'
                { data: 'category-name', name: 'category-name' },
                // Matches Controller addColumn 'image'
                { data: 'image', name: 'image', orderable: false, searchable: false },
                // Matches Controller addColumn 'is_active' (HTML), sorts by DB 'status'
                { data: 'is_active', name: 'status', orderable: true, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    }

    // --------------------------------------------------------------
    // 2. Initialize SubCategory DataTable
    // --------------------------------------------------------------
    var subCatTableSelector = '#subcategories-table';

    if ($(subCatTableSelector).length) {
        if ($.fn.DataTable.isDataTable(subCatTableSelector)) {
            $(subCatTableSelector).DataTable().destroy();
        }

        var subCatTable = $(subCatTableSelector).DataTable({
            processing: true,
            serverSide: true,
            ajax: $(subCatTableSelector).data('route'),
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                // Matches Controller addColumn 'name'
                { data: 'name', name: 'sub-category-name' },
                // Matches Controller addColumn 'category.name'
                { data: 'category.name', name: 'category.category-name' },
                // Matches Controller addColumn 'image'
                { data: 'image', name: 'image', orderable: false, searchable: false },
                // Matches Controller addColumn 'is_active'
                { data: 'is_active', name: 'status', orderable: true, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    }

    // --------------------------------------------------------------
    // 3. Handle Tab Switching (Fixes Hidden DataTable Geometry)
    // --------------------------------------------------------------
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        if(typeof catTable !== 'undefined') {
            catTable.columns.adjust().draw();
        }
        if(typeof subCatTable !== 'undefined') {
            subCatTable.columns.adjust().draw();
        }
    });
});
