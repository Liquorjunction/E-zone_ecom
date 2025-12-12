/**
 * FAQ Form Handler
 */
document.addEventListener("DOMContentLoaded", function () {
    const languages = ["en", "fr", "ar"];

    languages.forEach((lang) => {
        // Note: IDs in Blade are now expected to be 'answer_en' (hidden) and 'editor_answer_en' (visual)
        const editorId = `editor_answer_${lang}`;
        const hiddenId = `answer_${lang}`;

        const editorElement = document.getElementById(editorId);
        const hiddenInput = document.getElementById(hiddenId);

        if (editorElement && hiddenInput) {
            // 1. Initialize Quill
            const quill = new Quill(`#${editorId}`, {
                theme: "snow",
                modules: {
                    toolbar: [
                        [{ font: [] }],
                        [{ size: ["small", false, "large", "huge"] }],
                        [{ header: [1, 2, 3, 4, 5, 6, false] }],

                        ["bold", "italic", "underline", "strike"], // text styles
                        [{ color: [] }, { background: [] }], // color pickers
                        [{ script: "sub" }, { script: "super" }], // subscript/superscript

                        [{ list: "ordered" }, { list: "bullet" }],
                        [{ list: "check" }], // checkbox lists
                        [{ indent: "-1" }, { indent: "+1" }], // indentation
                        [{ align: [] }], // align: left, center, right, justify
                        [{ direction: "rtl" }], // RTL text direction

                        ["blockquote", "code-block"], // blocks
                        [{ header: 1 }, { header: 2 }], // specific headers

                        ["link", "image", "video", "formula"], // embeds

                        ["clean"], // remove formatting
                        ["undo", "redo"], // requires custom handler
                    ],
                },
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
            quill.on("text-change", function () {
                hiddenInput.value = quill.root.innerHTML;
            });
        }
    });
});
