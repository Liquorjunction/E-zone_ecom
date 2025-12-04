document.addEventListener('DOMContentLoaded', function() {
    // Initialize English Editor
    if (document.getElementById('editor_en')) {
        var quillEn = new Quill('#editor_en', {
            theme: 'snow',
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
            }
        });
        // CRITICAL FIX: Attach instance to DOM element
        document.getElementById('editor_en').__quill = quillEn;

        // Update hidden input on change
        quillEn.on('text-change', function() {
            document.getElementById('hidden_description_en').value = quillEn.root.innerHTML;
        });
    }

    // Initialize French Editor
    if (document.getElementById('editor_fr')) {
        var quillFr = new Quill('#editor_fr', {
            theme: 'snow',
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
            }
        });
        document.getElementById('editor_fr').__quill = quillFr;

        quillFr.on('text-change', function() {
            document.getElementById('hidden_description_fr').value = quillFr.root.innerHTML;
        });
    }

    // Initialize Arabic Editor
    if (document.getElementById('editor_ar')) {
        var quillAr = new Quill('#editor_ar', {
            theme: 'snow',
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
            }
        });
        document.getElementById('editor_ar').__quill = quillAr;

        quillAr.on('text-change', function() {
            document.getElementById('hidden_description_ar').value = quillAr.root.innerHTML;
        });
    }

    // Form Submission Handler (To ensure empty editors don't block submission if optional)
    const form = document.getElementById('privacyForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Populate hidden inputs one last time before submit
            if(document.getElementById('editor_en').__quill) {
                document.getElementById('hidden_description_en').value = document.getElementById('editor_en').__quill.root.innerHTML;
            }
            if(document.getElementById('editor_fr').__quill) {
                document.getElementById('hidden_description_fr').value = document.getElementById('editor_fr').__quill.root.innerHTML;
            }
            if(document.getElementById('editor_ar').__quill) {
                document.getElementById('hidden_description_ar').value = document.getElementById('editor_ar').__quill.root.innerHTML;
            }
        });
    }
});
