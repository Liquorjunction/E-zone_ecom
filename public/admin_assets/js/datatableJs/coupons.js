$(function () {
    var table = $('#coupons-table');
    var hasAction = table.data('hasAction') || table.attr('data-has-action') == 1 || table.attr('data-has-action') == '1';
    var columns = [
        { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
        { data: "name", name: "name" },
        { data: "code", name: "code" },
        { data: "usages_count", name: "usages_count", orderable: false, searchable: false },
        { data: "coupon_type", name: "coupon_type" },
        { data: "start_date", name: "start_date" },
        { data: "end_date", name: "end_date" },
        { data: "status", name: "status", orderable: false, searchable: false },
    ];
    if (hasAction) {
        columns.push({ data: 'action', name: 'action', orderable: false, searchable: false });
    }
    window.couponsTable = table.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: table.data('source'),
            data: function (d) {
                var selectedStatus = $('input[name="status"]:checked').val() || 'all';
                d.status = selectedStatus === 'all' ? '' : selectedStatus;
                d.date_from = $('#date_from').val() || '';
                d.date_to = $('#date_to').val() || '';
                d.sort_by_usage_count = $('#sort_by_usage_count').val() || '';
                d.coupon_type = $('#coupon_type').val() || '';
            }
        },
        columns: columns
    })
});
