$(document).ready(function() {
    $('#stockReportTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-2"Bfl>rtip',
        buttons: [
            {
                extend: 'csvHtml5',
                text: '<i class="fa fa-download"></i> Export CSV',
                className: 'btn btn-success btn-sm'
            }
        ],
        pageLength: 10,
        lengthMenu: [ [10, 25, 50, 100, -1], [10, 25, 50, 100, "All"] ],
        order: [],
        language: {
            search: "<span class='me-2'>Search:</span>",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            paginate: {
                previous: "<i class='fa fa-angle-left'></i>",
                next: "<i class='fa fa-angle-right'></i>"
            }
        }
    });
    // Move the export button to the right
    $(".dt-buttons").addClass("float-end");
    $(".dataTables_filter").addClass("float-end");
    $(".dataTables_length").addClass("float-start");
});
