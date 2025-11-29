/**
 * FAQ Form Handler
 */
document.addEventListener('DOMContentLoaded', function() {
    const languages = ['en', 'fr', 'ar'];

    languages.forEach(lang => {
        // Note: IDs in Blade are now expected to be 'answer_en' (hidden) and 'editor_answer_en' (visual)
        const editorId = `editor_answer_${lang}`;
        const hiddenId = `answer_${lang}`;

        const editorElement = document.getElementById(editorId);
        const hiddenInput = document.getElementById(hiddenId);

        if (editorElement && hiddenInput) {
            // 1. Initialize Quill
            const quill = new Quill(`#${editorId}`, {
                theme: 'snow',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'header': [1, 2, 3, false] }],
                        ['clean']
                    ]
                }
            });

            // 2. CRITICAL: Attach Quill instance to the HIDDEN input.
            // This allows translationHandler.js to find "answer_fr" and update the editor.
            hiddenInput.__quill = quill;
            editorElement.__quill = quill; // Attach to div too for individual buttons

            // 3. Initial Data Load
            if (hiddenInput.value) {
                quill.root.innerHTML = hiddenInput.value;
            }

            // 4. Sync on Change
            quill.on('text-change', function() {
                hiddenInput.value = quill.root.innerHTML;
            });
        }
    });
});
