document.addEventListener('DOMContentLoaded', function() {

    // -----------------------------------------------------------
    // 1. INDIVIDUAL TRANSLATE BUTTONS
    // -----------------------------------------------------------
    const translateButtons = document.querySelectorAll('.btn-translate-auto');
    translateButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const btn = this;
            const originalText = btn.innerHTML;
            const isQuill = btn.hasAttribute('data-quill'); // Legacy flag, but we'll use object detection now
            const sourceId = btn.getAttribute('data-source');
            const targetBase = btn.getAttribute('data-target-base');

            let sourceContent = '';
            const sourceEl = document.getElementById(sourceId);

            // Get Content
            if (sourceEl) {
                // If it's a Quill container (div) OR a textarea with attached Quill
                if (sourceEl.__quill) {
                    sourceContent = sourceEl.__quill.root.innerHTML;
                } else {
                    sourceContent = sourceEl.value;
                }
            }

            if (!sourceContent || sourceContent.trim() === '') {
                alert('Please enter text in English first.');
                return;
            }

            // UI Loading State
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            btn.disabled = true;

            const isHtml = isQuill; // If it's Quill, it's HTML
            axios.post(btn.getAttribute('data-url'), { text: sourceContent, is_html: isHtml })
            .then(function (response) {
                if (response.data.success) {
                    const trans = response.data.translations;
                    updateField(targetBase + '_fr', trans.fr);
                    updateField(targetBase + '_ar', trans.ar);
                    showSuccessToast();
                }
            })
            .catch(handleError)
            .finally(() => { btn.innerHTML = originalText; btn.disabled = false; });
        });
    });

    // -----------------------------------------------------------
    // 2. BATCH "TRANSLATE ALL" BUTTON
    // -----------------------------------------------------------
    const batchButton = document.querySelector('.btn-translate-all');

    if (batchButton) {
        batchButton.addEventListener('click', function(e) {
            e.preventDefault();
            const btn = this;
            const originalText = btn.innerHTML;
            const fieldKeys = JSON.parse(btn.getAttribute('data-fields'));
            const payload = {};
            let hasData = false;

            // Gather data
            fieldKeys.forEach(key => {
                let val = '';
                const el = document.getElementById(key + '_en');
                const quillEl = document.getElementById('editor_' + key + '_en');

                if (quillEl && quillEl.__quill) {
                    // Prefer Quill content if available
                    val = quillEl.__quill.root.innerHTML;
                } else if (el) {
                    // Fallback to input/textarea
                    val = el.__quill ? el.__quill.root.innerHTML : el.value;
                }

                if (val && val.trim() !== '') {
                    payload[key] = val;
                    hasData = true;
                }
            });

            if (!hasData) {
                alert('Please fill in at least one English field.');
                return;
            }

            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Translating All...';
            btn.disabled = true;

            axios.post(btn.getAttribute('data-url'), { fields: payload })
            .then(function (response) {
                if (response.data.success) {
                    const allTrans = response.data.translations;
                    Object.keys(allTrans).forEach(key => {
                        const trans = allTrans[key];
                        updateField(key + '_fr', trans.fr);
                        updateField(key + '_ar', trans.ar);
                    });
                    showSuccessToast();
                }
            })
            .catch(handleError)
            .finally(() => { btn.innerHTML = originalText; btn.disabled = false; });
        });
    }

     // --- Helper: Update Field (Smart Detection) ---
    function updateField(elementId, content) {
        const el = document.getElementById(elementId);

        // 1. Check if the element itself is a Quill editor
        if (el && el.__quill) {
            el.__quill.root.innerHTML = content;
            // Update associated hidden input if strictly naming convention follows (e.g. editor_content_en -> content_en)
            const hiddenId = elementId.replace('editor_', '');
            const hiddenEl = document.getElementById(hiddenId);
            if (hiddenEl) hiddenEl.value = content;
            return;
        }

        // 2. Check if there is a corresponding Quill editor for this input
        // (e.g., we received 'content_fr', let's look for 'editor_content_fr')
        const quillEl = document.getElementById('editor_' + elementId);
        if (quillEl && quillEl.__quill) {
            quillEl.__quill.root.innerHTML = content; // Update Visual Editor

            if (el) el.value = content; // Update Hidden Input
            return;
        }

        // 3. Fallback: Standard Input/Textarea
        if (el) {
            el.value = content;
        } else {
            console.warn(`Could not find field to update: ${elementId}`);
        }
    }

    function showSuccessToast() {
        if (typeof window.showToast === 'function') {
            window.showToast('Translation completed successfully!', 'success');
        } else {
            alert('Translation completed!');
        }
    }

    function handleError(error) {
        console.error(error);
        alert('Translation failed. Check console for details.');
    }
});
