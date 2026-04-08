$(document).ready(function () {
    var $table = $('#newsletters-table');
    window.newsletterTable = $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $table.data('url'),
            type: 'GET',
            data: function (d) {
                d.status = $('.newsletter-status-filter:checked').val() || '';
                d.date_from = $('#filter_date_from').val() || '';
                d.date_to = $('#filter_date_to').val() || '';
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'email', name: 'email' },
            { data: 'created_at', name: 'created_at' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        order: [[0, 'desc']],
    });
});
