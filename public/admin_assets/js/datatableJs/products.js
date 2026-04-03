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
                if (variant.can_delete_variant) {
                    actions = `
                        <td>
                            <button class="btn btn-sm btn-danger delete-variant shadow-sm" data-variant-id="${variant.id}" data-product-id="${variant.product_id}" data-url="${variant.delete_url || ""}" title="Delete Variant">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>`;
                } else {
                    actions = `<td></td>`;
                }
            }

            html += `
                <tr>
                    <td>${variant.name || "N/A"}</td>
                    <td>${variant.sku || "N/A"}</td>
                    <td class="text-success fw-bold">${window.CURRENCY_SYMBOL} ${variant.price}</td>
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
    var table = (window.productsTable = $("#product-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: $("#product-table").data("source"),
            type: "GET",
            data: function (d) {
                d.columns[3].search.value = $("#search").val();

                d.best_seller = $("#bestseller").is(":checked") ? "1" : "";
                d.in_stock = $("input[name='in_stock']").is(":checked")? "1" : "";
                d.out_of_stock = $("input[name='out_of_stock']").is(":checked") ? "1" : "";
                d.status = [];
                if ($("input[name='status'][value='active']").is(":checked")) d.status.push("active");
                if ($("input[name='status'][value='inactive']").is(":checked")) d.status.push("inactive");
                d.price_min = $("input[name='price_min']").val();
                d.price_max = $("input[name='price_max']").val();
                d.date_from = $("input[name='date_from']").val();
                d.date_to = $("input[name='date_to']").val();
            },
            error: function (xhr, error, thrown) {
                console.error("DataTables Error:", error, thrown);
            },
        },
        searching: true,
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
            { data: "category", name: "category.category_name", width: "10%", searchable: false },
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
            $("input.status-toggle").each(function () {
                const el = $(this);
                const allActive =
                    parseInt(el.attr("data-variants-all-active") || 0, 10) ===
                    1;
                const allInactive =
                    parseInt(el.attr("data-variants-all-inactive") || 0, 10) ===
                    1;
                if (!allActive && !allInactive) {
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
    }));

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
    // 4. FILTER BAR HANDLERS
    // =========================================================

    // Submit: prevent page reload, reload DataTable with new filter values
    $("#product-filter-bar").on("submit", function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // Reset: clear all filter inputs, reload DataTable
    $("#product-filter-bar").on("click", "a", function (e) {
        e.preventDefault();
        document.getElementById("product-filter-bar").reset();
        table.ajax.reload();
    });

    // =========================================================
    // 5. PARENT PRODUCT ACTIONS (Status & Bestseller)
    // =========================================================

    // NOTE: Status toggle is handled globally by
    // public/admin_assets/js/datatable-status-toggle.js

    // Parent product bestseller toggle
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
                    table.ajax.reload(null, false);
                } else {
                    checkbox.prop("checked", !isChecked);
                    if (window.showToast)
                        window.showToast(
                            response.message || "Error updating variant",
                            "error",
                        );
                }
            },
            error: function () {
                checkbox.prop("checked", !isChecked);
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
                    table.ajax.reload(null, false);
                } else {
                    checkbox.prop("checked", !isChecked);
                    if (window.showToast)
                        window.showToast(response.message || "Failed", "error");
                }
            },
            error: function () {
                checkbox.prop("checked", !isChecked);
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
    // 8. IMPORT FORM HANDLER
    // =========================================================
    const importForm = document.getElementById("importForm");

    if (importForm) {
        const importFile = document.getElementById("importFile");
        const btnStartImport = document.getElementById("btnStartImport");

        importForm.addEventListener("submit", function (e) {
            e.preventDefault();

            if (!importFile.files.length) {
                if (window.showToast) {
                    window.showToast(
                        "Please select a file to import",
                        "warning",
                    );
                }
                return;
            }

            const fileName = importFile.files[0].name;
            const formData = new FormData(importForm);

            btnStartImport.disabled = true;
            const modal = bootstrap.Modal.getInstance(
                document.getElementById("importModal"),
            );
            if (modal) {
                modal.hide();
            }

            axios
                .post(importForm.action, formData)
                .then((response) => {
                    if (response.data.success && response.data.batch_id) {
                        if (window.createImportNotification) {
                            window.createImportNotification(
                                response.data.batch_id,
                                fileName,
                                "/admin/products/import/progress/",
                            );
                        }
                        importForm.reset();
                    } else {
                        if (window.showToast) {
                            window.showToast(
                                response.data.message || "Upload failed",
                                "error",
                            );
                        }
                    }
                })
                .catch((error) => {
                    let message = "Upload failed";
                    if (
                        error.response &&
                        error.response.data &&
                        error.response.data.message
                    ) {
                        message = error.response.data.message;
                    }
                    if (window.showToast) {
                        window.showToast(message, "error");
                    }
                })
                .finally(() => {
                    btnStartImport.disabled = false;
                });
        });
    }
});
