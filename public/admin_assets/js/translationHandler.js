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

            axios.post(btn.getAttribute('data-url'), { text: sourceContent })
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
                const el = document.getElementById(key + '_en');
                if (el) {
                    // Check if it's a Quill field or regular input
                    const val = el.__quill ? el.__quill.root.innerHTML : el.value;

                    if (val && val.trim() !== '') {
                        payload[key] = val;
                        hasData = true;
                    }
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
        if (!el) return;

        // Check if this element is linked to a Quill instance
        if (el.__quill) {
            el.__quill.root.innerHTML = content; // Update Visual Editor
            el.value = content;                  // Update Hidden Input
        } else {
            // Standard Input/Textarea
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
