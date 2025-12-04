$(document).ready(function () {
    $('#shipping-return-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#shipping-return-table').data('route'),
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'title', name: 'title' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        initComplete: function () {
            var info = this.api().page.info();
            if (info.recordsTotal >= 1) {
                $('.add-button').hide();
            } else {
                $('.add-button').show();
            }
        },
        drawCallback: function () {
            var info = this.api().page.info();
            if (info.recordsTotal >= 1) {
                $('.add-button').hide();
            } else {
                $('.add-button').show();
            }
        }
    });
});

