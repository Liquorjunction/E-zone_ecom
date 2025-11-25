$(document).ready(function () {
    $('#slider-list-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#slider-list-table').data('url'),
        columns: [
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
        order: [[4, 'asc']], // Default sort by sort_order
        pageLength: 10,
        responsive: true
    });
});
