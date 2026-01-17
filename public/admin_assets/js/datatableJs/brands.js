$(document).ready(function () {
    // Get the flag from a data attribute on the table
    var hasAction = $('#brand-table').data('has-action');
    var columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'image', name: 'image', orderable: false, searchable: false },
        { data: 'name', name: 'name' },
        { data: 'status', name: 'status' }
    ];
    if (hasAction) {
        columns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
    }
    $('#brand-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#brand-table').data('route'),
        columns: columns
    });
});
