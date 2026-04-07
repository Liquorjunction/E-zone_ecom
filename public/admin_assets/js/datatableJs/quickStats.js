$(document).ready(function () {
    var $table = $('#quick-stats-table');
    if (!$table.length) {
        return;
    }

    var dataUrl = $table.data('url');
    var $addBtn = $('#add-quick-stat-btn');
    var MAX_QUICK_STATS = 4;

    function checkQuickStatsLimit(totalRecords) {
        if (totalRecords >= MAX_QUICK_STATS) {
            $addBtn.hide();
            // Optionally show a message
            if (!$('.quick-stats-limit-msg').length) {
                $addBtn.parent().append('<small class="text-muted quick-stats-limit-msg d-block mt-2">Maximum of ' + MAX_QUICK_STATS + ' quick stats reached</small>');
            }
        } else {
            $addBtn.show();
            $('.quick-stats-limit-msg').remove();
        }
    }

    var hasAction = $table.data('hasAction') || $table.attr('data-has-action') == 1 || $table.attr('data-has-action') == '1';

    var columns = [
        {
            data: 'DT_RowIndex',
            name: 'DT_RowIndex',
            orderable: false,
            searchable: false
        },
        {
            data: 'icon',
            name: 'icon',
            orderable: false,
            searchable: false,
            render: function (data) {
                if (data) {
                    return '<img src="' + data + '" alt="Icon" class="img-thumbnail" style="width: 40px; height: 40px; object-fit: contain;">';
                }
                return '<span class="text-muted">No Icon</span>';
            }
        },
        {
            data: 'text',
            name: 'text',
            orderable: false
        },
        {
            data: 'status',
            name: 'status',
            orderable: false,
            searchable: false
        }
    ];

    if (hasAction) {
        columns.push({
            data: 'actions',
            name: 'actions',
            orderable: false,
            searchable: false
        });
    }

    var dataTable = $table.DataTable({
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: {
            url: dataUrl,
            type: 'GET',
            dataSrc: function(json) {
                // Check limit after data loads
                checkQuickStatsLimit(json.recordsTotal);
                return json.data;
            }
        },
        columns: columns,
        pageLength: 10,
        responsive: false,
        drawCallback: function(settings) {
            // Re-check limit after table redraws (e.g., after delete)
            var totalRecords = settings.json ? settings.json.recordsTotal : 0;
            checkQuickStatsLimit(totalRecords);
        }
    });
});
