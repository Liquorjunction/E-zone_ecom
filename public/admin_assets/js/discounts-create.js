$(document).ready(function() {
        // Lock the start date field (readonly, styled, non-editable) for both create and edit forms
        function lockStartDateField() {
            var $startInput = $("#starts-at-field");
            var $hiddenInput = $("#starts-at-hidden");
            if ($startInput.length && $hiddenInput.length) {
                // Always sync hidden input value with visible field
                $hiddenInput.val($startInput.val());
                $startInput.on('change input', function() {
                    $hiddenInput.val($startInput.val());
                });
                $startInput.prop('readonly', true);
                $startInput.css({'background-color': '#98a3ad', 'cursor': 'not-allowed'});
                $startInput.attr('tabindex', -1);
                $startInput.on('keydown', function(e) { e.preventDefault(); });
            }
        }
        lockStartDateField();
    const data = window._discountData || {};
    let targetUrls = data.targetUrls || {};
    let rowIdx = data.rowIdx || 0;

    function getTargetTypeConfig(type) {
        var configs = {
            category: {
                url: targetUrls.category || '',
                placeholder: 'Select Category'
            },
            brand: {
                url: targetUrls.brand || '',
                placeholder: 'Select Brand'
            },
            subcategory: {
                url: targetUrls.subcategory || '',
                placeholder: 'Select Subcategory'
            },
            product: {
                url: targetUrls.product || '',
                placeholder: 'Select Product Variant'
            }
        };

        return configs[type] || null;
    }

    function makeTargetIdFieldHtml(index, type, value = '') {
        var config = getTargetTypeConfig(type);

        if (config) {
            var selectedOption = value ? `<option selected value="${value}">${value}</option>` : '<option value=""></option>';
            return `<select class="select2-ajax browser-default form-control target-id-select" data-allow-clear="true" data-option-url="${config.url}" data-placeholder="${config.placeholder}" name="targets[${index}][target_id]">${selectedOption}</select>`;
        }

        return `<input type="number" name="targets[${index}][target_id]" class="form-control" placeholder="Enter ID (e.g., 105)" value="${value}" required>`;
    }

    function getLockedTargetType() {
        let $rows = $('#target-rows tr').not('.empty-message');
        if (!$rows.length) return '';
        let $first = $rows.first().find('select[name*="[target_type]"]');
        return $first.length ? $first.val() : '';
    }

    function buildTargetTypeOptions(selectedType, lockedType) {
        let options = [
            { value: 'product', label: 'Product' },
            { value: 'category', label: 'Category' },
            { value: 'subcategory', label: 'Subcategory' },
            { value: 'brand', label: 'Brand' }
        ];
        return options.map(function(opt) {
            let selected = opt.value === selectedType ? 'selected' : '';
            let disabled = lockedType && opt.value !== lockedType ? 'disabled' : '';
            return `<option value="${opt.value}" ${selected} ${disabled}>${opt.label}</option>`;
        }).join('');
    }

    function syncAllTargetTypes(lockedType) {
        $('#target-rows tr').each(function() {
            let $tr = $(this);
            if ($tr.hasClass('empty-message')) return;
            let $typeSelect = $tr.find('select[name*="[target_type]"]');
            if (!$typeSelect.length) return;
            let name = $typeSelect.attr('name');
            let match = name.match(/targets\[(\d+)\]\[target_type\]/);
            let index = match ? match[1] : null;
            if ($typeSelect.val() !== lockedType) {
                $typeSelect.val(lockedType);
                let $secondTd = $tr.find('td').eq(1);
                $secondTd.html(makeTargetIdFieldHtml(index, lockedType, ''));
                initializeSelect2($secondTd);
            }
        });
    }

    $('#add-target-row').on('click', function() {
        $('.empty-message').remove();
        let lockedType = getLockedTargetType();
        let targetType = lockedType || 'product';

        let html = `
                <tr>
                    <td class="ps-4">
                        <select name="targets[${rowIdx}][target_type]" class="form-select target-type-select">
                            ${buildTargetTypeOptions(targetType, lockedType)}
                        </select>
                    </td>
                    <td>
                        ${makeTargetIdFieldHtml(rowIdx, targetType, '')}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-soft-danger btn-sm remove-row">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
        $('#target-rows').append(html);
        // initialize select2 on new selects
        initializeSelect2($('#target-rows').find('tr').last());
        rowIdx++;
    });

    // handle change of target type for existing and newly added rows
    $(document).on('change', '.target-type-select', function() {
        let $select = $(this);
        let $tr = $select.closest('tr');
        let name = $select.attr('name');
        let match = name.match(/targets\[(\d+)\]\[target_type\]/);
        let index = match ? match[1] : null;
        let val = $select.val();
        let lockedType = getLockedTargetType();
        if (lockedType && val !== lockedType) {
            $select.val(lockedType);
            val = lockedType;
        }
        let $secondTd = $tr.find('td').eq(1);
        $secondTd.html(makeTargetIdFieldHtml(index, val, ''));
        // initialize select2 for replaced field
        initializeSelect2($secondTd);
        if ($tr.is($('#target-rows tr').not('.empty-message').first())) {
            syncAllTargetTypes(val);
        }
        // re-validate all rows after replacing the field
        validateAllRows();
    });

    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        if ($('#target-rows tr').length === 0) {
            $('#target-rows').html(`
                    <tr class="empty-message">
                        <td colspan="4" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-inbox fa-3x mb-3 opacity-50"></i>
                                <p class="mb-0">No targets added yet</p>
                                <small>Click "Add Target" to apply this discount to specific items</small>
                            </div>
                        </td>
                    </tr>
                `);
        }
        // re-validate all rows after removal
        validateAllRows();
    });

    // delegated handler for changes to target_id inputs/selects
    $(document).on('change', '#target-rows select[name*="[target_id]"], #target-rows input[name*="[target_id]"]', function() {
        // when an id changes re-check all rows so duplicates clear correctly
        validateAllRows();
    });

    function getAllTargets() {
        let list = [];
        $('#target-rows tr').each(function() {
            let $tr = $(this);
            if ($tr.hasClass('empty-message')) return;
            let type = $tr.find('select[name*="[target_type]"]').val();
            let $idField = $tr.find('select[name*="[target_id]"], input[name*="[target_id]"]');
            let id = $idField.length ? String($idField.val()) : '';
            list.push({ type: type, id: id, $tr: $tr });
        });
        return list;
    }

    function clearRowInvalid($tr) {
        $tr.removeClass('table-danger');
        $tr.removeAttr('data-invalid');
        $tr.find('.target-error').remove();
    }

    function markRowInvalid($tr, message) {
        clearRowInvalid($tr);
        $tr.addClass('table-danger');
        $tr.attr('data-invalid', '1');
        $tr.find('td').eq(1).append(`<div class="target-error small text-danger mt-1">${message}</div>`);
    }

    function validateRow($tr) {
        clearRowInvalid($tr);
        if ($tr.hasClass('empty-message')) return true;
        let type = $tr.find('select[name*="[target_type]"]').val();
        let $idField = $tr.find('select[name*="[target_id]"], input[name*="[target_id]"]');
        if (!$idField.length) return true;
        let id = String($idField.val()).trim();

        // empty id is invalid for select-based targets
        if ((type === 'product' || type === 'category' || type === 'brand' || type === 'subcategory') && !id) {
            markRowInvalid($tr, 'Please select a target.');
            return false;
        }

        // check duplicates
        // duplicates are handled in validateAllRows for consistency

        return true;
    }

    function validateAllRows() {
        // clear all first
        $('#target-rows tr').each(function() { clearRowInvalid($(this)); });

        let map = {}; // key -> array of $tr
        $('#target-rows tr').each(function() {
            let $tr = $(this);
            if ($tr.hasClass('empty-message')) return;
            let type = $tr.find('select[name*="[target_type]"]').val();
            let $idField = $tr.find('select[name*="[target_id]"], input[name*="[target_id]"]');
            let id = $idField.length ? String($idField.val()).trim() : '';

            // mark empty selects as invalid immediately
            if ((type === 'product' || type === 'category' || type === 'brand' || type === 'subcategory') && !id) {
                markRowInvalid($tr, 'Please select a target.');
            }

            if (id !== '') {
                let key = type + '::' + id;
                if (!map[key]) map[key] = [];
                map[key].push($tr);
            }
        });

        // mark duplicates
        Object.keys(map).forEach(function(key) {
            let arr = map[key];
            if (arr.length > 1) {
                arr.forEach(function($tr) {
                    markRowInvalid($tr, 'Duplicate target.');
                });
            }
        });
    }

    // Initialize Select2 for target id selects within a context (or whole document)
    function initializeSelect2($context) {
        $context = $context && $($context).length ? $($context) : $(document);
        if (window.initSelect2Ajax) {
            window.initSelect2Ajax($context);
            return;
        }

        $context.find('.target-id-select').each(function() {
            let $el = $(this);
            if ($el.data('select2')) return;
            $el.select2({
                width: '100%',
                placeholder: $el.data('placeholder') || 'Select option',
                allowClear: true,
                dropdownParent: $el.closest('.modal, form, .card')
            });
        });
    }

    // initialize Select2 on page load for existing selects
    initializeSelect2(document);

    // Prevent back date selection for start date
    function setMinStartDate() {
        var today = new Date();
        var yyyy = today.getFullYear();
        var mm = String(today.getMonth() + 1).padStart(2, '0');
        var dd = String(today.getDate()).padStart(2, '0');
        var hh = String(today.getHours()).padStart(2, '0');
        var min = String(today.getMinutes()).padStart(2, '0');
        var minDateTime = yyyy + '-' + mm + '-' + dd + 'T' + hh + ':' + min;
        $("input[name='starts_at']").attr('min', minDateTime);
    }
    setMinStartDate();
    // Also update min on page show (for browser back/forward)
    $(window).on('pageshow', setMinStartDate);

    // form submit validation
    let $form = $('#target-rows').closest('form');
    $form.on('submit', function(e) {
        let valid = true;
        clearFormAlert();
        validateAllRows();
        // Check for at least one non-empty target row
        let hasTarget = false;
        $('#target-rows tr').each(function() {
            let $tr = $(this);
            if ($tr.hasClass('empty-message')) return;
            let type = $tr.find('select[name*="[target_type]"]').val();
            let $idField = $tr.find('select[name*="[target_id]"], input[name*="[target_id]"]');
            let id = $idField.length ? String($idField.val()).trim() : '';
            if (type && id) {
                hasTarget = true;
            }
        });
        if (!hasTarget) {
            valid = false;
            showFormAlert('At least one target is required. Please add a target before submitting.');
            // Highlight the empty-message row and show inline error
            let $emptyRow = $('#target-rows .empty-message');
            if ($emptyRow.length) {
                $emptyRow.addClass('table-danger');
                if ($emptyRow.find('.target-error').length === 0) {
                    $emptyRow.find('td').eq(0).prepend('<div class="target-error small text-danger mb-2">At least one target is required.</div>');
                }
            }
        } else {
            // Remove error highlight if targets exist
            $('#target-rows .empty-message').removeClass('table-danger').find('.target-error').remove();
        }
        // Removed JS validation for start date being in the past
        if ($('#target-rows tr[data-invalid="1"]').length) valid = false;
        if (!valid) {
            e.preventDefault();
            if (!hasTarget) {
                // Optionally, scroll to targets section
                let $targetsSection = $('#targets-table');
                if ($targetsSection.length) {
                    $('html, body').animate({ scrollTop: $targetsSection.offset().top - 100 }, 300);
                }
            }
            return false;
        }
        return true;
    });

    function showFormAlert(message) {
        let $alert = $form.find('.targets-validation-alert');
        if (!$alert.length) {
            $alert = $(`<div class="alert alert-danger targets-validation-alert">${message}</div>`);
            $form.prepend($alert);
        } else {
            $alert.text(message).show();
        }
    }

    function clearFormAlert() {
        $form.find('.targets-validation-alert').remove();
    }
});
