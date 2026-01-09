$(document).ready(function () {
    var $table = $('#whyshopwithus-table');
    if (!$table.length) {
        return;
    }

    var dataRoute = $table.data('route');

    $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataRoute,
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'icon', name: 'icon', orderable: false, searchable: false ,
                render: function (data) {
                    if (data) {
                        return '<img src="/storage/' + data + '" alt="Icon" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: contain;">';
                    }
                    return '<span class="text-muted">No Icon</span>';
                }
            },
            { data: 'title', name: 'title' },
            { data: 'description', name: 'description', orderable: false, searchable: false,
                render: function (data) {
                    if (data && data.length > 100) {
                        return data.substring(0, 100) + '...';
                    }
                    return data || '';
                }
            },
            { data: 'status',name: 'status',orderable: false, searchable: false },
            { data: 'actions', name: 'actions',orderable: false, searchable: false }
        ],
        pageLength: 10,
        responsive: true,
        order: [[0, 'asc']]
    });
});
