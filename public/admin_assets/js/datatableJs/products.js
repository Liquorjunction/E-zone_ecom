$(document).ready(function () {
    function format(d) {

        let variantsData = [];
        try {
            if (typeof d.variants_data === 'string') {
                let jsonString = d.variants_data;
                if (jsonString && jsonString.length > 0) {
                    variantsData = JSON.parse(jsonString);
                }
            } else if (Array.isArray(d.variants_data)) {
                variantsData = d.variants_data;
            } else if (typeof d.variants_data === 'object' && d.variants_data !== null) {
                variantsData = Array.isArray(d.variants_data) ? d.variants_data : [d.variants_data];
            }
        } catch (e) {
            console.error('Error parsing variants_data:', e, 'Input was:', d.variants_data);
            return '<tr><td colspan="7" class="text-center text-muted">Error parsing variants data</td></tr>';
        }

        if (!variantsData || !Array.isArray(variantsData) || variantsData.length === 0) {
            console.warn('No variants data found or empty array');
            return '<tr><td colspan="7" class="text-center text-muted">No variants available</td></tr>';
        }

        let html = `
            <table class="table table-hover table-striped mb-0">
                <thead class="bg-light text-uppercase small">
                    <tr>
                        <th class="border-0">Variant Name</th>
                        <th class="border-0">Price</th>
                        <th class="border-0 text-center">Bestseller</th>
                        <th class="border-0 text-center">Status</th>
                        <th class="border-0 text-center" style="width: 100px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
        `;

        variantsData.forEach(variant => {
            html += '<tr>';
            html += '<td class="fw-medium">' + variant.name + '</td>';
            html += '<td class="text-success fw-bold">$' + variant.price + '</td>';

            // Bestseller column
            if (variant.can_update) {
                const bestsellerSwitchId = 'variant-bestseller-switch-' + variant.id;
                const isChecked = variant.is_bestseller ? 'checked' : '';
                const labelSpanClass = variant.is_bestseller ? 'text-warning' : 'text-muted';
                html += '<td class="text-center">';
                html += '<div class="form-check form-switch form-check-inline pe-0">';
                html += '<input class="form-check-input variant-bestseller-toggle" type="checkbox" ' + isChecked + ' ';
                html += 'data-variant-id="' + variant.id + '" data-product-id="' + variant.product_id + '" ';
                html += 'data-url="' + variant.bestseller_toggle_url + '" id="' + bestsellerSwitchId + '">';
                html += '<label class="pl-2 form-check-label" for="' + bestsellerSwitchId + '">';
                html += '<span class="' + labelSpanClass + '"></span>';
                html += '</label>';
                html += '</div>';
                html += '</td>';
            } else {
                const bestsellerIcon = variant.is_bestseller
                    ? '<i class="bi bi-star-fill text-warning fs-5"></i>'
                    : '<i class="bi bi-star text-muted fs-5"></i>';
                html += '<td class="text-center">' + bestsellerIcon + '</td>';
            }

            // Status column
            if (variant.can_update) {
                const statusSwitchId = 'variant-status-switch-' + variant.id;
                const isChecked = variant.status === 'active' ? 'checked' : '';
                html += '<td class="text-center">';
                html += '<div class="form-check form-switch form-check-inline pe-0">';
                html += '<input class="form-check-input variant-status-toggle" type="checkbox" ' + isChecked + ' ';
                html += 'data-variant-id="' + variant.id + '" data-product-id="' + variant.product_id + '" ';
                html += 'data-url="' + variant.status_toggle_url + '" id="' + statusSwitchId + '">';
                html += '<label class="pl-2 form-check-label" for="' + statusSwitchId + '"></label>';
                html += '</div>';
                html += '</td>';
            } else {
                const statusBadgeClass = variant.status === 'active' ? 'bg-success' : 'bg-danger';
                const statusText = variant.status === 'active' ? 'Active' : 'Inactive';
                html += '<td class="text-center"><span class="badge ' + statusBadgeClass + '">' + statusText + '</span></td>';
            }

            // Actions column
            html += '<td class="text-center">';
            if (variant.can_delete) {
                html += '<button class="btn btn-sm btn-danger delete-variant shadow-sm" data-variant-id="' + variant.id + '" data-product-id="' + variant.product_id + '" title="Delete Variant">';
                html += '<i class="bi bi-trash"></i>';
                html += '</button>';
            }
            html += '</td>';
            html += '</tr>';
        });

        html += `
                </tbody>
            </table>
        `;

        return html;
    }

    // Initialize DataTable
    var table = $('#product-table').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        responsive: true,
        ajax: $('#product-table').data('source'),
        columns: [
            {
                className: 'dt-control',
                orderable: false,
                data: null,
                defaultContent: '',
                width: "15px"
            },
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'brand', name: 'brand.name', orderable: true, searchable: true },
            { data: 'category', name: 'category.name', orderable: true, searchable: true },
            { data: 'is_bestseller', name: 'is_bestseller', orderable: false, searchable: false },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
            { data: 'variants', name: 'variants', visible: false },
            { data: 'variants_data', name: 'variants_data', visible: false }
        ],
        order: [[1, 'desc']]
    });

    // Handle expand/collapse click
    $('#product-table tbody').on('click', 'td.dt-control', function () {
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

    // Toggle Variant Status (Switch)
    $(document).on('change', '.variant-status-toggle', function() {
        const checkbox = $(this);
        const url = checkbox.data('url');
        const variantId = checkbox.data('variant-id');
        const productId = checkbox.data('product-id');
        const isChecked = checkbox.is(':checked');

        console.log('Toggle variant status - URL:', url, 'Variant ID:', variantId, 'Product ID:', productId);

        if (!productId) {
            console.error('Could not find product ID');
            if (window.showToast) {
                window.showToast('Error: Could not find product ID', 'error');
            }
            checkbox.prop('checked', !isChecked);
            return;
        }

        $.ajax({
            url: url,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: productId,
                variant_id: variantId
            },
            success: function(response) {
                console.log('Status toggle response:', response);
                if (response.success) {
                    if (window.showToast) {
                        window.showToast(response.message, 'success');
                    }
                } else {
                    // failure
                    checkbox.prop('checked', !isChecked);
                    if (window.showToast) {
                        window.showToast(response.message || 'Failed to update variant status', 'error');
                    }
                }
            },
            error: function(xhr) {
                console.error('Status toggle error:', xhr.status, xhr.responseText);
                // failure
                checkbox.prop('checked', !isChecked);
                let message = 'Failed to update variant status';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                if (window.showToast) {
                    window.showToast(message, 'error');
                }
            }
        });
    });

    // Toggle Variant Bestseller (Switch)
    $(document).on('change', '.variant-bestseller-toggle', function() {
        const checkbox = $(this);
        const url = checkbox.data('url');
        const variantId = checkbox.data('variant-id');
        const productId = checkbox.data('product-id');
        const isChecked = checkbox.is(':checked');
        const label = checkbox.siblings('label').find('span');

        console.log('Toggle variant bestseller - URL:', url, 'Variant ID:', variantId, 'Product ID:', productId);

        if (!productId) {
            console.error('Could not find product ID');
            if (window.showToast) {
                window.showToast('Error: Could not find product ID', 'error');
            }
            checkbox.prop('checked', !isChecked);
            return;
        }

        if (isChecked) {
            label.removeClass('text-muted').addClass('text-warning');
        } else {
            label.removeClass('text-warning').addClass('text-muted');
        }

        $.ajax({
            url: url,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: productId,
                variant_id: variantId
            },
            success: function(response) {
                console.log('Bestseller toggle response:', response);
                if (response.success) {
                    if (window.showToast) {
                        window.showToast(response.message, 'success');
                    }
                } else {
                    // failure
                    checkbox.prop('checked', !isChecked);
                    if (!isChecked) {
                        label.removeClass('text-muted').addClass('text-warning');
                    } else {
                        label.removeClass('text-warning').addClass('text-muted');
                    }
                    if (window.showToast) {
                        window.showToast(response.message || 'Failed to update variant bestseller status', 'error');
                    }
                }
            },
            error: function(xhr) {
                console.error('Bestseller toggle error:', xhr.status, xhr.responseText);
                // failure
                checkbox.prop('checked', !isChecked);
                if (!isChecked) {
                    label.removeClass('text-muted').addClass('text-warning');
                } else {
                    label.removeClass('text-warning').addClass('text-muted');
                }
                let message = 'Failed to update variant bestseller status';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                if (window.showToast) {
                    window.showToast(message, 'error');
                }
            }
        });
    });

    // Delete Variant
    $(document).on('click', '.delete-variant', function() {
        const button = $(this);
        const variantId = button.data('variant-id');

        if (!confirm('Are you sure you want to delete this variant?')) {
            return;
        }

        $.ajax({
            url: '/admin/products/variant-destroy',
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                variant_id: variantId
            },
            success: function(response) {
                if (response.success) {
                    if (window.showToast) {
                        window.showToast(response.message, 'success');
                    }
                    table.ajax.reload(null, false);
                }
            },
            error: function(xhr) {
                if (window.showToast) {
                    window.showToast('Failed to delete variant', 'error');
                }
            }
        });
    });

    // Handle Import Form
    const importForm = document.getElementById('importForm');

    if (importForm) {
        const importFile = document.getElementById('importFile');
        const btnStartImport = document.getElementById('btnStartImport');

        importForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!importFile.files.length) {
                if (window.showToast) {
                    window.showToast('Please select a file to import', 'warning');
                }
                return;
            }

            const fileName = importFile.files[0].name;
            const formData = new FormData(importForm);

            // Disable button and close modal
            btnStartImport.disabled = true;
            const modal = bootstrap.Modal.getInstance(document.getElementById('importModal'));
            if (modal) {
                modal.hide();
            }

            // Upload file and start import job
            axios.post(importForm.action, formData)
                .then(response => {
                    if (response.data.success && response.data.batch_id) {
                        // Create floating notification
                        if (window.createImportNotification) {
                            window.createImportNotification(
                                response.data.batch_id,
                                fileName,
                                '/admin/products/import/progress/'
                            );
                        }
                        // Reset form
                        importForm.reset();
                    } else {
                        if (window.showToast) {
                            window.showToast(response.data.message || 'Upload failed', 'error');
                        }
                    }
                })
                .catch(error => {
                    let message = 'Upload failed';
                    if (error.response && error.response.data && error.response.data.message) {
                        message = error.response.data.message;
                    }
                    if (window.showToast) {
                        window.showToast(message, 'error');
                    }
                })
                .finally(() => {
                    btnStartImport.disabled = false;
                });
        });
    }

    // Handle product-level status toggle (syncs with variants)
    $(document).on('change', '.status-toggle', function() {
        const checkbox = $(this);
        const url = checkbox.data('url');
        const isChecked = checkbox.is(':checked');
        const productRow = checkbox.closest('tr');
        const row = table.row(productRow);
        const wasExpanded = row.child.isShown();

        $.ajax({
            url: url,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    if (window.showToast) {
                        window.showToast(response.message || 'Status updated!', 'success');
                    }

                    // Reload the entire row to get fresh data with synced variants
                    table.row(productRow).invalidate().draw(false);

                    // If row was expanded, expand it again to show synced data
                    if (wasExpanded) {
                        setTimeout(function() {
                            const updatedRow = table.row(productRow);
                            updatedRow.child(format(updatedRow.data())).show();
                            productRow.addClass('shown');
                        }, 100);
                    }
                } else {
                    checkbox.prop('checked', !isChecked);
                    if (window.showToast) {
                        window.showToast(response.message || 'Failed to update status', 'error');
                    }
                }
            },
            error: function(xhr) {
                checkbox.prop('checked', !isChecked);
                let message = 'Failed to update status';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                if (window.showToast) {
                    window.showToast(message, 'error');
                }
            }
        });
    });

    // Handle product-level bestseller toggle (syncs with variants)
    $(document).on('change', '.bestseller-toggle', function() {
        const checkbox = $(this);
        const url = checkbox.data('url');
        const isChecked = checkbox.is(':checked');
        const label = checkbox.siblings('label');
        const productRow = checkbox.closest('tr');
        const row = table.row(productRow);
        const wasExpanded = row.child.isShown();

        $.ajax({
            url: url,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    if (window.showToast) {
                        window.showToast(response.message || 'Bestseller status updated!', 'success');
                    }

                    // Reload the entire row to get fresh data with synced variants
                    table.row(productRow).invalidate().draw(false);

                    // If row was expanded, expand it again to show synced data
                    if (wasExpanded) {
                        setTimeout(function() {
                            const updatedRow = table.row(productRow);
                            updatedRow.child(format(updatedRow.data())).show();
                            productRow.addClass('shown');
                        }, 100);
                    }
                } else {
                    checkbox.prop('checked', !isChecked);
                    if (window.showToast) {
                        window.showToast(response.message || 'Failed to update bestseller status', 'error');
                    }
                }
            },
            error: function(xhr) {
                checkbox.prop('checked', !isChecked);
                if (label.length) {
                    if (!isChecked) {
                        label.html('<span class="text-warning">⭐ Bestseller</span>');
                    } else {
                        label.html('<span class="text-muted">Regular</span>');
                    }
                }

                if (window.showToast) {
                    window.showToast('Failed to update bestseller status. Please try again.', 'error');
                }
            }
        });
    });
});
