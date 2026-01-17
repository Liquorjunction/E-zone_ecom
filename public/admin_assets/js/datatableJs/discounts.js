$(function () {
    // Format function for row details: render targets array into HTML
    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

        function decodeHtmlEntities(html) {
            if (!html) return html;
            var txt = document.createElement('textarea');
            txt.innerHTML = html;
            return txt.value || txt.textContent;
        }

    function format(d) {
        var targets = d.targets || [];
        if (!Array.isArray(targets) || targets.length === 0) {
            return '<div class="p-2 text-muted small">No targets</div>';
        }

        var html =
            '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">';
        html +=
            "<thead><tr><th>Target Type</th><th>Target Name</th></tr></thead><tbody>";
            targets.forEach(function(t) {
                var type = t.target_type ? (t.target_type.charAt(0).toUpperCase() + t.target_type.slice(1)) : '';
                var rawName = t.name || 'Unknown';
                var decodedName = decodeHtmlEntities(rawName);
                var name = decodedName;
                html += '<tr>' +
                    '<td>' + escapeHtml(type) + '</td>' +
                    '<td>' + escapeHtml(name) + '</td>' +
                    '</tr>';
        });
        html += "</tbody></table></div>";
        return html;
    }

    var tableEl = $("#discounts-table");

    var hasAction = tableEl.data('hasAction') || tableEl.attr('data-has-action') == 1 || tableEl.attr('data-has-action') == '1';

    var columns = [
        {
            className: "dt-control",
            orderable: false,
            data: null,
            defaultContent: "",
            width: "15px",
        },
        { data: "id", name: "id" },
        { data: "name", name: "name" },
        { data: "type", name: "type" },
        { data: "value", name: "value" },
        { data: "starts_at", name: "starts_at" },
        { data: "ends_at", name: "ends_at" },
        {
            data: "status",
            name: "status",
            orderable: false,
            searchable: false,
        }
    ];

    if (hasAction) {
        columns.push({
            data: "action",
            name: "action",
            orderable: false,
            searchable: false,
        });
    }

    var table = tableEl.DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,
        ajax: tableEl.data("source"),
        columns: columns,
        order: [[1, "desc"]],
    });

    $("#discounts-table tbody").on("click", "td.dt-control", function () {
        var tr = $(this).closest("tr");
        var row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass("shown");
        } else {
            row.child(format(row.data())).show();
            tr.addClass("shown");
        }
    });
});
