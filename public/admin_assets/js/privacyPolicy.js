document.addEventListener('DOMContentLoaded', function() {
    // --- 1. Common Configuration ---
    const toolbarOptions = [
        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        [{ 'align': [] }],
        [{ 'direction': 'rtl' }], // Text direction
        ['link', 'image', 'clean']
    ];

    // Variables to store editor instances
    let quillEn = null;
    let quillFr = null;
    let quillAr = null;

    // --- 2. Initialize Editors (Conditional) ---

    // English (Used on both Create & Edit pages)
    const enEditorEl = document.getElementById('editor_en');
    if (enEditorEl) {
        quillEn = new Quill('#editor_en', {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });
    }

    // French (Used on Edit page only)
    const frEditorEl = document.getElementById('editor_fr');
    if (frEditorEl) {
        quillFr = new Quill('#editor_fr', {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });
    }

    // Arabic (Used on Edit page only)
    const arEditorEl = document.getElementById('editor_ar');
    if (arEditorEl) {
        quillAr = new Quill('#editor_ar', {
            theme: 'snow',
            modules: { toolbar: toolbarOptions }
        });
    }

    // --- 3. Handle Form Submission ---
    const form = document.getElementById('privacyForm');
    if (form) {
        form.onsubmit = function() {
            // Sync English Data (Always expected if initialized)
            if (quillEn) {
                const hiddenEn = document.getElementById('hidden_description_en');
                if (hiddenEn) hiddenEn.value = quillEn.root.innerHTML;
            }

            // Sync French Data (Only if initialized)
            if (quillFr) {
                const hiddenFr = document.getElementById('hidden_description_fr');
                if (hiddenFr) hiddenFr.value = quillFr.root.innerHTML;
            }

            // Sync Arabic Data (Only if initialized)
            if (quillAr) {
                const hiddenAr = document.getElementById('hidden_description_ar');
                if (hiddenAr) hiddenAr.value = quillAr.root.innerHTML;
            }

            return true; // Allow form submission
        };
    }
});
