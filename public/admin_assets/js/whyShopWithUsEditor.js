/**
 * Why Shop With Us Form Editor Handler
 */
document.addEventListener("DOMContentLoaded", function () {
    const languages = ["en", "fr", "ar"];

    languages.forEach((lang) => {
        const editorId = `editor_description_${lang}`;
        const hiddenId = `description_${lang}`;

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

                        ["bold", "italic", "underline", "strike"],
                        [{ color: [] }, { background: [] }],
                        [{ script: "sub" }, { script: "super" }],

                        [{ list: "ordered" }, { list: "bullet" }],
                        [{ list: "check" }],
                        [{ indent: "-1" }, { indent: "+1" }],
                        [{ align: [] }],
                        [{ direction: "rtl" }],

                        ["blockquote", "code-block"],
                        [{ header: 1 }, { header: 2 }],

                        ["link", "image", "video", "formula"],

                        ["clean"],
                    ],
                },
            });

            // 2. CRITICAL: Attach Quill instance to the HIDDEN input for translation handler
            hiddenInput.__quill = quill;
            editorElement.__quill = quill;

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
