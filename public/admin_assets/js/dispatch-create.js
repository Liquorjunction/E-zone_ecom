// Open the dispatch modal and fetch order items
window.openDispatchModal = function(orderId) {
    var $form = $('#dispatchForm');
    if ($form.length) {
        $form[0].reset();
    } else {
        console.warn('Dispatch form not found in DOM. Modal may not be included on this page.');
    }
    $('#dispatch_order_id').val(orderId);
    $('#order_id').val(orderId);
    $('#order_id_display').val(orderId ? ('Order #' + orderId) : '');
    fetchOrderItems(orderId);
    $('#dispatchModal').modal('show');
};

// Fetch order items for the given orderId and render the table
function fetchOrderItems(orderId) {
    $.ajax({
        url: '/admin/orders/' + orderId + '/items',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.success && Array.isArray(data.items)) {
                renderOrderItemsTable(data.items);
            } else {
                $('#dispatch-items-table tbody').empty().html('<tr><td colspan="5">No items found for this order.</td></tr>');
            }
        },
        error: function() {
            $('#dispatch-items-table tbody').empty().html('<tr><td colspan="5">Failed to load order items.</td></tr>');
        }
    });
}

// Render the order items table body
function renderOrderItemsTable(items) {
    var html = '';
    items.forEach(function(item) {
        html += '<tr data-order-item-id="' + item.id + '">';
        html += '<td>' + (item.product_name || '-') + '</td>';
        html += '<td>' + (item.variant || '-') + '</td>';
        html += '<td>' + (item.ordered_qty || '-') + '</td>';
        html += '<td><input type="number" min="1" max="' + (item.ordered_qty || 1) + '" class="form-control dispatch-qty-input" value="' + (item.ordered_qty || 1) + '" /></td>';
        html += '<td>' + renderInventorySelect(item.inventories) + '</td>';
        html += '</tr>';
    });
    $('#dispatch-items-table tbody').empty().html(html);
}

// Render inventory select for each item (expects inventories array)
function renderInventorySelect(inventories) {
    if (!inventories || !Array.isArray(inventories) || inventories.length === 0) {
        return '<select class="form-control inventory-select"><option value="">Select</option></select>';
    }
    var html = '<select class="form-control inventory-select">';
    html += '<option value="">Select</option>';
    inventories.forEach(function(inv) {
        var label = inv.name ? inv.name : (inv.location_type ? inv.location_type : 'Inventory');
        if (typeof inv.quantity !== 'undefined') {
            label += ' (Qty: ' + inv.quantity + ')';
        }
        html += '<option value="' + inv.id + '">' + label + '</option>';
    });
    html += '</select>';
    return html;
}

// Intercept form submit to build items array and send via AJAX
$(document).on('submit', '#dispatchForm', function(e) {
    e.preventDefault();
    var $form = $(this);
    var orderId = $('#order_id').val();
    var dispatchMethod = $('#dispatch_method').val();
    var remarks = $('#remarks').val();
    var dispatch_from_store_id = $('#dispatch_from_store_id').val();
    var dispatch_from_warehouse_id = $('#dispatch_from_warehouse_id').val();
    // Build items array from table rows
    var items = [];
    $('#dispatch-items-table tbody tr').each(function() {
        var $row = $(this);
        var order_item_id = $row.data('order-item-id');
        var inventory_id = $row.find('.inventory-select').val();
        var quantity = $row.find('.dispatch-qty-input').val();
        if (order_item_id && inventory_id && quantity) {
            items.push({
                order_item_id: order_item_id,
                inventory_id: inventory_id,
                quantity: quantity,
                dispatch_from_store_id: dispatch_from_store_id,
                dispatch_from_warehouse_id: dispatch_from_warehouse_id
            });
        }
    });
    if (!items.length) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Please add at least one item to dispatch.' });
        return;
    }
    var payload = {
        order_id: orderId,
        dispatch_method: dispatchMethod,
        remarks: remarks,
        items: items
    };
    var url = $form.attr('action');
    var csrfToken = $('meta[name="csrf-token"]').attr('content') || window.Laravel && window.Laravel.csrfToken;
    $.ajax({
        url: url,
        type: 'POST',
        data: JSON.stringify(payload),
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        success: function(data) {
            if (data.success) {
                Swal.fire({ icon: 'success', title: 'Success', text: data.message || 'Dispatch created.' });
                $('#dispatchModal').modal('hide');
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: data.message || 'Failed to create dispatch.' });
            }
        },
        error: function(xhr) {
            let msg = 'Failed to create dispatch.';
            if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
            Swal.fire({ icon: 'error', title: 'Error', text: msg });
        }
    });
});
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
