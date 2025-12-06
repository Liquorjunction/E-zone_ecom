$(document).ready(function () {
    // 1. Initialize DataTable
    $('#product-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: $('#product-table').data('route'),
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'image', name: 'image', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    // 2. Handle Import Form with Progress Bar
    const importForm = document.getElementById('importForm');

    if (importForm) {
        const btnContainer = document.getElementById('import-buttons');
        const progressContainer = document.getElementById('import-progress');
        const progressBar = document.getElementById('progress-bar-fill');
        const progressPercent = document.getElementById('progress-percent');
        const progressLabel = document.getElementById('progress-label');
        const errorMsg = document.getElementById('import-error-msg');

        importForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Reset UI
            btnContainer.classList.add('d-none');
            progressContainer.classList.remove('d-none');
            errorMsg.classList.add('d-none');
            progressBar.style.width = '0%';
            progressBar.classList.remove('bg-danger', 'progress-bar-striped');
            progressBar.classList.add('bg-success');
            progressLabel.innerText = "Uploading...";
            progressPercent.innerText = "0%";

            let formData = new FormData(importForm);
            let xhr = new XMLHttpRequest();

            xhr.open('POST', importForm.action, true);

            // Add CSRF Token
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (tokenMeta) {
                xhr.setRequestHeader('X-CSRF-TOKEN', tokenMeta.content);
            }

            // Track Upload Progress
            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    let percentComplete = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentComplete + '%';
                    progressPercent.innerText = percentComplete + '%';

                    if (percentComplete === 100) {
                        progressLabel.innerText = "Processing File (Do not close)...";
                        // Add striped animation to indicate server-side processing
                        progressBar.classList.add('progress-bar-striped');
                    }
                }
            };

            // Handle Response
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 300) {
                    // Success: Reload page to show the results modal
                    window.location.reload();
                } else {
                    // Try to parse JSON error message
                    let message = 'Upload failed. Status: ' + xhr.status;
                    try {
                        let response = JSON.parse(xhr.responseText);
                        if (response.errors && response.errors.file) {
                            message = response.errors.file[0];
                        } else if (response.errors && response.errors.error) {
                            message = response.errors.error; // Custom error key
                        } else if (response.message) {
                            message = response.message;
                        }
                    } catch (e) { console.error(e); }

                    showError(message);
                }
            };

            xhr.onerror = function() {
                showError('Network error occurred. Please try again.');
            };

            xhr.send(formData);
        });

        function showError(message) {
            btnContainer.classList.remove('d-none');
            // Keep progress bar visible but red to show where it failed
            progressBar.classList.remove('bg-success');
            progressBar.classList.add('bg-danger');
            progressBar.style.width = '100%';
            progressLabel.innerText = "Error";
            errorMsg.innerText = message;
            errorMsg.classList.remove('d-none');
        }
    }
});
