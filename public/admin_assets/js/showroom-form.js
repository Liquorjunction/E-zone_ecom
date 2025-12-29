// --- Translate All Fields Button Handler (Shipping Return style, with DOM IDs) ---
$(document).on("click", "#btn-translate-all", function (e) {
    e.preventDefault();
    var btn = $(this);
    var url = btn.data("url");
    var fields = btn.data("fields");
    if (typeof fields === 'string') fields = JSON.parse(fields);
    var data = {};
    // Collect English values using DOM IDs for consistency
    fields.forEach(function(field) {
        var input = document.getElementById(field + '_en');
        data[field] = input ? input.value : '';
    });
    btn.prop("disabled", true).text("Translating...");
    axios.post(url, {
        fields: data,
        from: 'en',
        to: ['fr', 'ar']
    }).then(function (resp) {
        var result = resp.data;
        fields.forEach(function(field) {
            // French
            if (result.fr && result.fr[field] !== undefined) {
                var inputFr = document.getElementById(field + '_fr');
                if (inputFr) inputFr.value = result.fr[field];
            }
            // Arabic
            if (result.ar && result.ar[field] !== undefined) {
                var inputAr = document.getElementById(field + '_ar');
                if (inputAr) inputAr.value = result.ar[field];
            }
        });
        btn.prop("disabled", false).text("Translate All Fields");
    }).catch(function (err) {
        alert("Translation failed. Please try again.");
        btn.prop("disabled", false).text("Translate All Fields");
    });
});
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

                        ["clean"] // remove formatting
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

    // Step navigation (improved for Bootstrap 5)
    const nextBtns = document.querySelectorAll(".next-step");
    const prevBtns = document.querySelectorAll(".prev-step");

    nextBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const nextTabId = this.getAttribute("data-next");
            const triggerEl = document.querySelector(`#${nextTabId}-tab`);
            if (triggerEl) {
                triggerEl.classList.remove("disabled");
                if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    const tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
                    tab.show();
                } else {
                    // Fallback: always use the main step tab-content
                    const tabContent = document.getElementById('pills-tabContent-edit');
                    if (tabContent) {
                        Array.from(tabContent.children).forEach(pane => pane.classList.remove('show', 'active'));
                        const nextPane = tabContent.querySelector(`#${nextTabId}`);
                        if (nextPane) {
                            nextPane.classList.add('show', 'active');
                        }
                    }
                }
            }
        });
    });

    prevBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            const prevTabId = this.getAttribute("data-prev");
            const triggerEl = document.querySelector(`#${prevTabId}-tab`);
            if (triggerEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Tab) {
                    const tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
                    tab.show();
                } else {
                    const tabContent = document.getElementById('pills-tabContent-edit');
                    if (tabContent) {
                        Array.from(tabContent.children).forEach(pane => pane.classList.remove('show', 'active'));
                        const prevPane = tabContent.querySelector(`#${prevTabId}`);
                        if (prevPane) {
                            prevPane.classList.add('show', 'active');
                        }
                    }
                }
            }
        });
    });
});
