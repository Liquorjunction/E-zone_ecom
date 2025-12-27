$(function () {
    "use strict";

    if ($('#inventory-table').length) {
        $('#inventory-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: $('#inventory-table').data('route'),
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'batch_no', name: 'batch_no' },
                { data: 'created_at', name: 'created_at' },
                { data: 'location', name: 'location', orderable: false },
                { data: 'items_summary', name: 'items_summary', searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[2, 'desc']] // Sort by Date desc
        });
    }
});
