$(function () {
    // Format function for row details
    function format (d) {
        return `
            <table class="table table-bordered">
                <tr>
                    <td><strong>Batch No:</strong></td><td>${d.batch_no}</td>
                </tr>
                <tr>
                    <td><strong>Reference Type:</strong></td><td>${d.reference_type}</td>
                </tr>
                <tr>
                    <td><strong>Before Quantity:</strong></td><td>${d.before_quantity}</td>
                </tr>
                <tr>
                    <td><strong>After Quantity:</strong></td><td>${d.after_quantity}</td>
                </tr>
                <tr>
                    <td><strong>Quantity Change:</strong></td><td>${d.quantity_change}</td>
                </tr>
            </table>
        `;
    }

    var table = $('#inventory-transactions-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,
        ajax: $('#inventory-transactions-table').data('source'),
        columns: [
            {
                className: 'dt-control',
                orderable: false,
                data: null,
                defaultContent: '',
                width: "15px"
            },
            { data: 'id', name: 'inventory_transactions.id' },
            { data: 'product_name', name: 'products.name' },
            { data: 'quantity_change', name: 'inventory_transactions.quantity_change' },
            { data: 'location_type', name: 'inventories.location_type' },
            { data: 'type', name: 'inventory_transactions.type' },
            { data: 'created_at', name: 'inventory_transactions.created_at' }
        ],
        order: [[1, 'desc']]
    });

    $('#inventory-transactions-table tbody').on('click', 'td.dt-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(format(row.data())).show();
            tr.addClass('shown');
        }
    });
});
