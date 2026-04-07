$(document).ready(function () {
    var $table = $('#slider-list-table');
    var dataUrl = $table.data('url');
    var hasAction = $table.data('hasAction') || $table.attr('data-has-action') == 1 || $table.attr('data-has-action') == '1';

    var columns = [
        { data: 'batch_checkbox', name: 'batch_checkbox', orderable: false, searchable: false },
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'title', name: 'title' },
        {
            data: 'image',
            name: 'image',
            orderable: false,
            searchable: false,
            render: function (data, type, row) {
                if (data) {
                    return '<img src="' + data + '" alt="Slider Image" class="img-thumbnail" style="width: 50px; height: 50px;">';
                }
                return 'No Image';
            }
        },
        {
            data: 'is_active',
            name: 'is_active',
            orderable: false,
            searchable: false
        },
        { data: 'sort_order', name: 'sort_order' }
    ];

    if (hasAction) {
        columns.push({
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
        });
    }

    var table = $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            type: 'GET',
            data: function (d) {
                d.status = $("input[name='status']:checked").val() || 'all';
            }
        },
        columns: columns,
        order: [[4, 'asc']],
        pageLength: 10,
        responsive: true,
        drawCallback: function() {
            // Reset select-all checkbox after table redraw
            $('#select-all-sliders').prop('checked', false);
        }
    });

    // Expose DataTable for filter bar interactions
    window.sliderDataTable = table;

    // Initialize batch operations
    initBatchOperations({
        tableId: 'slider-list-table',
        selectAllCheckboxId: 'select-all-sliders',
        itemCheckboxClass: 'slider-checkbox',
        itemName: 'slider'
    });
});

