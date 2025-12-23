/**
 * Common Image Preview & Validation Script
 * Handles file size check (2MB), type validation, and live preview.
 */
function setupImagePreview(inputId, previewId, feedbackId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    const feedback = document.getElementById(feedbackId);

    // Config
    const allowed = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp', 'image/bmp', 'image/x-icon', 'image/tiff', 'image/avif'];
    const maxSize = 2 * 1024 * 1024; // 2MB
    const defaultIcon = preview ? preview.src : ''; // Save original source

    if (!input || !preview || !feedback) return; // Exit if elements don't exist

    input.addEventListener('change', function(e) {
        const file = this.files && this.files[0];

        // 1. If no file selected (cancel pressed), just hide error
        if (!file) {
            feedback.style.display = 'none';
            return;
        }

        // 2. Validate Type
        if (!allowed.includes(file.type)) {
            feedback.style.display = 'block';
            feedback.textContent = 'Invalid file type. Please upload an image.';
            this.value = ''; // Reset input
            // Optional: Revert preview to default if strictly needed,
            // but usually keeping the old image visible is better UI.
            return;
        }

        // 3. Validate Size
        if (file.size > maxSize) {
            feedback.style.display = 'block';
            feedback.textContent = 'File too large. Max allowed size is 2MB.';
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

// Auto-initialize based on common IDs used in your project
document.addEventListener('DOMContentLoaded', function() {
    // Try initializing for Category Form
    setupImagePreview('category-icon', 'iconPreview', 'iconFeedback');

    // Try initializing for SubCategory Form
    setupImagePreview('sub-category-icon', 'iconPreview', 'iconFeedback');
});
