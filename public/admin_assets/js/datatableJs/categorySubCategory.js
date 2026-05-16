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

        // Helper to get filter data for AJAX
        function getCategoryFilterData(d) {
            d.status = $("input[name='status']:checked").val() || '';
        }

        var catTable = $(catTableSelector).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $(catTableSelector).data('route'),
                data: getCategoryFilterData
            },
            columns: catColumns
        });
        // Expose for filter bar JS
        window.categoryTable = catTable;
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

        // Helper to get filter data for AJAX
        function getSubCategoryFilterData(d) {
            d.status = $("input[name='status']:checked").val() || '';
        }

        var subCatTable = $(subCatTableSelector).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: $(subCatTableSelector).data('route'),
                data: getSubCategoryFilterData
            },
            columns: subCatColumns
        });
        // Expose for filter bar JS
        window.subcategoryTable = subCatTable;
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
