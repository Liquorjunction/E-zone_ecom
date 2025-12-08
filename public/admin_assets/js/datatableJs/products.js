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

    // 2. Handle Import Form (Queue + Polling)
    const importForm = document.getElementById('importForm');

    if (importForm) {
        const inputSection = document.getElementById('import-input-section');
        const progressSection = document.getElementById('import-progress-section');
        const btnSection = document.getElementById('import-buttons');
        const btnCloseModal = document.getElementById('btn-close-modal');

        const progressBar = document.getElementById('progress-bar-fill');
        const progressText = document.getElementById('progress-percent-text');
        const statusText = document.getElementById('progress-status-text');
        const detailsText = document.getElementById('progress-details');
        const errorMsg = document.getElementById('import-error-msg');

        importForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // --- A. Setup UI for Upload ---
            inputSection.classList.add('d-none');
            btnSection.classList.add('d-none');
            progressSection.classList.remove('d-none');
            btnCloseModal.disabled = true; // Prevent closing
            errorMsg.classList.add('d-none');

            // Reset Bars
            progressBar.style.width = '0%';
            progressBar.classList.remove('bg-danger');
            progressBar.classList.add('bg-success');
            statusText.innerText = "Uploading File...";
            detailsText.innerText = "Please wait while we upload your file.";

            let formData = new FormData(importForm);

            // --- B. Step 1: Upload File & Start Job ---
            axios.post(importForm.action, formData)
                .then(response => {
                    if (response.data.success && response.data.batch_id) {
                        // Upload success, now we wait for the Queue
                        startPolling(response.data.batch_id);
                    } else {
                        showImportError(response.data.message || 'Upload failed unknown error');
                    }
                })
                .catch(error => {
                    let msg = "Upload Failed";
                    if(error.response && error.response.data && error.response.data.message) {
                        msg = error.response.data.message;
                    }
                    showImportError(msg);
                });
        });

        // --- C. Step 2: Poll for Progress ---
        function startPolling(batchId) {
            statusText.innerText = "Processing Data...";

            // Poll every 1.5 seconds
            let interval = setInterval(() => {
                axios.get(`/admin/products/import/progress/${batchId}`)
                    .then(res => {
                        const data = res.data;

                        if (data.status === 'processing') {
                            // Update UI with real numbers
                            updateProgressUI(data);
                        }
                        else if (data.status === 'completed') {
                            clearInterval(interval);
                            progressBar.style.width = '100%';
                            progressText.innerText = '100%';
                            statusText.innerText = "Import Complete!";
                            detailsText.innerText = "Refreshing page to show results...";

                            // --- D. Step 3: Reload to show the Success Popup ---
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                        else if (data.status === 'failed') {
                            clearInterval(interval);
                            let serverErr = data.errors && data.errors.length > 0 ? data.errors[0] : 'Unknown Error';
                            showImportError("Import Job Failed: " + serverErr);
                        }
                    })
                    .catch(err => {
                        // Don't stop polling immediately on network blip, but log it
                        console.error("Polling error", err);
                    });
            }, 1500);
        }

        function updateProgressUI(data) {
            // Update Bar
            progressBar.style.width = data.progress + '%';
            progressText.innerText = data.progress + '%';

            // Update Text Details (e.g., "Processed 5 of 20")
            if (data.total > 0) {
                detailsText.innerText = `Processed ${data.processed} of ${data.total} rows.`;
            } else {
                detailsText.innerText = "Initializing data...";
            }
        }

        function showImportError(message) {
            progressBar.classList.remove('bg-success');
            progressBar.classList.add('bg-danger');
            progressBar.style.width = '100%';
            statusText.innerText = "Error";
            errorMsg.innerText = message;
            errorMsg.classList.remove('d-none');

            // Re-enable close button so they can try again
            btnCloseModal.disabled = false;
        }
    }

    // 3. Handle Bestseller Toggle (using event delegation)
    $(document).on('change', '.bestseller-toggle', function() {
        const checkbox = $(this);
        const url = checkbox.data('url');
        const isChecked = checkbox.is(':checked');
        const label = checkbox.siblings('label');

        // Optimistic UI Update
        if (label.length) {
            if (isChecked) {
                label.html('<span class="text-warning">⭐ Bestseller</span>');
            } else {
                label.html('<span class="text-muted">Regular</span>');
            }
        }

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
