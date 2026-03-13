$(document).ready(function () {
    var $table = $('#stockReportTable');

    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().destroy();
    }

    $table.DataTable({
        dom:
            "<'row align-items-center g-2 mb-3'" +
                "<'col-md-6 d-flex align-items-center gap-2 dt-toolbar-left'Bl>" +
                "<'col-md-6 dt-toolbar-right'f>" +
            ">" +
            "rt" +
            "<'row align-items-center g-2 mt-3'" +
                "<'col-md-6'i>" +
                "<'col-md-6 d-flex justify-content-end'p>" +
            ">",
        buttons: [
            {
                extend: 'csvHtml5',
                text: '<i class="fa fa-download me-1"></i>Export CSV',
                className: 'btn btn-primary btn-sm'
            }
        ],
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        order: [],
        language: {
            search: '',
            searchPlaceholder: 'Search stock report...',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            paginate: {
                previous: '<i class="fa fa-angle-left"></i>',
                next: '<i class="fa fa-angle-right"></i>'
            }
        },
        initComplete: function () {
            var $wrapper = $table.closest('.dataTables_wrapper');

            $wrapper.find('.dataTables_filter input')
                .addClass('form-control form-control-sm')
                .attr('aria-label', 'Search stock report table');

            $wrapper.find('.dataTables_length select')
                .addClass('form-select form-select-sm')
                .attr('aria-label', 'Select stock report rows per page');

            $wrapper.find('.dataTables_filter').addClass('d-flex justify-content-md-end justify-content-start');
            $wrapper.find('.dt-buttons').addClass('d-flex gap-2');
        }
    });
});
