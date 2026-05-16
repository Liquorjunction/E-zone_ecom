$(document).ready(function () {
    var $table = $('#customers-table');
    var dataUrl = $table.data('route');

    var table = $table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: dataUrl,
            type: 'GET',
            data: function(d) {
                // Read filters from the current filter bar controls
                d.status = $('input[name="status"]:checked').val() || 'all';
                d.start_date = $('input[name="start_date"]').val() || '';
                d.end_date = $('input[name="end_date"]').val() || '';
                d.email_verified = $('#email_verified').is(':checked') ? 1 : '';
                console.log('Sending filters:', d);
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX Error:', xhr.responseText);
                console.error('Error:', error);
                console.error('Thrown:', thrown);
            }
        },
        columns: [
            { data: 'batch_checkbox', name: 'batch_checkbox', orderable: false, searchable: false, className: 'text-center', defaultContent: '' },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'created_at', name: 'created_at' },
            { data: 'phone', name: 'phone' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        drawCallback: function() {
            $('#select-all-customers').prop('checked', false);
        }
    });

    // Store table reference globally for filter access
    window.customersDataTable = table;

    // Initialize batch operations
    initBatchOperations({
        tableId: 'customers-table',
        selectAllCheckboxId: 'select-all-customers',
        itemCheckboxClass: 'customer-checkbox',
        itemName: 'customer'
    });
});
