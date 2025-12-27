$(document).ready(function () {
    $('#faqs-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#faqs-table').data('route'),
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'question', name: 'question' },
            { data: 'answer', name: 'answer' },
            { data: 'category', name: 'category' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
