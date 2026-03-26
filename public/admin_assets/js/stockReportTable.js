$(document).ready(function () {
    var $table = $('#stockReportTable');

    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().destroy();
    }

    var childRowMap = {};
    $('#skuChildRowsData tbody tr.sku-child-row').each(function () {
        var pid = $(this).attr('data-sku-parent');
        if (!childRowMap[pid]) {
            childRowMap[pid] = [];
        }
        childRowMap[pid].push($(this).clone(true, true));
    });

    var dt = $table.DataTable({
        dom:
            "<'row align-items-center g-2 mb-3'" +
                "<'col-md-6 d-flex align-items-center gap-2 dt-toolbar-left'Bl>" +
                "<'col-md-6 dt-toolbar-right'f>" +
            ">" +
            "<'table-responsive'rt>" +
            "<'row align-items-center g-2 mt-3'" +
                "<'col-md-6'i>" +
                "<'col-md-6 d-flex justify-content-end'p>" +
            ">",
        buttons: [
            {
                extend: 'csvHtml5',
                text: '<i class="fa fa-download me-1"></i>Export CSV',
                className: 'btn btn-primary btn-sm',
                exportOptions: {
                    rows: '.sku-parent-row'
                }
            }
        ],
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
                next:     '<i class="fa fa-angle-right"></i>'
            }
        },
        initComplete: function () {
            var $wrapper = $table.closest('.dataTables_wrapper');

            $wrapper.find('.dataTables_filter input')
                .addClass('form-control form-control-sm')
                .attr('aria-label', 'Search stock report table');

            $wrapper.find('.dataTables_length select')
                .addClass('form-select form-select-sm')
                .attr('aria-label', 'Select rows per page');

            $wrapper.find('.dataTables_filter').addClass('d-flex justify-content-md-end justify-content-start');
            $wrapper.find('.dt-buttons').addClass('d-flex gap-2');
        }
    });

    // ── Expand / collapse on the button click ─────────────────────────────
    $table.on('click', '.expand-btn', function (e) {
        e.stopPropagation();

        var $btn  = $(this);
        var skuId = $btn.attr('data-sku-id');
        var $icon = $btn.find('i.fa');

        var $existing = $table.find('tbody tr.sku-child-row[data-sku-parent="' + skuId + '"]');

        if ($existing.length > 0) {
            if ($existing.first().is(':visible')) {
                $existing.hide();
                $icon.removeClass('fa-minus').addClass('fa-plus');
                $btn.removeClass('btn-primary').addClass('btn-outline-primary');
            } else {
                $existing.show();
                $icon.removeClass('fa-plus').addClass('fa-minus');
                $btn.removeClass('btn-outline-primary').addClass('btn-primary');
            }
        } else {
            var children = childRowMap[skuId];
            if (children && children.length > 0) {
                var $parentRow   = $btn.closest('tr');
                var $insertAfter = $parentRow;

                $.each(children, function (i, $child) {
                    var $clone = $child.clone(true, true);
                    $clone.insertAfter($insertAfter);
                    $insertAfter = $clone;
                });

                $icon.removeClass('fa-plus').addClass('fa-minus');
                $btn.removeClass('btn-outline-primary').addClass('btn-primary');
            }
        }
    });

    // On DataTable redraw (page change / search) remove injected rows and reset all buttons
    dt.on('draw', function () {
        $table.find('tbody tr.sku-child-row').remove();
        $table.find('.expand-btn')
            .removeClass('btn-primary')
            .addClass('btn-outline-primary')
            .find('i.fa')
            .removeClass('fa-minus')
            .addClass('fa-plus');
    });
});
