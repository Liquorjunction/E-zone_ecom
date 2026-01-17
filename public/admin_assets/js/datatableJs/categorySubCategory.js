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
        if ($.fn.DataTable.isDataTable(catTableSelector)) {
            $(catTableSelector).DataTable().destroy();
        }

        // Build columns dynamically so we can omit the action column when not allowed
        var catColumns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'category-name', name: 'category-name' },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'is_active', name: 'status', orderable: true, searchable: false }
        ];

        var hasCatActions = $(catTableSelector).data('has-actions');
        if (hasCatActions === 1 || hasCatActions === '1' || hasCatActions === true) {
            catColumns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
        }

        var catTable = $(catTableSelector).DataTable({
            processing: true,
            serverSide: true,
            ajax: $(catTableSelector).data('route'),
            columns: catColumns
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

        // Build columns dynamically so we can omit the action column when not allowed
        var subCatColumns = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'sub-category-name' },

            // FIXED: Changed data from 'category.name' to 'category_name'
            { data: 'category_name', name: 'category_name', orderable: false, searchable: false },

            { data: 'is_active', name: 'status', orderable: true, searchable: false }
        ];

        var hasSubCatActions = $(subCatTableSelector).data('has-actions');
        if (hasSubCatActions === 1 || hasSubCatActions === '1' || hasSubCatActions === true) {
            subCatColumns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
        }

        var subCatTable = $(subCatTableSelector).DataTable({
            processing: true,
            serverSide: true,
            ajax: $(subCatTableSelector).data('route'),
            columns: subCatColumns
        });
    }

    // --------------------------------------------------------------
    // 3. Handle Tab Switching
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
