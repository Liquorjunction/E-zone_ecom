$(function () {
    "use strict";

    if (!$('#inventory-table').length) return;

    window.inventoryTable = $('#inventory-table').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        ajax: {
            url:  $('#inventory-table').data('route'),
            type: 'GET',
            data: function (d) {
                d.exclude_sold  = $('#exclude_sold').is(':checked') ? 1 : '';
                d.exclude_completely_sold = $('#exclude_completely_sold').is(':checked') ? 1 : '';
                d.location_type = $('#location_type').val();
                d.location_id   = $('#location_id').val();
                d.date_from     = $('#date_from').val();
                d.date_to       = $('#date_to').val();
            },
            error: function (xhr, error, thrown) {
                console.error('DataTables Error:', error, thrown);
            }
        },
        columns: [
            { data: 'DT_RowIndex',    name: 'DT_RowIndex',    orderable: false, searchable: false },
            { data: 'batch_no',       name: 'batch_no',        searchable: true },
            { data: 'created_at',     name: 'created_at' },
            { data: 'location',       name: 'location',        orderable: false, searchable: false },
            { data: 'items_summary',  name: 'items_summary',   searchable: false },
            { data: 'total_quantity', name: 'total_quantity',   searchable: false },
            { data: 'subtotal_cost',  name: 'subtotal_cost',    searchable: false },
            { data: 'action',         name: 'action',           orderable: false, searchable: false, className: 'text-center' }
        ],
        order: [[2, 'desc']],
    });
});
