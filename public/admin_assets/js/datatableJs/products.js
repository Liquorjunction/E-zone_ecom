$(document).ready(function () {
    // 1. Initialize DataTable
    if ($('#product-table').length) {
        $('#product-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: $('#product-table').data('route'),
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'is_bestseller', name: 'is_bestseller', orderable: false, searchable: false },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    }

    // 2. Handle Import Form (with Advanced Floating Notification)
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

    // 3. Handle Bestseller Toggle (using event delegation)
    $(document).on('change', '.bestseller-toggle', function() {
        const checkbox = $(this);
        const url = checkbox.data('url');
        const isChecked = checkbox.is(':checked');
        const label = checkbox.siblings('label');

        // Optimistic UI Update
        // if (label.length) {
        //     if (isChecked) {
        //         label.html('<span class="text-warning">⭐ Bestseller</span>');
        //     } else {
        //         label.html('<span class="text-muted">Regular</span>');
        //     }
        // }

        // Send AJAX Request
        $.ajax({
            url: url,
            type: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (window.showToast) {
                    window.showToast(response.message || 'Bestseller status updated!', 'success');
                }
            },
            error: function(xhr) {
                // Revert UI on Failure
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
