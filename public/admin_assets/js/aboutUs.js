document.addEventListener("DOMContentLoaded", function () {
    // Initialize English Editor
    if (document.getElementById("editor_en")) {
        var quillEn = new Quill("#editor_en", {
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
        // CRITICAL FIX: Attach instance to DOM element
        document.getElementById("editor_en").__quill = quillEn;

        // Update hidden input on change
        quillEn.on("text-change", function () {
            document.getElementById("hidden_description_en").value =
                quillEn.root.innerHTML;
        });
    }

    // Initialize French and Arabic Editors on tab show
    $("#langTab button").on("shown.bs.tab", function (e) {
        var target = $(e.target).data("bs-target");
        if (target == "#fr" && !document.getElementById("editor_fr").__quill) {
            var quillFr = new Quill("#editor_fr", {
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
            document.getElementById("editor_fr").__quill = quillFr;
            quillFr.on("text-change", function () {
                document.getElementById("hidden_description_fr").value =
                    quillFr.root.innerHTML;
            });
        }
        if (target == "#ar" && !document.getElementById("editor_ar").__quill) {
            var quillAr = new Quill("#editor_ar", {
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
            document.getElementById("editor_ar").__quill = quillAr;
            quillAr.on("text-change", function () {
                document.getElementById("hidden_description_ar").value =
                    quillAr.root.innerHTML;
            });
        }
    });

    // --- Translate to Others Button Handler ---
    $(document).on("click", ".btn-translate-auto", function (e) {
        e.preventDefault();
        var btn = $(this);
        var url = btn.data("url");
        var sourceId = btn.data("source");
        var targetBase = btn.data("target-base");
        var isQuill = btn.data("quill");
        var $source = document.getElementById(sourceId);
        var html = "";
        if (isQuill && $source && $source.__quill) {
            html = $source.__quill.root.innerHTML;
        } else if ($source) {
            html = $source.value || $source.innerHTML;
        }
        if (!html) {
            alert("No content to translate.");
            return;
        }
        btn.prop("disabled", true).text("Translating...");
        axios.post(url, {
            html: html,
            from: 'en',
            to: ['fr', 'ar']
        }).then(function (resp) {
            var data = resp.data;
            // French
            if (data.fr && document.getElementById(targetBase + '_fr')) {
                var frEditor = document.getElementById(targetBase + '_fr');
                if (frEditor.__quill) {
                    frEditor.__quill.root.innerHTML = data.fr;
                    document.getElementById('hidden_description_fr').value = data.fr;
                } else {
                    frEditor.innerHTML = data.fr;
                }
            }
            // Arabic
            if (data.ar && document.getElementById(targetBase + '_ar')) {
                var arEditor = document.getElementById(targetBase + '_ar');
                if (arEditor.__quill) {
                    arEditor.__quill.root.innerHTML = data.ar;
                    document.getElementById('hidden_description_ar').value = data.ar;
                } else {
                    arEditor.innerHTML = data.ar;
                }
            }
            btn.prop("disabled", false).text("Translate to Others");
        }).catch(function (err) {
            alert("Translation failed. Please try again.");
            btn.prop("disabled", false).text("Translate to Others");
        });
    });

    // Form Submission Handler (To ensure empty editors don't block submission if optional)
    const form = document.getElementById("aboutUsForm");
    if (form) {
        form.addEventListener("submit", function (e) {
            // Populate hidden inputs one last time before submit
            if (
                document.getElementById("editor_en") &&
                document.getElementById("editor_en").__quill
            ) {
                document.getElementById("hidden_description_en").value =
                    document.getElementById("editor_en").__quill.root.innerHTML;
            }
            if (
                document.getElementById("editor_fr") &&
                document.getElementById("editor_fr").__quill
            ) {
                document.getElementById("hidden_description_fr").value =
                    document.getElementById("editor_fr").__quill.root.innerHTML;
            }
            if (
                document.getElementById("editor_ar") &&
                document.getElementById("editor_ar").__quill
            ) {
                document.getElementById("hidden_description_ar").value =
                    document.getElementById("editor_ar").__quill.root.innerHTML;
            }
        });
    }
});
