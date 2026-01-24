$(document).ready(function() {
        // Lock the start date field (readonly, styled, non-editable) for both create and edit forms
        function lockStartDateField() {
            var $startInput = $("#starts-at-field");
            if ($startInput.length) {
                if (!$startInput.val()) {
                    var now = new Date();
                    var pad = n => n < 10 ? '0' + n : n;
                    var local = now.getFullYear() + '-' + pad(now.getMonth() + 1) + '-' + pad(now.getDate()) + 'T' + pad(now.getHours()) + ':' + pad(now.getMinutes());
                    $startInput.val(local);
                }
                $startInput.prop('readonly', true);
                $startInput.css({'background-color': '#98a3ad', 'cursor': 'not-allowed'});
                $startInput.attr('tabindex', -1);
                $startInput.on('keydown', function(e) { e.preventDefault(); });
            }
        }
        lockStartDateField();
    const data = window._discountData || {};
    let categories = data.categories || [];
    let brands = data.brands || [];
    let subcategories = data.subcategories || [];
    let productVariants = data.productVariants || [];
    let rowIdx = data.rowIdx || 0;

    function makeTargetIdFieldHtml(index, type, value = '') {
        if (type === 'category') {
            let options = '<option value="">Select Category</option>';
            categories.forEach(function(cat) {
                let selected = (value == cat.id) ? 'selected' : '';
                options += `<option value="${cat.id}" ${selected}>${cat.name}</option>`;
            });
            return `<select name="targets[${index}][target_id]" class="form-select target-id-select">${options}</select>`;
        } else if (type === 'brand') {
            let options = '<option value="">Select Brand</option>';
            brands.forEach(function(br) {
                let selected = (value == br.id) ? 'selected' : '';
                options += `<option value="${br.id}" ${selected}>${br.name}</option>`;
            });
            return `<select name="targets[${index}][target_id]" class="form-select target-id-select">${options}</select>`;
        } else if (type === 'subcategory') {
            let options = '<option value="">Select Subcategory</option>';
            subcategories.forEach(function(sc) {
                let selected = (value == sc.id) ? 'selected' : '';
                options += `<option value="${sc.id}" ${selected}>${sc.name}</option>`;
            });
            return `<select name="targets[${index}][target_id]" class="form-select target-id-select">${options}</select>`;
        } else if (type === 'product') {
            let options = '<option value="">Select Variant</option>';
            productVariants.forEach(function(p) {
                let selected = (value == p.id) ? 'selected' : '';
                options += `<option value="${p.id}" ${selected}>${p.name}</option>`;
            });
            return `<select name="targets[${index}][target_id]" class="form-select target-id-select">${options}</select>`;
        } else {
            return `<input type="number" name="targets[${index}][target_id]" class="form-control" placeholder="Enter ID (e.g., 105)" value="${value}" required>`;
        }
    }

    $('#add-target-row').on('click', function() {
        $('.empty-message').remove();
        let targetType = 'product';

        let html = `
                <tr>
                    <td class="ps-4">
                        <select name="targets[${rowIdx}][target_type]" class="form-select target-type-select">
                            <option value="product">Product</option>
                            <option value="category">Category</option>
                            <option value="subcategory">Subcategory</option>
                            <option value="brand">Brand</option>
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
        let currentVal = '';
        let $secondTd = $tr.find('td').eq(1);
        let $existing = $secondTd.find('select, input');
        if ($existing.length) {
            currentVal = $existing.val();
        }
        $secondTd.html(makeTargetIdFieldHtml(index, val, currentVal));
        // initialize select2 for replaced field
        initializeSelect2($secondTd);
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
        $context.find('.target-id-select').each(function() {
            let $el = $(this);
            if ($el.data('select2')) return; // already initialized
            $el.select2({
                width: '100%',
                placeholder: $el.find('option[value=""]').first().text() || 'Select',
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
        // Validate start date is not in the past
        var $startInput = $("input[name='starts_at']");
        if ($startInput.length && $startInput.val()) {
            var selected = new Date($startInput.val());
            var now = new Date();
            if (selected < now) {
                valid = false;
                $startInput.addClass('is-invalid');
                if ($startInput.next('.start-date-error').length === 0) {
                    $startInput.after('<div class="start-date-error invalid-feedback d-block">Start date cannot be in the past.</div>');
                }
            } else {
                $startInput.removeClass('is-invalid');
                $startInput.next('.start-date-error').remove();
            }
        }
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
