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
        ajax: dataUrl,
        columns: columns,
        order: [[4, 'asc']],
        pageLength: 10,
        responsive: true,
        drawCallback: function() {
            // Reset select-all checkbox after table redraw
            $('#select-all-sliders').prop('checked', false);
        }
    });

    // Initialize batch operations
    initBatchOperations({
        tableId: 'slider-list-table',
        selectAllCheckboxId: 'select-all-sliders',
        itemCheckboxClass: 'slider-checkbox',
        itemName: 'slider'
    });
    // Handle status toggle
    $table.on('change', '.status-toggle', function () {
        var $checkbox = $(this);
        var url = $checkbox.data('url');
        if (!url) return;

        // Prevent toggle if disabled
        if ($checkbox.is(':disabled')) return;

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: window.Laravel && window.Laravel.csrfToken ? window.Laravel.csrfToken : $('meta[name="csrf-token"]').attr('content'),
            },
            success: function (response) {
                if (response.success) {
                    // Optionally show a toast or notification
                    // Reload the table to reflect changes
                    $table.DataTable().ajax.reload(null, false);
                } else {
                    alert(response.message || 'Failed to update status.');
                    $table.DataTable().ajax.reload(null, false);
                }
            },
            error: function (xhr) {
                var msg = 'Failed to update status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                alert(msg);
                $table.DataTable().ajax.reload(null, false);
            }
        });
    });
});

