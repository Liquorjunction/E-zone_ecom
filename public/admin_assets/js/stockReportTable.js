$(document).ready(function () {
    var $table = $('#stockReportTable');
    var dataUrl = $table.data('url');

    if ($.fn.DataTable.isDataTable($table)) {
        $table.DataTable().destroy();
    }

    var dt = $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            type: 'GET'
        },
        columns: [
            {
                data: 'expand_button',
                name: 'expand_button',
                orderable: false,
                searchable: false,
                className: 'text-center align-middle expand-cell'
            },
            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false,
                className: 'align-middle'
            },
            { data: 'sku_html', name: 'sku', className: 'align-middle' },
            { data: 'product_name', name: 'product_name', className: 'align-middle fw-semibold' },
            { data: 'variant_name', name: 'variant_name', className: 'align-middle' },
            { data: 'brand_name', name: 'brand_name', className: 'align-middle' },
            { data: 'category_name', name: 'category_name', className: 'align-middle' },
            { data: 'sub_category_name', name: 'sub_category_name', className: 'align-middle' },
            {
                data: 'total_qty_badge',
                name: 'total_qty',
                className: 'align-middle text-center'
            },
            {
                data: 'sold_qty_badge',
                name: 'sold_qty',
                className: 'align-middle text-center'
            },
            {
                data: 'reserved_qty_badge',
                name: 'reserved_qty',
                className: 'align-middle text-center'
            },
            {
                data: 'available_qty_badge',
                name: 'available_qty',
                className: 'align-middle text-center'
            }
        ],
        createdRow: function (row, data) {
            $(row)
                .addClass('sku-parent-row')
                .attr('data-sku-id', data.row_key)
                .attr('data-has-children', data.has_children ? '1' : '0');

            if (data.has_children) {
                $(row).addClass('has-children');
            }
        },
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

    function childRowMarkup(child) {
        var locationHtml = '<span class="text-muted">-</span>';

        if (child.location_type === 'Warehouse') {
            locationHtml = '<span class="badge bg-primary">Warehouse</span> '
                + '<span class="text-muted small">' + (child.location_name || 'N/A') + '</span>';
        } else if (child.location_type === 'Store') {
            locationHtml = '<span class="badge bg-success">Store</span> '
                + '<span class="text-muted small">' + (child.location_name || 'N/A') + '</span>';
        }

        return '<tr class="sku-child-row" data-sku-parent="' + child.parent_key + '">' +
            '<td></td>' +
            '<td></td>' +
            '<td><span class="child-batch-badge"><i class="bi bi-arrow-return-right me-1"></i>' + (child.batch_no || 'N/A') + '</span></td>' +
            '<td>' + (child.product_name || 'N/A') + '</td>' +
            '<td>' + (child.variant_name || 'N/A') + '</td>' +
            '<td>' + (child.brand_name || 'N/A') + '</td>' +
            '<td>' + locationHtml + '</td>' +
            '<td>-</td>' +
            '<td>' + (child.total_qty || 0) + '</td>' +
            '<td>' + (child.sold_qty || 0) + '</td>' +
            '<td>' + (child.reserved_qty || 0) + '</td>' +
            '<td>' + (child.available_qty || 0) + '</td>' +
        '</tr>';
    }

    $table.on('click', '.expand-btn', function (e) {
        e.stopPropagation();

        var $btn  = $(this);
        var skuId = $btn.attr('data-sku-id');
        var $icon = $btn.find('i.bi');

        var $existing = $table.find('tbody tr.sku-child-row[data-sku-parent="' + skuId + '"]');

        if ($existing.length > 0) {
            if ($existing.first().is(':visible')) {
                $existing.hide();
                $icon.removeClass('bi-chevron-double-up').addClass('bi-chevron-double-down');
                $btn.removeClass('btn-primary').addClass('btn-outline-primary');
            } else {
                $existing.show();
                $icon.removeClass('bi-chevron-double-down').addClass('bi-chevron-double-up');
                $btn.removeClass('btn-outline-primary').addClass('btn-primary');
            }
        } else {
            var rowData = dt.row($btn.closest('tr')).data();
            var children = (rowData && rowData.child_rows) ? rowData.child_rows : [];

            if (children.length > 0) {
                var $parentRow = $btn.closest('tr');
                var $insertAfter = $parentRow;

                $.each(children, function (i, child) {
                    var childWithParent = $.extend({}, child, { parent_key: skuId });
                    var $childRow = $(childRowMarkup(childWithParent));
                    $childRow.insertAfter($insertAfter);
                    $insertAfter = $childRow;
                });

                $icon.removeClass('bi-chevron-double-down').addClass('bi-chevron-double-up');
                $btn.removeClass('btn-outline-primary').addClass('btn-primary');
            }
        }
    });

    dt.on('draw', function () {
        $table.find('tbody tr.sku-child-row').remove();
        $table.find('.expand-btn')
            .removeClass('btn-primary')
            .addClass('btn-outline-primary')
            .find('i.bi')
            .removeClass('bi-chevron-double-up')
            .addClass('bi-chevron-double-down');
    });
});
