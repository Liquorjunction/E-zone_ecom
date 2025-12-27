/**
 * Common Image Preview & Validation Script
 * * @param {string} inputId      - The ID of the file input
 * @param {string} previewId    - The ID of the img tag for preview
 * @param {string} feedbackId   - The ID of the error message div
 * @param {number} maxSizeMB    - (Optional) Max file size in MB. Default is 2.
 */
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

        // 1. If cancel is pressed
        if (!file) {
            // In 'Create' mode, we might want to reset.
            // In 'Edit' mode, we might want to keep the old image.
            // Safest UI behavior: just hide the error feedback.
            feedback.style.display = 'none';
            return;
        }

        // 2. Validate Type
        // We check if the file type is in our allowed list
        if (!allowed.includes(file.type)) {
            feedback.style.display = 'block';
            feedback.textContent = 'Invalid file type. Allowed: jpg, png, webp, gif, bmp, ico, tiff, avif.';
            this.value = ''; // Reset input
            return;
        }

        // 3. Validate Size
        if (file.size > maxSizeBytes) {
            feedback.style.display = 'block';
            feedback.textContent = `File too large. Max allowed size is ${maxSizeMB}MB.`;
            this.value = ''; // Reset input
            return;
        }

        // 4. Success - Show Preview
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
