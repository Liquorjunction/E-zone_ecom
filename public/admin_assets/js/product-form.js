document.addEventListener('DOMContentLoaded', () => {
    const select = (s, ctx = document) => ctx.querySelector(s);
    const selectAll = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));
    const isEmpty = v => v === undefined || v === null || (typeof v === 'string' && v.trim() === '');

    const ensureModalInBody = (selector) => {
        const modal = select(selector);
        if (modal && modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    };

    const quillEditors = {};
    function initQuill(id, hiddenId) {
        const container = select(`#${id}`);
        if (!container) return;
        if (container.classList.contains('ql-container')) return;

        const quill = new Quill(`#${id}`, {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ font: [] }],
                    [{ size: ['small', false, 'large', 'huge'] }],
                    [{ header: [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }],
                    ['link', 'image', 'clean']
                ]
            }
        });

        quillEditors[id] = { quill, hiddenId };
        const hidden = select(`#${hiddenId}`);
        if (hidden) hidden.__quill = quill;
        if (hidden && hidden.value) quill.root.innerHTML = hidden.value;
        if (hidden && !hidden.value && container.innerHTML.trim()) {
            hidden.value = container.innerHTML;
        }
        quill.on('text-change', () => {
            if (hidden) {
                hidden.value = quill.getText().trim().length === 0 ? '' : quill.root.innerHTML;
                hidden.dispatchEvent(new Event('input'));
            }
        });
    }

    ['en', 'fr', 'ar'].forEach(lang => initQuill(`quill-description-${lang}`, `description_${lang}`));

    ensureModalInBody('#variantTranslationModal');

    function saveAllQuillContents() {
        Object.values(quillEditors).forEach(info => {
            const hidden = select(`#${info.hiddenId}`);
            if (hidden && hidden.__quill) {
                hidden.value = hidden.__quill.getText().trim().length === 0 ? '' : hidden.__quill.root.innerHTML;
                hidden.dispatchEvent(new Event('input'));
            }
        });
    }

    selectAll('.btn-translate-all').forEach(btn => btn.addEventListener('click', (e) => {
        e.preventDefault();
        const langs = ['fr', 'ar'];
        const copyIfEmpty = (selector, value) => {
            if (!value) return;
            langs.forEach(lang => {
                const el = select(`${selector.replace('{lang}', lang)}`);
                if (el && isEmpty(el.value)) el.value = value;
            });
        };

        const enName = select('input[name="name[en]"]')?.value || '';
        copyIfEmpty('input[name="name[{lang}]"]', enName);

        const descEn = select('#description_en')?.value || '';
        copyIfEmpty('input[name="description[{lang}]"]', descEn);

        const dimEn = select('input[name="dimensions[en]"]')?.value || '';
        copyIfEmpty('input[name="dimensions[{lang}]"]', dimEn);
        const warEn = select('input[name="warranty[en]"]')?.value || '';
        copyIfEmpty('input[name="warranty[{lang}]"]', warEn);

        ['en'].forEach(srcLang => {
            langs.forEach(tLang => {
                const srcRows = selectAll(`#spec-table-${srcLang} tbody tr`);
                const tgtBody = select(`#spec-table-${tLang} tbody`);
                if (!tgtBody) return;
                if (tgtBody.children.length <= 0) {
                    srcRows.forEach(r => {
                        const title = r.querySelector('input')?.value || '';
                        const value = r.querySelectorAll('input')[1]?.value || '';
                        if (title || value) {
                            tgtBody.insertAdjacentHTML('beforeend', `<tr><td><input type="text" name="specifications[${tLang}][${Date.now()}][title]" class="form-control form-control-sm" value="${title}"></td><td><input type="text" name="specifications[${tLang}][${Date.now()}][value]" class="form-control form-control-sm" value="${value}"></td><td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-spec-row">&times;</button></td></tr>`);
                        }
                    });
                }
            });
        });

        const variantRows = selectAll('#variants-table tbody tr');
        variantRows.forEach(row => {
            if (row.id === 'no-variants-row') return;
            const enInput = row.querySelector('input[name*="[name][en]"]');
            if (!enInput) return;
            const nameAttr = enInput.getAttribute('name') || '';
            const idxMatch = nameAttr.match(/^variants\[(.+?)\]/);
            const idx = idxMatch ? idxMatch[1] : null;
            if (!idx) return;

            langs.forEach(lang => {
                const targetName = row.querySelector(`input[name="variants[${idx}][name][${lang}]"]`);
                if (targetName && isEmpty(targetName.value)) targetName.value = enInput.value;

                const aEn = row.querySelector(`input[name="variants[${idx}][attributes][en]"]`);
                const aTarget = row.querySelector(`input[name="variants[${idx}][attributes][${lang}]"]`);
                if (aEn && aTarget && (isEmpty(aTarget.value) || aTarget.value === '[]')) {
                    aTarget.value = aEn.value || '[]';
                }
            });
        });

        const original = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check me-2"></i> Done';
        setTimeout(() => btn.innerHTML = original, 1500);
    }));

    function activatePane(targetId) {
        const allNavLinks = selectAll('[data-bs-toggle="pill"], [data-bs-toggle="tab"]');
        const targetLink = allNavLinks.find(link => {
            const href = (link.getAttribute('href') || '').split('#')[1];
            const dataTarget = (link.getAttribute('data-bs-target') || '').replace('#', '');
            return (href || dataTarget) === targetId;
        });

        if (targetLink) {
            targetLink.classList.remove('disabled');
            try {
                new bootstrap.Tab(targetLink).show();
            } catch (e) {
                const pane = document.getElementById(targetId);
                if (pane) {
                    const container = pane.closest('.tab-content');
                    if (container) container.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show', 'active'));
                    pane.classList.add('show', 'active');
                }
            }
        } else {
            const pane = document.getElementById(targetId);
            if (!pane) return;
            const container = pane.closest('.tab-content');
            if (container) container.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('show', 'active'));
            pane.classList.add('show', 'active');
        }

        allNavLinks.forEach(link => {
            const href = (link.getAttribute('href') || '').split('#')[1];
            const dataTarget = (link.getAttribute('data-bs-target') || '').replace('#', '');
            const linkTarget = href || dataTarget;
            if (linkTarget === targetId) {
                link.classList.remove('disabled');
                link.classList.add('active');
                link.setAttribute('aria-selected', 'true');
            } else {
                link.classList.remove('active');
                link.setAttribute('aria-selected', 'false');
            }
        });

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateAndGo(nextId, currentPane, doActivate = true) {
        saveAllQuillContents();
        let valid = true;
        let firstInvalid = null;

        selectAll('[required]', currentPane).forEach(input => {
            const val = (input.type === 'file') ? (input.files && input.files.length > 0) : input.value;
            if (isEmpty(val)) {
                valid = false;
                input.classList.add('is-invalid');
                if (!firstInvalid) firstInvalid = input;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (currentPane && currentPane.id === 'step1') {
            const descEn = select('#description_en');
            if (!descEn || isEmpty(descEn.value)) {
                valid = false;
                const qu = select('#quill-description-en');
                if (qu) qu.classList.add('is-invalid');
                if (!firstInvalid) firstInvalid = qu || descEn;
            } else {
                const qu = select('#quill-description-en');
                if (qu) qu.classList.remove('is-invalid');
            }

            const coverInput = currentPane.querySelector('input[name="cover_image"]');
            const hasExisting = !!currentPane.querySelector('.current-cover-image, [data-has-cover-image="1"]');
            if (coverInput) {
                const hasFile = coverInput.files && coverInput.files.length > 0;
                if (!hasFile && !hasExisting) {
                    valid = false;
                    coverInput.classList.add('is-invalid');
                    if (!firstInvalid) firstInvalid = coverInput;
                } else {
                    coverInput.classList.remove('is-invalid');
                }
            }
        }

        if (currentPane && currentPane.id === 'step3') {
            const variantsTable = select('#variants-table');
            const rows = variantsTable ? Array.from(variantsTable.querySelectorAll('tbody tr')).filter(tr => !tr.id || tr.id !== 'no-variants-row') : [];
            if (rows.length === 0) {
                alert('Please add at least one product variant.');
                return false;
            }
            rows.forEach(row => {
                const name = row.querySelector('input[name*="[name][en]"]');
                const price = row.querySelector('input[name*="[price]"]');
                if (name && isEmpty(name.value)) { valid = false; name.classList.add('is-invalid'); if (!firstInvalid) firstInvalid = name; }
                if (price && isEmpty(price.value)) { valid = false; price.classList.add('is-invalid'); if (!firstInvalid) firstInvalid = price; }
            });
        }

        if (!valid) {
            if (firstInvalid) {
                try { firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' }); firstInvalid.focus(); } catch (e) {}
            }
            return false;
        }

        if (nextId && doActivate) activatePane(nextId);
        return true;
    }

    selectAll('.next-step').forEach(btn => btn.addEventListener('click', e => {
        e.preventDefault();
        const pane = btn.closest('.tab-pane');
        const nextId = btn.getAttribute('data-next');
        validateAndGo(nextId, pane);
    }));
    selectAll('.prev-step').forEach(btn => btn.addEventListener('click', e => { e.preventDefault(); activatePane(btn.getAttribute('data-prev')); }));

    selectAll('[data-bs-toggle="pill"], [data-bs-toggle="tab"]').forEach(link => {
        link.addEventListener('click', function (e) {
            const rawTarget = (link.getAttribute('data-bs-target') || link.getAttribute('href') || '');
            const targetId = rawTarget.replace('#', '');
            const activePane = document.querySelector('.tab-pane.show.active');
            if (activePane && activePane.id && activePane.id !== targetId) {
                const ok = validateAndGo(targetId, activePane, false);
                if (!ok) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
            }
        }, true);
    });

    selectAll('[data-bs-toggle="pill"], [data-bs-toggle="tab"]').forEach(link => {
        link.addEventListener('show.bs.tab', function (e) {
            const rawTarget = (link.getAttribute('data-bs-target') || link.getAttribute('href') || '');
            const targetId = rawTarget.replace('#', '');
            const activePane = document.querySelector('.tab-pane.show.active');
            if (activePane && activePane.id && activePane.id !== targetId) {
                const ok = validateAndGo(targetId, activePane, false);
                if (!ok) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    return false;
                }
            }
        });
    });

    if (typeof $ !== 'undefined' && $.fn && $.fn.select2) {
        $('#brand-select, #category-select, #subcategory-select').each(function () {
            const $el = $(this);
            const placeholder = $el.data('placeholder') || $el.attr('placeholder') || $el.find('option[value=""]').text() || '';
            const options = { width: '100%', allowClear: true };
            if (placeholder) options.placeholder = placeholder;
            $el.select2(options);
        });

        const cfg = { allowClear: true, width: '100%' };
        const allOpts = (window.__ALL_SUBCATEGORIES && Array.isArray(window.__ALL_SUBCATEGORIES))
            ? window.__ALL_SUBCATEGORIES
            : (function(){ const tmp = []; $('#subcategory-select option').each(function () { tmp.push({ id: $(this).val(), text: $(this).text(), categoryId: $(this).attr('data-category') }); }); return tmp; })();
        const $sub = $('#subcategory-select');

        function setSubDisabled(disabled) {
            $sub.prop('disabled', !!disabled);
            try { $sub.trigger('change.select2'); } catch (e) { $sub.trigger('change'); }
        }

        function rebuildSub() {
            const selCat = $('#category-select').val();
            const curVal = $sub.val();

            const fragment = document.createDocumentFragment();
            const placeholderOpt = new Option('Select Sub Category', '');
            fragment.appendChild(placeholderOpt);
            allOpts.forEach(o => {
                if (selCat && o.categoryId && (String(o.categoryId) === String(selCat))) {
                    const opt = new Option(o.text, o.id);
                    opt.setAttribute('data-category', o.categoryId);
                    fragment.appendChild(opt);
                }
            });

            const hadSelect2 = !!($sub.data('select2'));
            if (hadSelect2) {
                try { $sub.select2('destroy'); } catch (e) { }
            }

            $sub.empty();
            $sub[0].appendChild(fragment);

            if (curVal && $sub.find(`option[value="${curVal}"]`).length) $sub.val(curVal);
            else $sub.val('');

            if (hadSelect2 && $.fn && $.fn.select2) {
                $sub.select2({ ...cfg, placeholder: $sub.data('placeholder') || 'Select Sub Category' });
            }

            setSubDisabled(!selCat);
        }

        setSubDisabled(!$('#category-select').val());
        $('#category-select').on('change', function () {
            const has = !!$(this).val();
            setSubDisabled(!has);
            rebuildSub();
        });
        setTimeout(rebuildSub, 200);
    } else {
        const categorySelect = select('#category-select');
        const subcategorySelect = select('#subcategory-select');

        if (categorySelect && subcategorySelect) {
            const allSubOptions = Array.from(subcategorySelect.options).map(opt => ({
                value: opt.value,
                text: opt.text,
                categoryId: opt.getAttribute('data-category')
            }));

            function rebuildSub() {
                const selCat = categorySelect.value;
                subcategorySelect.innerHTML = '<option value="">Select Sub Category</option>';
                allSubOptions.forEach(opt => {
                    if (selCat && opt.categoryId && String(opt.categoryId) === String(selCat)) {
                        const newOpt = new Option(opt.text, opt.value);
                        if (opt.categoryId) newOpt.setAttribute('data-category', opt.categoryId);
                        subcategorySelect.appendChild(newOpt);
                    }
                });
                subcategorySelect.disabled = !categorySelect.value;
            }

            categorySelect.addEventListener('change', () => { rebuildSub(); });
            setTimeout(rebuildSub, 200);
        }
    }

    document.body.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-spec-row')) e.target.closest('tr')?.remove();
        if (e.target.classList.contains('add-spec-btn')) {
            const lang = e.target.getAttribute('data-lang');
            const idx = Date.now();
            const tbody = select(`#spec-table-${lang} tbody`);
            if (!tbody) return;
            tbody.insertAdjacentHTML('beforeend', `<tr><td><input type="text" name="specifications[${lang}][${idx}][title]" class="form-control form-control-sm"></td><td><input type="text" name="specifications[${lang}][${idx}][value]" class="form-control form-control-sm"></td><td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-spec-row">&times;</button></td></tr>`);
        }
    });

    const variantsTbody = select('#variants-table tbody');
    const noVariantsRow = select('#no-variants-row');

    if (variantsTbody) {
        variantsTbody.addEventListener('click', (e) => {
            const rem = e.target.closest('.remove-variant');
            if (!rem) return;
            if (!confirm('Delete this variant?')) return;
            const tr = rem.closest('tr'); tr?.remove();
            if (variantsTbody.querySelectorAll('tr').length === (noVariantsRow ? 1 : 0)) { if (noVariantsRow) noVariantsRow.style.display = 'table-row'; }
        });
    }

    const addVariantBtn = select('#add-variant-btn');
    function makeVariantRow(index) {
        return `
            <tr class="align-middle">
                <td>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold mb-1">Variant Name</label>
                        <input type="text" name="variants[${index}][name][en]" class="form-control form-control-sm variant-name-input" placeholder="e.g. Red / Large" required>
                        <input type="hidden" name="variants[${index}][name][fr]" value="" />
                        <input type="hidden" name="variants[${index}][name][ar]" value="" />
                    </div>
                    <label class="form-label small fw-semibold mb-1">Images</label>
                    <input type="file" name="variants[${index}][images][]" class="form-control form-control-sm" multiple accept="image/*">
                </td>
                <td>
                    <label class="form-label small fw-semibold mb-1">Price</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text">${window.CURRENCY_SYMBOL}</span>
                        <input type="number" name="variants[${index}][price]" step="0.01" min="0" class="form-control" required>
                    </div>
                </td>
                <td class="text-center">
                    <div class="form-check form-switch justify-content-center d-flex">
                        <input class="form-check-input" type="checkbox" name="variants[${index}][is_bestseller]" value="1" id="variant-bestseller-${index}">
                    </div>
                </td>
                <td class="text-center">
                    <div class="form-check form-switch justify-content-center d-flex">
                        <input class="form-check-input" type="checkbox" name="variants[${index}][status]" value="active" id="variant-status-${index}" checked>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-primary edit-variant-attributes" data-index="${index}"><i class="bi bi-pencil"></i> Edit</button>
                    <input type="hidden" name="variants[${index}][attributes][en]" value="[]">
                    <input type="hidden" name="variants[${index}][attributes][fr]" value="[]">
                    <input type="hidden" name="variants[${index}][attributes][ar]" value="[]">
                </td>
                <td>
                    <span class="badge bg-light text-dark border">Auto-Gen</span>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm remove-variant"><i class="bi bi-trash"></i></button>
                </td>
            </tr>`;
    }

    if (addVariantBtn) addVariantBtn.addEventListener('click', () => {
        if (noVariantsRow) noVariantsRow.style.display = 'none';
        const idx = 'new_' + Date.now();
        variantsTbody.insertAdjacentHTML('beforeend', makeVariantRow(idx));
        const newRow = variantsTbody.lastElementChild;
        const en = newRow.querySelector('.variant-name-input');
        en.addEventListener('input', function () {
            const fr = newRow.querySelector('input[name*="[name][fr]"]');
            const ar = newRow.querySelector('input[name*="[name][ar]"]');
            if (fr && isEmpty(fr.value)) fr.value = this.value;
            if (ar && isEmpty(ar.value)) ar.value = this.value;
        });
    });

    document.body.addEventListener('input', (e) => {
        if (e.target && e.target.classList && e.target.classList.contains('variant-name-input')) {
            const row = e.target.closest('tr');
            if (!row) return;
            const fr = row.querySelector('input[name*="[name][fr]"]');
            const ar = row.querySelector('input[name*="[name][ar]"]');
            if (fr && isEmpty(fr.value)) fr.value = e.target.value;
            if (ar && isEmpty(ar.value)) ar.value = e.target.value;
        }
    });

    const variantModal = select('#variantTranslationModal');
    const modalIndex = select('#modal-variant-index');
    const modalNameEn = select('#modal-variant-name-en');
    const modalNameFr = select('#modal-variant-name-fr');
    const modalNameAr = select('#modal-variant-name-ar');

    function parseAttributesValue(val) {
        if (isEmpty(val)) return [];
        try {
            const parsed = JSON.parse(val);
            if (Array.isArray(parsed)) return parsed.map(it => ({ key: it.key ?? it[0] ?? '', value: it.value ?? it[1] ?? '' }));
            if (typeof parsed === 'object') return Object.keys(parsed).map(k => ({ key: k, value: parsed[k] }));
        } catch (e) { return []; }
        return [];
    }

    function populateAttributesTable(lang, jsonVal) {
        const tbody = select(`#variant-attr-table-${lang} tbody`);
        if (!tbody) return;
        tbody.innerHTML = '';
        const attrs = parseAttributesValue(jsonVal || '[]');
        attrs.forEach(a => tbody.insertAdjacentHTML('beforeend', `<tr><td><input class="form-control form-control-sm attr-key" value="${a.key || ''}"></td><td><input class="form-control form-control-sm attr-value" value="${a.value || ''}"></td><td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-attr-row">&times;</button></td></tr>`));
    }

    function getAttributesFromTable(lang) {
        const tbody = select(`#variant-attr-table-${lang} tbody`);
        if (!tbody) return [];
        const out = [];
        tbody.querySelectorAll('tr').forEach(tr => {
            const k = tr.querySelector('.attr-key')?.value.trim();
            const v = tr.querySelector('.attr-value')?.value.trim();
            if (!isEmpty(k) && !isEmpty(v)) out.push({ key: k, value: v });
        });
        return out;
    }

    document.body.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.edit-variant-attributes');
        if (editBtn) {
            const idx = editBtn.getAttribute('data-index');
            modalIndex.value = idx;
            const row = editBtn.closest('tr');
            modalNameEn.value = row.querySelector(`input[name="variants[${idx}][name][en]"]`)?.value || '';
            modalNameFr.value = row.querySelector(`input[name="variants[${idx}][name][fr]"]`)?.value || '';
            modalNameAr.value = row.querySelector(`input[name="variants[${idx}][name][ar]"]`)?.value || '';
            populateAttributesTable('en', row.querySelector(`input[name="variants[${idx}][attributes][en]"]`)?.value || '[]');
            populateAttributesTable('fr', row.querySelector(`input[name="variants[${idx}][attributes][fr]"]`)?.value || '[]');
            populateAttributesTable('ar', row.querySelector(`input[name="variants[${idx}][attributes][ar]"]`)?.value || '[]');
            new bootstrap.Modal(variantModal).show();
        }

        if (e.target.classList.contains('add-variant-attr-btn')) {
            const lang = e.target.getAttribute('data-lang');
            const tbody = select(`#variant-attr-table-${lang} tbody`);
            tbody.insertAdjacentHTML('beforeend', `<tr><td><input class="form-control form-control-sm attr-key"></td><td><input class="form-control form-control-sm attr-value"></td><td class="text-center"><button type="button" class="btn btn-danger btn-sm remove-attr-row">&times;</button></td></tr>`);
        }

        if (e.target.classList.contains('remove-attr-row')) e.target.closest('tr')?.remove();
    });

    const saveModalBtn = select('#save-variant-translation');
    if (saveModalBtn) saveModalBtn.addEventListener('click', () => {
        const idx = modalIndex.value;
        let targetRow = null;

        if (idx.startsWith('new_')) {
            targetRow = select(`button[data-index="${idx}"]`)?.closest('tr');
        } else {
            targetRow = [...selectAll('#variants-table tbody tr')].find(r => r.querySelector(`input[name="variants[${idx}][name][en]"]`));
        }

        if (targetRow) {
            const en = targetRow.querySelector(`input[name="variants[${idx}][name][en]"]`);
            const fr = targetRow.querySelector(`input[name="variants[${idx}][name][fr]"]`);
            const ar = targetRow.querySelector(`input[name="variants[${idx}][name][ar]"]`);
            if (en) en.value = modalNameEn.value;
            if (fr) fr.value = modalNameFr.value;
            if (ar) ar.value = modalNameAr.value;

            const display = targetRow.querySelector('.variant-name-input');
            if (display) display.value = modalNameEn.value || modalNameFr.value || modalNameAr.value || '';

            const aen = targetRow.querySelector(`input[name="variants[${idx}][attributes][en]"]`);
            const afr = targetRow.querySelector(`input[name="variants[${idx}][attributes][fr]"]`);
            const aar = targetRow.querySelector(`input[name="variants[${idx}][attributes][ar]"]`);
            if (aen) aen.value = JSON.stringify(getAttributesFromTable('en'));
            if (afr) afr.value = JSON.stringify(getAttributesFromTable('fr'));
            if (aar) aar.value = JSON.stringify(getAttributesFromTable('ar'));
        } else {
            console.warn('Could not find target row for variant index:', idx);
        }
        bootstrap.Modal.getInstance(variantModal).hide();
    });

    const form = select('#product-form'); if (form) form.addEventListener('submit', () => saveAllQuillContents());
});
