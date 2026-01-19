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

            // 4. Character limit setup (English only) and sync on change
            const maxChars = (lang === 'en') ? 150 : null;

            // create a small counter element under the editor
            let counter = document.createElement('div');
            counter.className = 'small text-muted mt-1';
            if (maxChars) counter.textContent = `0/${maxChars} characters`;
            editorElement.parentNode.insertBefore(counter, editorElement.nextSibling);

            // helper to get plain text length (without trailing newline)
            const getPlainLength = () => {
                return quill.getText().replace(/\n$/, '').length;
            };

            // sync and enforce limit
            quill.on("text-change", function (delta, oldDelta, source) {
                if (maxChars) {
                    let plainLen = getPlainLength();
                    if (plainLen > maxChars) {
                        // remove excess characters from the end
                        const excess = plainLen - maxChars;
                        const currentLength = quill.getLength(); // includes trailing newline
                        // deleteText takes index and length; delete from (maxChars) for `excess` chars
                        quill.deleteText(maxChars, excess, 'silent');
                        plainLen = getPlainLength();
                        // move cursor to end of allowed text
                        const sel = quill.getSelection();
                        if (sel) {
                            quill.setSelection(Math.min(sel.index, maxChars), 0, 'silent');
                        }
                    }
                    counter.textContent = `${plainLen}/${maxChars} characters`;
                    counter.classList.toggle('text-danger', plainLen >= maxChars);
                }

                // always sync HTML to hidden input
                hiddenInput.value = quill.root.innerHTML;
            });
        }
    });
});
