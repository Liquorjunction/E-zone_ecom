document.addEventListener('DOMContentLoaded', function() {

    // -----------------------------------------------------------
    // 1. INDIVIDUAL TRANSLATE BUTTONS (Existing Logic)
    // -----------------------------------------------------------
    const translateButtons = document.querySelectorAll('.btn-translate-auto');
    translateButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const btn = this;
            const originalText = btn.innerHTML;
            const isQuill = btn.hasAttribute('data-quill');
            const sourceId = btn.getAttribute('data-source');
            const targetBase = btn.getAttribute('data-target-base');

            let sourceContent = '';

            if (isQuill) {
                const editorEl = document.getElementById(sourceId);
                if (editorEl && editorEl.__quill) {
                    sourceContent = editorEl.__quill.root.innerHTML;
                } else {
                    sourceContent = document.getElementById(sourceId).querySelector('.ql-editor').innerHTML;
                }
            } else {
                const inputEl = document.getElementById(sourceId);
                if (inputEl) sourceContent = inputEl.value;
            }

            if (!sourceContent || sourceContent.trim() === '') {
                alert('Please enter text in English first.');
                return;
            }

            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            btn.disabled = true;

            axios.post(btn.getAttribute('data-url'), { text: sourceContent })
            .then(function (response) {
                if (response.data.success) {
                    const trans = response.data.translations;
                    updateField(targetBase + '_fr', trans.fr, isQuill);
                    updateField(targetBase + '_ar', trans.ar, isQuill);
                    showSuccessToast();
                }
            })
            .catch(handleError)
            .finally(() => { btn.innerHTML = originalText; btn.disabled = false; });
        });
    });

    // -----------------------------------------------------------
    // 2. BATCH "TRANSLATE ALL" BUTTON (New Logic)
    // -----------------------------------------------------------
    const batchButton = document.querySelector('.btn-translate-all');

    if (batchButton) {
        batchButton.addEventListener('click', function(e) {
            e.preventDefault();
            const btn = this;
            const originalText = btn.innerHTML;

            // Read the fields to translate from data attribute (e.g., ["title", "description"])
            const fieldKeys = JSON.parse(btn.getAttribute('data-fields'));
            const payload = {};
            let hasData = false;

            // Gather data from all English fields
            fieldKeys.forEach(key => {
                const el = document.getElementById(key + '_en');
                if (el && el.value.trim() !== '') {
                    payload[key] = el.value;
                    hasData = true;
                }
            });

            if (!hasData) {
                alert('Please fill in at least one English field.');
                return;
            }

            // Show Loading
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Translating All...';
            btn.disabled = true;

            // Send Batch Request
            axios.post(btn.getAttribute('data-url'), { fields: payload })
            .then(function (response) {
                if (response.data.success) {
                    const allTrans = response.data.translations;

                    // Loop through results and update fields
                    Object.keys(allTrans).forEach(key => {
                        const trans = allTrans[key];
                        updateField(key + '_fr', trans.fr, false);
                        updateField(key + '_ar', trans.ar, false);
                    });

                    showSuccessToast();
                }
            })
            .catch(handleError)
            .finally(() => { btn.innerHTML = originalText; btn.disabled = false; });
        });
    }

    // --- Helpers ---

    function updateField(elementId, content, isQuill) {
        const el = document.getElementById(elementId);
        if (!el) return;

        if (isQuill && el.__quill) {
            el.__quill.root.innerHTML = content;
            // Update hidden input for Quill if it exists
            const hidden = document.getElementById('hidden_' + elementId); // simple guess
            if(hidden) hidden.value = content;
        } else {
            el.value = content;
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
