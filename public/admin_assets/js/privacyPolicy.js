document.addEventListener('DOMContentLoaded', function() {
    // Initialize English Editor
    if (document.getElementById('editor_en')) {
        var quillEn = new Quill('#editor_en', {
            theme: 'snow'
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
            theme: 'snow'
        });
        document.getElementById('editor_fr').__quill = quillFr;

        quillFr.on('text-change', function() {
            document.getElementById('hidden_description_fr').value = quillFr.root.innerHTML;
        });
    }

    // Initialize Arabic Editor
    if (document.getElementById('editor_ar')) {
        var quillAr = new Quill('#editor_ar', {
            theme: 'snow'
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
