document.addEventListener("DOMContentLoaded", function () {
    // ==========================================
    // 1. QUILL EDITORS HANDLING
    // ==========================================
    const quillEditors = {};

    function initQuill(id, hiddenInputId) {
        const editorElement = document.getElementById(id);
        if (!editorElement) return;
        if (editorElement.classList.contains('ql-container')) return;

        const quill = new Quill(`#${id}`, {
            theme: "snow",
            modules: {
                toolbar: [
                    [{ font: [] }],
                    [{ size: ["small", false, "large", "huge"] }],
                    [{ header: [1, 2, 3, 4, 5, 6, false] }],
                    ["bold", "italic", "underline", "strike"],
                    [{ list: "ordered" }, { list: "bullet" }],
                    [{ indent: "-1" }, { indent: "+1" }],
                    [{ align: [] }],
                    [{ direction: "rtl" }],
                    ["link", "image", "clean"],
                ],
            },
        });

        quillEditors[id] = { quill, hiddenInputId };
        const hiddenInput = document.getElementById(hiddenInputId);

        if (hiddenInput) hiddenInput.__quill = quill;
        if (hiddenInput && hiddenInput.value) quill.root.innerHTML = hiddenInput.value;

        quill.on("text-change", function () {
            if (hiddenInput) {
                hiddenInput.value = quill.getText().trim().length === 0 ? "" : quill.root.innerHTML;
                hiddenInput.dispatchEvent(new Event("input"));
            }
        });
    }

    ["en", "fr", "ar"].forEach((lang) => {
        initQuill(`quill-description-${lang}`, `description_${lang}`);
    });

    // ==========================================
    // 2. NAVIGATION & TABS
    // ==========================================
    const nextBtns = document.querySelectorAll(".next-step");
    const prevBtns = document.querySelectorAll(".prev-step");

    function switchTab(targetId) {
        const triggerEl = document.querySelector(`#${targetId}-tab`);
        if (triggerEl) {
            const tab = new bootstrap.Tab(triggerEl);
            tab.show();
            triggerEl.classList.remove('disabled');
            window.scrollTo({ top: 0, behavior: "smooth" });
        }
    }

    nextBtns.forEach((btn) => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();
            const currentPane = this.closest(".tab-pane");
            const inputs = currentPane.querySelectorAll("[required]");
            let valid = true;
            inputs.forEach(input => {
                if (!input.value || input.value.trim() === "") {
                    valid = false;
                    input.classList.add("is-invalid");
                } else {
                    input.classList.remove("is-invalid");
                }
            });
            if (!valid) {
                alert("Please fill in required fields.");
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
    // 3. SELECT2 & SUBCATEGORY FILTER
    // ==========================================
    if (typeof $ !== 'undefined' && $.fn.select2) {
        const select2Config = { allowClear: true, width: '100%' };
        $('#brand-select, #category-select').select2({ ...select2Config });

        const allSubcategoryOptions = [];
        $('#subcategory-select option').each(function() {
            allSubcategoryOptions.push({
                id: $(this).val(),
                text: $(this).text(),
                categoryId: $(this).attr('data-category')
            });
        });

        const $subcategorySelect = $('#subcategory-select');
        $subcategorySelect.select2({ ...select2Config, placeholder: 'Select subcategory' });

        function filterSubcategories() {
            const selectedCategoryId = $('#category-select').val();
            const currentValue = $subcategorySelect.val();
            $subcategorySelect.empty().append('<option value="">Select Sub Category</option>');

            if (selectedCategoryId) {
                allSubcategoryOptions.forEach(option => {
                    if (!option.categoryId || option.categoryId === selectedCategoryId) {
                        $subcategorySelect.append(new Option(option.text, option.id, false, false));
                    }
                });
                $subcategorySelect.prop('disabled', false);
            } else {
                $subcategorySelect.prop('disabled', true);
            }

            if(currentValue && $subcategorySelect.find(`option[value="${currentValue}"]`).length) {
                $subcategorySelect.val(currentValue);
            }
            $subcategorySelect.trigger('change');
        }

        $('#category-select').on('change', filterSubcategories);
        setTimeout(filterSubcategories, 500);
    }

    // ==========================================
    // 4. SPECIFICATIONS (Dynamic Attributes)
    // ==========================================
    document.body.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-spec-row")) e.target.closest("tr").remove();
    });

    document.querySelectorAll(".add-spec-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            const index = Date.now();
            const lang = this.getAttribute("data-lang");
            const tbody = document.querySelector(`#spec-table-${lang} tbody`);
            if (tbody) {
                const row = `
                    <tr>
                        <td><input type="text" name="specifications[${lang}][${index}][title]" class="form-control form-control-sm" placeholder="Title"></td>
                        <td><input type="text" name="specifications[${lang}][${index}][value]" class="form-control form-control-sm" placeholder="Value"></td>
                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-spec-row">&times;</button></td>
                    </tr>`;
                tbody.insertAdjacentHTML("beforeend", row);
            }
        });
    });

    // ==========================================
    // 5. VARIANTS (Dynamic Rows)
    // ==========================================
    const addVariantBtn = document.getElementById("add-variant-btn");
    const variantsTbody = document.querySelector("#variants-table tbody");
    const noVariantsRow = document.getElementById("no-variants-row");

    if (variantsTbody) {
        variantsTbody.addEventListener("click", function (e) {
            if (e.target.closest(".remove-variant")) {
                if(confirm("Delete this variant?")) {
                    e.target.closest("tr").remove();
                    if (variantsTbody.querySelectorAll("tr").length === (noVariantsRow ? 1 : 0)) {
                        if(noVariantsRow) noVariantsRow.style.display = "table-row";
                    }
                }
            }
        });
    }

    if (addVariantBtn && variantsTbody) {
        addVariantBtn.addEventListener("click", function () {
            if (noVariantsRow) noVariantsRow.style.display = "none";
            const index = "new_" + Date.now();

            // Note: New variants get an "Initial Stock" input.
            // Existing variants in Edit Blade are disabled.
            const rowHtml = `
                <tr>
                    <td style="vertical-align: top;">
                        <input type="text" name="variants[${index}][name][en]" class="form-control form-control-sm mb-2 variant-name-input" placeholder="Name (e.g. Blue / Small)" required>
                        <input type="hidden" name="variants[${index}][name][fr]" class="variant-name-fr">
                        <input type="hidden" name="variants[${index}][name][ar]" class="variant-name-ar">
                        <div class="mt-2">
                            <label class="small text-muted">Upload Images</label>
                            <input type="file" name="variants[${index}][images][]" class="form-control form-control-sm" multiple accept="image/*">
                        </div>
                    </td>
                    <td style="vertical-align: top;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">$</span>
                            <input type="number" name="variants[${index}][price]" step="0.01" min="0" class="form-control" placeholder="0.00" required>
                        </div>
                    </td>
                    <td style="vertical-align: top;">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-variant-attributes" data-index="${index}">Edit Attributes</button>
                        <input type="hidden" name="variants[${index}][attributes][en]" value="[]">
                        <input type="hidden" name="variants[${index}][attributes][fr]" value="[]">
                        <input type="hidden" name="variants[${index}][attributes][ar]" value="[]">
                    </td>
                    <td style="vertical-align: top;">
                        <span class="badge bg-secondary">Auto-Gen</span>
                    </td>
                    <td class="text-end">
                        <button type="button" class="btn btn-danger btn-sm remove-variant"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            `;
            variantsTbody.insertAdjacentHTML("beforeend", rowHtml);

            // Sync Name logic
            const newRow = variantsTbody.lastElementChild;
            const enInput = newRow.querySelector(".variant-name-input");
            enInput.addEventListener("input", function () {
                newRow.querySelector(".variant-name-fr").value = this.value;
                newRow.querySelector(".variant-name-ar").value = this.value;
            });
        });
    }

    // ==========================================
    // 6. VARIANTS ATTRIBUTES MODAL
    // ==========================================
    const variantModal = document.getElementById("variantTranslationModal");
    const modalVariantIndex = document.getElementById("modal-variant-index");
    const modalVariantNameEn = document.getElementById("modal-variant-name-en");
    const modalVariantNameFr = document.getElementById("modal-variant-name-fr");
    const modalVariantNameAr = document.getElementById("modal-variant-name-ar");
    const saveVariantTranslationBtn = document.getElementById("save-variant-translation");

    // Edit Attributes Button
    document.body.addEventListener("click", function (e) {
        if (e.target.classList.contains("edit-variant-attributes")) {
            const index = e.target.getAttribute("data-index");
            const row = e.target.closest("tr");

            // Populate modal with current data
            modalVariantIndex.value = index;
            modalVariantNameEn.value = row.querySelector(`input[name="variants[${index}][name][en]"]`)?.value || "";
            modalVariantNameFr.value = row.querySelector(`input[name="variants[${index}][name][fr]"]`)?.value || "";
            modalVariantNameAr.value = row.querySelector(`input[name="variants[${index}][name][ar]"]`)?.value || "";

            // Populate attributes tables
            populateAttributesTable("en", row.querySelector(`input[name="variants[${index}][attributes][en]"]`)?.value || "[]");
            populateAttributesTable("fr", row.querySelector(`input[name="variants[${index}][attributes][fr]"]`)?.value || "[]");
            populateAttributesTable("ar", row.querySelector(`input[name="variants[${index}][attributes][ar]"]`)?.value || "[]");

            // Show modal
            const modal = new bootstrap.Modal(variantModal);
            modal.show();
        }
    });

    // Add Attribute Button
    document.body.addEventListener("click", function (e) {
        if (e.target.classList.contains("add-variant-attr-btn")) {
            const lang = e.target.getAttribute("data-lang");
            const tbody = document.querySelector(`#variant-attr-table-${lang} tbody`);
            const row = `<tr>
                <td><input type="text" class="form-control form-control-sm attr-key" placeholder="Key"></td>
                <td><input type="text" class="form-control form-control-sm attr-value" placeholder="Value"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-attr-row">&times;</button></td>
            </tr>`;
            tbody.insertAdjacentHTML("beforeend", row);
        }
    });

    // Remove Attribute Row
    document.body.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-attr-row")) {
            e.target.closest("tr").remove();
        }
    });

    // Save Modal
    if (saveVariantTranslationBtn) {
        saveVariantTranslationBtn.addEventListener("click", function () {
            const index = modalVariantIndex.value;
            const row = document.querySelector(`[data-index="${index}"]`)?.closest("tr");

            if (row) {
                // Update names
                const enInput = row.querySelector(`input[name="variants[${index}][name][en]"]`);
                const frInput = row.querySelector(`input[name="variants[${index}][name][fr]"]`);
                const arInput = row.querySelector(`input[name="variants[${index}][name][ar]"]`);

                if (enInput) enInput.value = modalVariantNameEn.value;
                if (frInput) frInput.value = modalVariantNameFr.value;
                if (arInput) arInput.value = modalVariantNameAr.value;

                // Update the display name in the table if English name changed
                const displayNameInput = row.querySelector('.variant-name-input');
                if (displayNameInput && modalVariantNameEn.value) {
                    displayNameInput.value = modalVariantNameEn.value;
                }

                // Update attributes
                row.querySelector(`input[name="variants[${index}][attributes][en]"]`).value = JSON.stringify(getAttributesFromTable("en"));
                row.querySelector(`input[name="variants[${index}][attributes][fr]"]`).value = JSON.stringify(getAttributesFromTable("fr"));
                row.querySelector(`input[name="variants[${index}][attributes][ar]"]`).value = JSON.stringify(getAttributesFromTable("ar"));
            }

            // Close modal
            const modal = bootstrap.Modal.getInstance(variantModal);
            modal.hide();
        });
    }

    function populateAttributesTable(lang, attributesJson) {
        const tbody = document.querySelector(`#variant-attr-table-${lang} tbody`);
        tbody.innerHTML = "";
        const attributes = JSON.parse(attributesJson);
        attributes.forEach(attr => {
            const row = `<tr>
                <td><input type="text" class="form-control form-control-sm attr-key" value="${attr.key || ""}"></td>
                <td><input type="text" class="form-control form-control-sm attr-value" value="${attr.value || ""}"></td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-attr-row">&times;</button></td>
            </tr>`;
            tbody.insertAdjacentHTML("beforeend", row);
        });
    }

    function getAttributesFromTable(lang) {
        const tbody = document.querySelector(`#variant-attr-table-${lang} tbody`);
        const attributes = [];
        tbody.querySelectorAll("tr").forEach(tr => {
            const key = tr.querySelector(".attr-key").value.trim();
            const value = tr.querySelector(".attr-value").value.trim();
            if (key && value) {
                attributes.push({ key, value });
            }
        });
        return attributes;
    }
});
