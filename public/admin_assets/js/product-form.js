document.addEventListener("DOMContentLoaded", function () {
    // ==========================================
    // 1. QUILL EDITORS HANDLING (Init first to bind events)
    // ==========================================
    const quillEditors = {};

    function initQuill(id, hiddenInputId) {
        const editorElement = document.getElementById(id);
        if (!editorElement) return;

        // Initialize Quill
        const quill = new Quill(`#${id}`, {
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
            }
        });
        quillEditors[id] = { quill, hiddenInputId };

        const hiddenInput = document.getElementById(hiddenInputId);

        // CRITICAL: Attach Quill instance to hidden input for translation handler
        if (hiddenInput) {
            hiddenInput.__quill = quill;
        }
        // Also attach to editor div for fallback
        editorElement.__quill = quill;

        // A. Load initial data
        if (hiddenInput && hiddenInput.value) {
            quill.root.innerHTML = hiddenInput.value;
        }

        // B. Real-time Sync (Fixes "Next" button validation issue)
        quill.on("text-change", function () {
            if (hiddenInput) {
                // If editor is empty (just <p><br></p>), set hidden input to empty string
                if (quill.getText().trim().length === 0) {
                    hiddenInput.value = "";
                } else {
                    hiddenInput.value = quill.root.innerHTML;
                }
                // Trigger 'input' event on hidden field to clear validation errors if any
                hiddenInput.dispatchEvent(new Event("input"));
            }
        });
    }

    // Initialize for all supported languages
    ["en", "fr", "ar"].forEach((lang) => {
        // Updated to match new blade ID format: description_en instead of description-input-en
        initQuill(`quill-description-${lang}`, `description_${lang}`);
    });

    // ==========================================
    // 2. NAVIGATION & VALIDATION LOGIC
    // ==========================================
    const nextBtns = document.querySelectorAll(".next-step");
    const prevBtns = document.querySelectorAll(".prev-step");

    function switchTab(targetId, skipValidation = false) {
        // Prevent Bootstrap's scrollspy from interfering
        const mainContentContainer = document.getElementById("pills-tabContent-product");
        const mainNavContainer = document.getElementById("pills-tab-product");

        if (!mainContentContainer || !mainNavContainer) return;

        // 1. Hide ONLY the main step panes (Step 1, Step 2, Step 3)
        mainContentContainer.querySelectorAll(":scope > .tab-pane").forEach((pane) => {
            pane.classList.remove("show", "active");
        });

        // 2. Deactivate ONLY the main step nav links
        mainNavContainer.querySelectorAll(".nav-link").forEach((nav) => {
            nav.classList.remove("active", "show");
            nav.setAttribute("aria-selected", "false");
        });

        // 3. Activate target step
        const targetPane = document.getElementById(targetId);
        const targetBtn = document.getElementById(`${targetId}-tab`);

        if (targetPane) {
            targetPane.classList.add("show", "active");
        }

        if (targetBtn) {
            targetBtn.classList.remove("disabled");
            targetBtn.classList.add("active", "show");
            targetBtn.setAttribute("aria-selected", "true");
        }

        // Scroll to top smoothly
        window.scrollTo({ top: 0, behavior: "smooth" });
    }

    // Prevent Bootstrap's default pill behavior from interfering with our custom logic
    const stepTabs = document.querySelectorAll("#pills-tab-product .nav-link");
    stepTabs.forEach((tab) => {
        tab.addEventListener("click", function(e) {
            // Only prevent default if the tab is not disabled and not using next/prev buttons
            if (!this.classList.contains("disabled") &&
                !this.classList.contains("next-step") &&
                !this.classList.contains("prev-step")) {
                e.preventDefault();
                e.stopPropagation();

                // Get the target step from href
                const targetId = this.getAttribute("href").substring(1);
                switchTab(targetId, true);
            }
        });
    });

    // Isolate language tabs from step navigation - prevent active state bleed
    const languageTabs = document.querySelectorAll("#langTabBasic .nav-link, #langTabSpecs .nav-link");
    languageTabs.forEach((langTab) => {
        langTab.addEventListener("shown.bs.tab", function(e) {
            // Prevent Bootstrap from applying active state to step pills
            e.stopPropagation();

            // Ensure only language tabs within the same group are affected
            const parentTabList = this.closest('[role="tablist"]');
            if (parentTabList) {
                parentTabList.querySelectorAll('.nav-link').forEach(link => {
                    link.classList.remove('active');
                    link.setAttribute('aria-selected', 'false');
                });
                this.classList.add('active');
                this.setAttribute('aria-selected', 'true');
            }
        });
    });

    nextBtns.forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const currentPane = this.closest(".tab-pane");

            // Select standard inputs AND hidden inputs that are required (like description)
            const inputs = currentPane.querySelectorAll(
                "input[required], select[required], textarea[required]"
            );
            let isValid = true;

            inputs.forEach((input) => {
                // Check value validity
                if (!input.value || !input.value.trim()) {
                    isValid = false;

                    // Visual feedback
                    input.classList.add("is-invalid");

                    // If it's a hidden Quill input, highlight the editor container
                    if (
                        input.type === "hidden" &&
                        input.id.includes("description_")
                    ) {
                        const editorId = input.id.replace(
                            "description_",
                            "quill-description-"
                        );
                        const editorDiv = document.getElementById(editorId);
                        if (editorDiv)
                            editorDiv.style.border = "1px solid #dc3545";
                    }

                    // Remove error on input
                    input.addEventListener(
                        "input",
                        function () {
                            this.classList.remove("is-invalid");
                            if (
                                this.type === "hidden" &&
                                this.id.includes("description_")
                            ) {
                                const editorId = this.id.replace(
                                    "description_",
                                    "quill-description-"
                                );
                                const editorDiv =
                                    document.getElementById(editorId);
                                if (editorDiv) editorDiv.style.border = ""; // Reset border
                            }
                        },
                        { once: true }
                    );
                } else {
                    input.classList.remove("is-invalid");
                }
            });

            if (!isValid) {
                // Simple toast or alert
                alert(
                    "Please fill in all required fields (marked with *). Check English Description if empty."
                );
                return;
            }

            switchTab(this.getAttribute("data-next"));
        });
    });

    prevBtns.forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            switchTab(this.getAttribute("data-prev"));
        });
    });

    // ==========================================
    // 3. SELECT2 INITIALIZATION & CATEGORY -> SUBCATEGORY CASCADE
    // ==========================================

    // Initialize Select2 on all dropdowns with the select2-dropdown class
    if (typeof $ !== 'undefined' && $.fn.select2) {
        // Common Select2 configuration
        const select2Config = {
            allowClear: true,
            width: '100%',
            dropdownAutoWidth: false,
            minimumResultsForSearch: 0 // Always show search
        };

        // Brand Select2
        $('#brand-select').select2({
            ...select2Config,
            placeholder: 'Select a brand'
        });

        // Category Select2
        $('#category-select').select2({
            ...select2Config,
            placeholder: 'Select a category'
        });

        // Store all subcategory options on page load (before updates)
        const allSubcategoryOptions = [];
        $('#subcategory-select option').each(function() {
            allSubcategoryOptions.push({
                id: $(this).val(),
                text: $(this).text(),
                categoryId: $(this).attr('data-category')
            });
        });

        // Subcategory Select2 with dynamic filtering
        const $subcategorySelect = $('#subcategory-select');
        $subcategorySelect.select2({
            ...select2Config,
            placeholder: 'Select a subcategory'
        });

        // Function to filter subcategories based on selected category
        function filterSubcategories() {
            const selectedCategoryId = $('#category-select').val();
            const currentValue = $subcategorySelect.val();

            // Destroy Select2 temporarily to update options
            $subcategorySelect.select2('destroy');

            // Clear and rebuild options
            $subcategorySelect.empty();
            $subcategorySelect.append('<option value="">Select Sub Category</option>');

            if (selectedCategoryId) {
                // Add only matching subcategories
                allSubcategoryOptions.forEach(function(option) {
                    if (!option.categoryId || option.categoryId === selectedCategoryId) {
                        const $option = $('<option></option>')
                            .val(option.id)
                            .text(option.text)
                            .attr('data-category', option.categoryId);
                        $subcategorySelect.append($option);
                    }
                });

                // Enable subcategory dropdown
                $subcategorySelect.prop('disabled', false);
            } else {
                // Disable subcategory dropdown when no category is selected
                $subcategorySelect.prop('disabled', true);
                $subcategorySelect.val('').trigger('change');
            }

            // Re-initialize Select2
            $subcategorySelect.select2({
                ...select2Config,
                placeholder: selectedCategoryId ? 'Select a subcategory' : 'Please select a category first'
            });

            // Restore previous value if still valid
            if (selectedCategoryId && currentValue && $subcategorySelect.find('option[value="' + currentValue + '"]').length > 0) {
                $subcategorySelect.val(currentValue).trigger('change');
            }
        }

        // Listen for category changes
        $('#category-select').on('change', function() {
            filterSubcategories();
        });

        // Run on page load with slight delay to ensure DOM is ready
        setTimeout(filterSubcategories, 100);
    } else {
        console.warn('Select2 is not loaded. Falling back to basic dropdowns.');

        // Fallback to basic filtering if Select2 is not available
        const categorySelect = document.querySelector('select[name="category_id"]');
        const subSelect = document.querySelector('select[name="sub_category_id"]');

        function filterSubcategoriesBasic() {
            if (!subSelect || !categorySelect) return;

            const selectedCat = categorySelect.value;
            const options = Array.from(subSelect.querySelectorAll("option"));

            if (!selectedCat) {
                // Disable subcategory when no category is selected
                subSelect.disabled = true;
                subSelect.value = "";
                return;
            }

            // Enable subcategory when category is selected
            subSelect.disabled = false;

            // Reset selection if current option is becoming hidden
            let currentValid = false;

            options.forEach((opt) => {
                const catId = opt.getAttribute("data-category");
                if (!catId) return; // Skip placeholder

                if (String(catId) === String(selectedCat)) {
                    opt.style.display = "";
                    if (subSelect.value === opt.value) currentValid = true;
                } else {
                    opt.style.display = "none";
                }
            });

            if (!currentValid && subSelect.value !== "") {
                subSelect.value = "";
            }
        }

        if (categorySelect) {
            categorySelect.addEventListener("change", filterSubcategoriesBasic);
            filterSubcategoriesBasic(); // Run on load
        }
    }

    // ==========================================
    // 4. DYNAMIC SPECIFICATIONS
    // ==========================================
    document.body.addEventListener("click", function (e) {
        if (
            e.target.classList.contains("remove-spec-row") ||
            e.target.closest(".remove-spec-row")
        ) {
            e.target.closest("tr").remove();
        }
    });

    document.querySelectorAll(".add-spec-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            // We use a common timestamp index so EN/FR/AR rows share the same array Key
            const index = Date.now();
            const langs = ["en", "fr", "ar"];

            langs.forEach((lang) => {
                const tbody = document.querySelector(
                    `#spec-table-${lang} tbody`
                );
                if (!tbody) return;

                const row = `
                    <tr>
                        <td>
                            <input type="text" name="specifications[${lang}][${index}][title]" class="form-control form-control-sm" placeholder="Feature">
                        </td>
                        <td>
                            <input type="text" name="specifications[${lang}][${index}][value]" class="form-control form-control-sm" placeholder="Value">
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm py-0 px-2 remove-spec-row">&times;</button>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML("beforeend", row);
            });
        });
    });

    // ==========================================
    // 5. DYNAMIC VARIANTS
    // ==========================================
    const addVariantBtn = document.getElementById("add-variant-btn");
    const variantsTbody = document.querySelector("#variants-table tbody");
    const noVariantsRow = document.getElementById("no-variants-row");

    // Remove Variant
    if (variantsTbody) {
        variantsTbody.addEventListener("click", function (e) {
            if (e.target.classList.contains("remove-variant")) {
                e.target.closest("tr").remove();

                // Show "no variants" message if empty
                const rows = variantsTbody.querySelectorAll("tr");
                let hasRealRows = false;
                rows.forEach((r) => {
                    if (r.id !== "no-variants-row") hasRealRows = true;
                });

                if (!hasRealRows && noVariantsRow) {
                    noVariantsRow.style.display = "table-row";
                }
            }
        });
    }

    // Add Variant
    if (addVariantBtn && variantsTbody) {
        addVariantBtn.addEventListener("click", function () {
            if (noVariantsRow) noVariantsRow.style.display = "none";

            // Prefix with 'new_' to ensure we don't conflict with DB IDs in edit mode
            const index = "new_" + Date.now();

            const rowHtml = `
                <tr>
                    <td>
                        <input type="text" name="variants[${index}][name][en]" class="form-control form-control-sm variant-name-input" placeholder="e.g. Red / Large" required>
                        <input type="hidden" name="variants[${index}][name][fr]" class="variant-name-fr">
                        <input type="hidden" name="variants[${index}][name][ar]" class="variant-name-ar">
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">$</span>
                            <input type="number" name="variants[${index}][price]" step="0.01" min="0" class="form-control" placeholder="0.00" required>
                        </div>
                    </td>
                    <td><span class="badge bg-secondary">Auto-Gen</span></td>
                    <td class="text-end">
                        <button type="button" class="btn btn-danger btn-sm remove-variant">Delete</button>
                    </td>
                </tr>
            `;
            variantsTbody.insertAdjacentHTML("beforeend", rowHtml);

            // Sync Name logic (simple copy to other languages as default)
            const newRow = variantsTbody.lastElementChild;
            const enInput = newRow.querySelector(".variant-name-input");
            const frInput = newRow.querySelector(".variant-name-fr");
            const arInput = newRow.querySelector(".variant-name-ar");

            enInput.addEventListener("input", function () {
                frInput.value = this.value;
                arInput.value = this.value;
            });
        });
    }
});
