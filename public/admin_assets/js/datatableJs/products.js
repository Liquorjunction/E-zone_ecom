$(document).ready(function () {
    // =========================================================
    // 1. HELPER: FORMAT CHILD ROWS (VARIANTS)
    // =========================================================
    function format(d) {
        let variantsData = [];
        try {
            if (typeof d.variants_data === "string") {
                let jsonString = d.variants_data;
                if (jsonString && jsonString.length > 0) {
                    variantsData = JSON.parse(jsonString);
                }
            } else if (Array.isArray(d.variants_data)) {
                variantsData = d.variants_data;
            } else if (
                typeof d.variants_data === "object" &&
                d.variants_data !== null
            ) {
                variantsData = Array.isArray(d.variants_data)
                    ? d.variants_data
                    : [d.variants_data];
            }
        } catch (e) {
            console.error(
                "Error parsing variants_data:",
                e,
                "Input was:",
                d.variants_data,
            );
            return '<tr><td colspan="4" class="text-center text-muted">Error parsing variants data</td></tr>';
        }

        if (
            !variantsData ||
            !Array.isArray(variantsData) ||
            variantsData.length === 0
        ) {
            return '<tr><td colspan="4" class="text-center text-muted">No variants available</td></tr>';
        }

        const showVariantActions = variantsData.some(
            (v) => v.can_update || v.can_delete,
        );

        let html = `
            <table class="table table-hover table-striped mb-0">
                <thead>
                    <tr>
                        <th style="width: 25%">Variant Name</th>
                        <th style="width: 20%">SKU</th>
                        <th style="width: 15%">Price</th>
                        <th style="width: 10%">Bestseller</th>
                        <th style="width: 15%">Status</th>
                        ${showVariantActions ? '<th style="width: 15%">Actions</th>' : ""}
                    </tr>
                </thead>
                <tbody>
        `;

        variantsData.forEach((variant) => {
            const bestsellerToggle = variant.can_update
                ? `<div class="form-check form-switch">
                        <input class="form-check-input variant-bestseller-toggle" type="checkbox"
                            data-id="${variant.id}"
                            data-product-id="${variant.product_id || ""}"
                            data-url="${variant.bestseller_toggle_url}"
                            href="${variant.bestseller_toggle_url}"
                            ${variant.is_bestseller ? "checked" : ""}>
                   </div>`
                : variant.is_bestseller
                  ? '<span class="badge bg-warning">Yes</span>'
                  : '<span class="badge bg-secondary">No</span>';

            const statusBadge =
                variant.status === "active"
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>';

            const statusToggle = variant.can_update
                ? `<div class="form-check form-switch">
                        <input class="form-check-input variant-status-toggle" type="checkbox"
                            data-id="${variant.id}"
                            data-product-id="${variant.product_id || ""}"
                            data-url="${variant.status_toggle_url}"
                            href="${variant.status_toggle_url}"
                            ${variant.status === "active" ? "checked" : ""}>
                   </div>`
                : statusBadge;

            let actions = "";
            if (showVariantActions) {
                actions = `
                    <td>
                        <button class="btn btn-sm btn-danger delete-variant shadow-sm" data-variant-id="${variant.id}" data-product-id="${variant.product_id}" data-url="${variant.delete_url || ""}" title="Delete Variant">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>`;
            }

            html += `
                <tr>
                    <td>${variant.name || "N/A"}</td>
                    <td>${variant.sku || "N/A"}</td>
                    <td class="text-success fw-bold">$${variant.price}</td>
                    <td>${bestsellerToggle}</td>
                    <td>${statusToggle}</td>
                    ${showVariantActions ? actions : ""}
                </tr>
            `;
        });

        html += "</tbody></table>";
        return html;
    }

    // =========================================================
    // 2. DATATABLE INITIALIZATION
    // =========================================================
    var table = $("#product-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $("#product-table").data("source"),
            data: function (d) {
                // Append Filter Data to the AJAX request
                d.brand_id = $("#filter-brand").val();
                d.category_id = $("#filter-category").val();
                d.status = $("#filter-status").val();
            },
            error: function (xhr, error, thrown) {
                console.error("DataTables Error:", error, thrown);
            },
        },
        columns: [
            {
                className: "dt-control",
                orderable: false,
                data: null,
                defaultContent: "",
                width: "5%",
            },
            {
                data: "DT_RowIndex",
                name: "DT_RowIndex",
                orderable: false,
                searchable: false,
                width: "5%",
            },
            {
                data: "image",
                name: "image",
                orderable: false,
                searchable: false,
                width: "10%",
            },
            { data: "name", name: "name", width: "20%" },
            { data: "brand", name: "brand.name", width: "10%" },
            { data: "category", name: "category.name", width: "10%" },
            { data: "is_bestseller", name: "is_bestseller", width: "10%" },
            { data: "status", name: "status", width: "10%" },
            {
                data: "action",
                name: "action",
                orderable: false,
                searchable: false,
                width: "15%",
            },
        ],
        order: [[1, "asc"]],
        pageLength: 10,
        lengthMenu: [
            [10, 25, 50, -1],
            [10, 25, 50, "All"],
        ],
        responsive: true,
        language: {
            processing:
                '<i class="fa fa-spinner fa-spin fa-2x fa-fw"></i><span class="sr-only">Loading...</span>',
        },
        drawCallback: function () {
            // Re-initialize any tooltips or plugins after table draw
            // $('[data-toggle="tooltip"]').tooltip();
            // Update indeterminate state for parent toggles based on server-provided flags
            $("input.status-toggle").each(function () {
                const el = $(this);
                const allActive =
                    parseInt(el.attr("data-variants-all-active") || 0, 10) ===
                    1;
                const allInactive =
                    parseInt(el.attr("data-variants-all-inactive") || 0, 10) ===
                    1;
                if (!allActive && !allInactive) {
                    // Mixed state
                    this.indeterminate = true;
                } else {
                    this.indeterminate = false;
                }
            });

            $("input.bestseller-toggle").each(function () {
                const el = $(this);
                const allYes =
                    parseInt(
                        el.attr("data-variants-all-bestseller") || 0,
                        10,
                    ) === 1;
                const allNo =
                    parseInt(
                        el.attr("data-variants-all-not-bestseller") || 0,
                        10,
                    ) === 1;
                if (!allYes && !allNo) {
                    this.indeterminate = true;
                } else {
                    this.indeterminate = false;
                }
            });
        },
    });

    // =========================================================
    // 3. CHILD ROW EXPANSION
    // =========================================================
    $("#product-table tbody").on("click", "td.dt-control", function () {
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

    // =========================================================
    // 4. FILTERING HANDLERS
    // =========================================================
    $("#filter-brand, #filter-category, #filter-status").on(
        "change",
        function () {
            table.draw();
        },
    );

    $("#reset-filters").on("click", function () {
        $("#filter-brand").val("").trigger("change");
        $("#filter-category").val("").trigger("change");
        $("#filter-status").val("").trigger("change");
        table.draw();
    });

    // =========================================================
    // 5. PARENT PRODUCT ACTIONS (Status & Bestseller)
    // =========================================================

    // NOTE: Status toggle is handled globally by
    // public/admin_assets/js/datatable-status-toggle.js

    // Toggle Product Bestseller
    // Parent product bestseller toggle (checkbox in datatable)
    $(document).on("change", ".bestseller-toggle", function (e) {
        e.preventDefault();
        const checkbox = $(this);
        const url = checkbox.data("url") || checkbox.attr("data-url");
        if (!url) return;

        const isChecked = checkbox.prop("checked");
        checkbox.prop("disabled", true);

        $.ajax({
            url: url,
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                _method: "PATCH",
            },
            success: function (response) {
                if (response.success) {
                    if (window.showToast)
                        window.showToast(response.message, "success");
                    table.ajax.reload(null, false);
                } else {
                    checkbox.prop("checked", !isChecked);
                    if (window.showToast)
                        window.showToast(
                            response.message || "Error updating bestseller",
                            "error",
                        );
                }
            },
            error: function () {
                checkbox.prop("checked", !isChecked);
                if (window.showToast) window.showToast("Server Error", "error");
            },
            complete: function () {
                checkbox.prop("disabled", false);
            },
        });
    });

    // =========================================================
    // 6. VARIANT ACTIONS (Status & Bestseller)
    // =========================================================

    // Variant Status Toggle
    $(document).on("change", ".variant-status-toggle", function (e) {
        e.preventDefault();
        const checkbox = $(this);
        const url = checkbox.data("url") || checkbox.attr("href");
        const productId =
            checkbox.data("product-id") || checkbox.attr("data-product-id");
        const isChecked = checkbox.prop("checked");

        checkbox.prop("disabled", true);

        $.ajax({
            url: url,
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                _method: "PATCH",
                variant_id: checkbox.data("id"),
                id: productId,
            },
            success: function (response) {
                if (response.success) {
                    if (window.showToast)
                        window.showToast(response.message, "success");
                    // SYNC FIX: Reload to update parent status if needed
                    table.ajax.reload(null, false);
                } else {
                    checkbox.prop("checked", !isChecked); // Revert
                    if (window.showToast)
                        window.showToast(
                            response.message || "Error updating variant",
                            "error",
                        );
                }
            },
            error: function () {
                checkbox.prop("checked", !isChecked); // Revert
                if (window.showToast)
                    window.showToast("Failed to update status", "error");
            },
            complete: function () {
                checkbox.prop("disabled", false);
            },
        });
    });

    // Variant Bestseller Toggle
    $(document).on("change", ".variant-bestseller-toggle", function (e) {
        e.preventDefault();
        const checkbox = $(this);
        const url = checkbox.data("url") || checkbox.attr("href");
        const productId =
            checkbox.data("product-id") || checkbox.attr("data-product-id");
        const isChecked = checkbox.prop("checked");

        checkbox.prop("disabled", true);

        $.ajax({
            url: url,
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                _method: "PATCH",
                variant_id: checkbox.data("id"),
                id: productId,
            },
            success: function (response) {
                if (response.success) {
                    if (window.showToast)
                        window.showToast(response.message, "success");
                    // SYNC FIX: Reload to update parent bestseller status if needed
                    table.ajax.reload(null, false);
                } else {
                    checkbox.prop("checked", !isChecked); // Revert
                    if (window.showToast)
                        window.showToast(response.message || "Failed", "error");
                }
            },
            error: function () {
                checkbox.prop("checked", !isChecked); // Revert
                if (window.showToast)
                    window.showToast("Failed to update status", "error");
            },
            complete: function () {
                checkbox.prop("disabled", false);
            },
        });
    });

    // Variant Delete Button
    $(document).on("click", ".delete-variant", function () {
        const button = $(this);
        const variantId = button.data("variant-id");

        if (!confirm("Are you sure you want to delete this variant?")) {
            return;
        }

        $.ajax({
            url: "/admin/products/variant-destroy",
            type: "PATCH",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: {
                variant_id: variantId,
            },
            success: function (response) {
                if (response.success) {
                    if (window.showToast) {
                        window.showToast(response.message, "success");
                    }
                    table.ajax.reload(null, false);
                }
            },
            error: function (xhr) {
                if (window.showToast) {
                    window.showToast("Failed to delete variant", "error");
                }
            },
        });
    });

    // =========================================================
    // 7. DELETE ACTIONS
    // =========================================================
    $(document).on("click", ".delete-btn", function (e) {
        e.preventDefault();
        let url = $(this).attr("href");

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: {
                        _token: $('meta[name="csrf-token"]').attr("content"),
                    },
                    success: function (response) {
                        if (response.success) {
                            if (window.showToast)
                                window.showToast(response.message, "success");
                            table.ajax.reload(null, false);
                        } else {
                            if (window.showToast)
                                window.showToast(response.message, "error");
                        }
                    },
                    error: function () {
                        if (window.showToast)
                            window.showToast("Something went wrong!", "error");
                    },
                });
            }
        });
    });

    // =========================================================
    // 8. BULK ACTIONS (Select All & Bulk Delete)
    // =========================================================

    // Select All Checkbox
    $("#select-all").on("click", function () {
        var rows = table.rows({ search: "applied" }).nodes();
        $('input[type="checkbox"].product-checkbox', rows).prop(
            "checked",
            this.checked,
        );
    });

    // Handle individual checkbox click
    $("#product-table tbody").on(
        "change",
        'input[type="checkbox"].product-checkbox',
        function () {
            if (!this.checked) {
                var el = $("#select-all").get(0);
                if (el && el.checked && "indeterminate" in el) {
                    el.indeterminate = true;
                }
            }
        },
    );

    // Bulk Delete Button Click
    $("#bulk-delete-btn").on("click", function () {
        var selectedIds = [];
        $(".product-checkbox:checked").each(function () {
            selectedIds.push($(this).val());
        });

        if (selectedIds.length === 0) {
            Swal.fire(
                "No items selected",
                "Please select at least one product to delete.",
                "info",
            );
            return;
        }

        Swal.fire({
            title: "Are you sure?",
            text: `You are about to delete ${selectedIds.length} products. This cannot be undone!`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Yes, delete them!",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: $(this).data("url"), // Ensure your button has data-url attribute
                    type: "POST",
                    data: {
                        ids: selectedIds,
                        _token: $('meta[name="csrf-token"]').attr("content"),
                        _method: "DELETE", // Method spoofing if route is DELETE
                    },
                    success: function (response) {
                        if (response.success) {
                            if (window.showToast)
                                window.showToast(response.message, "success");
                            table.ajax.reload(null, false);
                            $("#select-all").prop("checked", false);
                        } else {
                            if (window.showToast)
                                window.showToast(
                                    response.message || "Error occurred",
                                    "error",
                                );
                        }
                    },
                    error: function () {
                        if (window.showToast)
                            window.showToast("Server Error", "error");
                    },
                });
            }
        });
    });
});
