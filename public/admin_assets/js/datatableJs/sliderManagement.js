$(document).ready(function () {
    var table = $('#slider-list-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#slider-list-table').data('url'),
        columns: [
            { data: 'batch_checkbox', name: 'batch_checkbox', orderable: false, searchable: false },
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
            { data: 'sort_order', name: 'sort_order' },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
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
});

