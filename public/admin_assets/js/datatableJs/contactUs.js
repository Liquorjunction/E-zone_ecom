$(function() {
    window.contactUsTable = $('#contactUsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $('#contactUsTable').data('source'),
            data: function (d) {
                var status = $('input[name="status"]:checked').val() || 'all';
                d.status = status === 'all' ? '' : status;
                d.subject = $('#subject').val() || '';
                d.date_from = $('#date_from').val() || '';
                d.date_to = $('#date_to').val() || '';
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'TicketNumber', name: 'ticket_number' },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            { data: 'subject', name: 'subject' },
            { data: 'status', name: 'status', orderable: false, searchable: false },
            { data: 'created_at', name: 'created_at' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false },
        ]
    });
});

$(document).on('click', '.change-enquiry-status-action', function() {
    var enquiryId = $(this).data('enquiry-id');
    var status = $(this).data('status');
    var url = $(this).data('url');

    $.ajax({
        url: url,
        method: 'POST',
        data: {
            id: enquiryId,
            status: status,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if(response.status) {
                location.reload();
            } else {
                alert(response.message);
            }
        },
        error: function() {
            alert('Failed to update status.');
        }
    });
});
