// JS for dynamic loading of order items, inventory, stores, and warehouses in the dispatch modal
$(document).ready(function() {
    // When the modal is shown, reset the form
    $('#dispatchModal').on('show.bs.modal', function (event) {
        $('#dispatchForm')[0].reset();
        $('#order_item_id').empty();
        $('#inventory_id').empty();
        $('#dispatch_from_store_id').empty();
        $('#dispatch_from_warehouse_id').empty();
    });

    // Load order items when an order is selected
    $('#order_id').on('change', function() {
        var orderId = $(this).val();
        $('#order_item_id').empty();
        $('#inventory_id').empty();
        if (!orderId) return;
        $.ajax({
            url: '/admin/orders/' + orderId + '/items',
            type: 'GET',
            success: function(data) {
                $('#order_item_id').append('<option value="">Select Order Item</option>');
                $.each(data, function(i, item) {
                    $('#order_item_id').append('<option value="' + item.id + '">' + item.product_name + (item.variant_name ? ' (' + item.variant_name + ')' : '') + ' - Qty: ' + item.quantity + '</option>');
                });
            }
        });
    });

    // Load inventory when an order item is selected
    $('#order_item_id').on('change', function() {
        var orderItemId = $(this).val();
        $('#inventory_id').empty();
        if (!orderItemId) return;
        $.ajax({
            url: '/admin/order-items/' + orderItemId + '/inventories',
            type: 'GET',
            success: function(data) {
                $('#inventory_id').append('<option value="">Select Inventory</option>');
                $.each(data, function(i, inv) {
                    $('#inventory_id').append('<option value="' + inv.id + '">' + inv.location + ' - Qty: ' + inv.quantity + '</option>');
                });
            }
        });
    });

    // Load stores and warehouses on modal show (or you can load once on page load)
    $('#dispatchModal').on('show.bs.modal', function () {
        // Stores
        $.ajax({
            url: '/admin/stores/list',
            type: 'GET',
            success: function(data) {
                $('#dispatch_from_store_id').append('<option value="">Select Store</option>');
                $.each(data, function(i, store) {
                    $('#dispatch_from_store_id').append('<option value="' + store.id + '">' + store.name + '</option>');
                });
            }
        });
        // Warehouses
        $.ajax({
            url: '/admin/warehouses/list',
            type: 'GET',
            success: function(data) {
                $('#dispatch_from_warehouse_id').append('<option value="">Select Warehouse</option>');
                $.each(data, function(i, wh) {
                    $('#dispatch_from_warehouse_id').append('<option value="' + wh.id + '">' + wh.name + '</option>');
                });
            }
        });
    });
});
