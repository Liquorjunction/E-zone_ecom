document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // 1. NAVIGATION & VALIDATION LOGIC (FIXED)
    // ==========================================
    const nextBtns = document.querySelectorAll('.next-step');
    const prevBtns = document.querySelectorAll('.prev-step');

    /**
     * Helper function to manually switch tabs.
     * This forces the browser to hide all other steps, preventing them from stacking.
     */
    function switchTab(targetId) {
        // 1. Hide ALL tab panes (remove show/active classes)
        document.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('show', 'active');
        });

        // 2. Deactivate ALL nav pills
        document.querySelectorAll('.nav-link').forEach(nav => {
            nav.classList.remove('active');
        });

        // 3. Show the TARGET tab pane
        const targetPane = document.getElementById(targetId);
        if (targetPane) {
            targetPane.classList.add('show', 'active');
        }

        // 4. Activate the TARGET nav pill
        const targetBtn = document.getElementById(`${targetId}-tab`);
        if (targetBtn) {
            targetBtn.classList.remove('disabled'); // Enable the step
            targetBtn.classList.add('active');      // Highlight it
        }

        // 5. Scroll to top of the form
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // --- Handle "Next" Buttons ---
    nextBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            // A. Validate inputs in the CURRENT visible step
            let currentPane = this.closest('.tab-pane');
            let inputs = currentPane.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                // Check if empty
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid'); // Add red border

                    // Remove red border as soon as user types
                    input.addEventListener('input', function() {
                        this.classList.remove('is-invalid');
                    }, { once: true });
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                alert('Please fill in all required fields marked with *.');
                return; // Stop here if invalid
            }

            // B. If valid, switch to next step
            const nextTabId = this.getAttribute('data-next'); // e.g., 'step2'
            switchTab(nextTabId);
        });
    });

    // --- Handle "Previous" Buttons ---
    prevBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const prevTabId = this.getAttribute('data-prev'); // e.g., 'step1'
            switchTab(prevTabId);
        });
    });


    // ==========================================
    // 2. DYNAMIC SPECIFICATIONS LOGIC
    // ==========================================
    const addSpecBtns = document.querySelectorAll('.add-spec-btn');

    // Event Delegation: Handles clicks on ".remove-spec-row" for both existing AND new rows
    document.querySelectorAll('[id^="spec-table-"]').forEach(table => {
        table.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-spec-row') || e.target.closest('.remove-spec-row')) {
                e.target.closest('tr').remove();
            }
        });
    });

    addSpecBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const lang = this.getAttribute('data-lang');
            const tbody = document.querySelector(`#spec-table-${lang} tbody`);

            // Generate Unique Index: Timestamp + Random number
            const index = 'new_' + Date.now() + Math.floor(Math.random() * 1000);

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input type="text" name="specifications[${lang}][${index}][title]" class="form-control form-control-sm" placeholder="Title">
                </td>
                <td>
                    <input type="text" name="specifications[${lang}][${index}][value]" class="form-control form-control-sm" placeholder="Value">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm py-0 px-2 remove-spec-row">
                        &times;
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    });


    // ==========================================
    // 3. DYNAMIC VARIANTS LOGIC
    // ==========================================
    const addVariantBtn = document.getElementById('add-variant-btn');
    const variantsTableBody = document.querySelector('#variants-table tbody');
    const noVariantsRow = document.getElementById('no-variants-row');

    // Event Delegation for Removing Variants
    if(variantsTableBody) {
        variantsTableBody.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-variant') || e.target.closest('.remove-variant')) {
                e.target.closest('tr').remove();

                // If no rows left (excluding the "no-variants" message row), show the message again
                const visibleRows = Array.from(variantsTableBody.querySelectorAll('tr')).filter(r => r.id !== 'no-variants-row');

                if (visibleRows.length === 0 && noVariantsRow) {
                    noVariantsRow.style.display = 'table-row';
                }
            }
        });
    }

    if(addVariantBtn) {
        addVariantBtn.addEventListener('click', function() {
            // Hide "No variants added" message
            if (noVariantsRow) noVariantsRow.style.display = 'none';

            // Unique Index
            const index = 'new_' + Date.now();

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input type="text" name="variants[${index}][name][en]" class="form-control form-control-sm variant-name-input" placeholder="e.g. Red / Large" required>
                    <input type="hidden" name="variants[${index}][name][fr]" class="variant-name-fr">
                    <input type="hidden" name="variants[${index}][name][ar]" class="variant-name-ar">
                </td>
                <td>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">$</span>
                        <input type="number" name="variants[${index}][price]" step="0.01" class="form-control" placeholder="0.00" required>
                    </div>
                </td>
                <td>
                    <span class="badge bg-secondary">Auto</span>
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-danger btn-sm remove-variant">Delete</button>
                </td>
            `;
            variantsTableBody.appendChild(tr);

            // Sync Logic: Copy EN name to Hidden FR/AR inputs
            const enInput = tr.querySelector('.variant-name-input');
            const frInput = tr.querySelector('.variant-name-fr');
            const arInput = tr.querySelector('.variant-name-ar');

            enInput.addEventListener('input', function() {
                frInput.value = this.value;
                arInput.value = this.value;
            });
        });
    }
});
