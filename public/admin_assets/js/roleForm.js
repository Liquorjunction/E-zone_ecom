// public/admin_assets/js/roleForm.js

$(document).ready(function() {
    // 1. Initialize DataTable
    $('#permissions-table').DataTable({
        paging: false,
        ordering: false,
        info: false,
        searching: true,
        language: {
            searchPlaceholder: "Search modules...",
            search: ""
        },
        dom: '<"row mb-3"<"col-12"f>>t'
    });

    // 2. "Select All" Clicked -> Toggle Rows
    $(document).on('change', '.select-all-row', function() {
        const moduleClass = $(this).data('module');
        const isChecked = $(this).prop('checked');
        $('.' + moduleClass).prop('checked', isChecked);
    });

    // 3. Child Clicked -> Update "Select All" Status
    $(document).on('change', '.perm-checkbox', function() {
        const classList = $(this).attr('class').split(/\s+/);
        const moduleClass = classList.find(cls => cls.startsWith('row-'));

        if (moduleClass) {
            const totalInRow = $('.' + moduleClass).length;
            const checkedInRow = $('.' + moduleClass + ':checked').length;
            const selectAllBtn = $('.select-all-row[data-module="' + moduleClass + '"]');

            selectAllBtn.prop('checked', totalInRow === checkedInRow);
        }
    });

    // 4. Page Load Initialization (Critical for Edit Page)
    $('.select-all-row').each(function() {
        const moduleClass = $(this).data('module');
        const totalInRow = $('.' + moduleClass).length;
        const checkedInRow = $('.' + moduleClass + ':checked').length;

        if (totalInRow > 0 && totalInRow === checkedInRow) {
            $(this).prop('checked', true);
        }
    });
});
