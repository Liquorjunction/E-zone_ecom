function setupImagePreview(inputId, previewId, feedbackId, maxSizeMB = 2) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const feedback = document.getElementById(feedbackId);

    // Convert MB to Bytes
    const maxSizeBytes = maxSizeMB * 1024 * 1024;

    // Allowed types (Union of all your module requirements)
    const allowed = [
        'image/jpeg', 'image/png', 'image/jpg', 'image/gif',
        'image/webp', 'image/bmp', 'image/x-icon', 'image/tiff', 'image/avif'
    ];

    if (!input || !preview || !feedback) return;

    input.addEventListener('change', function(e) {
        const file = this.files && this.files[0];

        if (!file) {
            feedback.style.display = 'none';
            return;
        }

        if (!allowed.includes(file.type)) {
            feedback.style.display = 'block';
            feedback.textContent = 'Invalid file type. Allowed: jpg, png, webp, gif, bmp, ico, tiff, avif.';
            this.value = '';
            return;
        }

        if (file.size > maxSizeBytes) {
            feedback.style.display = 'block';
            feedback.textContent = `File too large. Max allowed size is ${maxSizeMB}MB.`;
            this.value = '';
            return;
        }

        feedback.style.display = 'none';
        const reader = new FileReader();
        reader.onload = function(ev) {
            preview.src = ev.target.result;
            preview.style.opacity = '1';
            preview.style.display = 'inline-block';
        }
        reader.readAsDataURL(file);
    });
}
