document.addEventListener('DOMContentLoaded', function() {
    // Initialize English Editor
    if (document.getElementById('editor_content_en')) {
        var quillEn = new Quill('#editor_content_en', {
            theme: 'snow'
        });
        // CRITICAL FIX: Attach instance to DOM element
        document.getElementById('editor_content_en').__quill = quillEn;

        // Update hidden textarea on change
        quillEn.on('text-change', function() {
            document.getElementById('content_en').value = quillEn.root.innerHTML;
        });
    }

    // Initialize French Editor
    if (document.getElementById('editor_content_fr')) {
        var quillFr = new Quill('#editor_content_fr', {
            theme: 'snow'
        });
        document.getElementById('editor_content_fr').__quill = quillFr;

        quillFr.on('text-change', function() {
            document.getElementById('content_fr').value = quillFr.root.innerHTML;
        });
    }

    // Initialize Arabic Editor
    if (document.getElementById('editor_content_ar')) {
        var quillAr = new Quill('#editor_content_ar', {
            theme: 'snow'
        });
        document.getElementById('editor_content_ar').__quill = quillAr;

        quillAr.on('text-change', function() {
            document.getElementById('content_ar').value = quillAr.root.innerHTML;
        });
    }
});
