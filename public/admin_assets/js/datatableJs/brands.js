$(document).ready(function () {
    // Get the flag from a data attribute on the table
    var hasAction = $('#brand-table').data('has-action');
    var columns = [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
        { data: 'image', name: 'image', orderable: false, searchable: false },
        { data: 'name', name: 'name', searchable: true },
        { data: 'status', name: 'status' }
    ];
    if (hasAction) {
        columns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
    }

    window.brandTable = $('#brand-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $('#brand-table').data('route'),
            data: function (d) {
                d.status = $("input[name='status']:checked").val();
            }
        },
        columns: columns
    });
});
