// showroom-form.js - Handles Quill editors for create and edit forms

document.addEventListener("DOMContentLoaded", function () {
    // Initialize Quill editors for description fields in all language tabs
    const languages = ["en", "fr", "ar"];
    const quillEditors = {};

    languages.forEach((lang) => {
        const textarea = document.querySelector(
            `textarea[name="description[${lang}]"]`
        );
        if (textarea) {
            // Create a container for Quill
            const container = document.createElement("div");
            container.className = "quill-editor";
            container.style.height = "200px";
            textarea.parentNode.insertBefore(container, textarea);
            textarea.style.display = "none";

            // Initialize Quill
            const quill = new Quill(container, {
                theme: "snow",
                placeholder: `Enter description in ${lang.toUpperCase()}...`,
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

            // Set initial content if exists
            if (textarea.value) {
                quill.root.innerHTML = textarea.value;
            }

            // Update hidden textarea on change
            quill.on("text-change", function () {
                textarea.value = quill.root.innerHTML;
            });

            quillEditors[lang] = quill;
        }
    });

    // Handle tab switching to refresh Quill editors
    const langTabs = document.querySelectorAll("#langTab a, #langTabEdit a");
    langTabs.forEach((tab) => {
        tab.addEventListener("shown.bs.tab", function () {
            // Refresh all Quill editors after tab switch
            setTimeout(() => {
                Object.values(quillEditors).forEach((quill) => {
                    quill.focus();
                    quill.blur();
                });
            }, 100);
        });
    });

    // Form validation before submit
    const form =
        document.getElementById("showroom-form") ||
        document.querySelector('form[action*="showrooms"]');
    if (form) {
        form.addEventListener("submit", function (e) {
            // Ensure all Quill contents are synced to textareas
            Object.values(quillEditors).forEach((quill) => {
                const lang = Object.keys(quillEditors).find(
                    (key) => quillEditors[key] === quill
                );
                const textarea = document.querySelector(
                    `textarea[name="description[${lang}]"]`
                );
                if (textarea) {
                    textarea.value = quill.root.innerHTML;
                }
            });
        });
    }

    // Step navigation (existing functionality)
    const nextBtns = document.querySelectorAll(".next-step");
    const prevBtns = document.querySelectorAll(".prev-step");

    nextBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const nextTabId = this.getAttribute("data-next");
            const triggerEl = document.querySelector(`#${nextTabId}-tab`);
            if (triggerEl) {
                triggerEl.classList.remove("disabled");
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        });
    });

    prevBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const prevTabId = this.getAttribute("data-prev");
            const triggerEl = document.querySelector(`#${prevTabId}-tab`);
            if (triggerEl) {
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
            }
        });
    });
});
