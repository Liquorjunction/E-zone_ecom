
function initBatchOperations(config) {
    // Default configuration
    const defaults = {
        batchActionsId: 'batch-actions',
        activeBtnId: 'batch-active-btn',
        inactiveBtnId: 'batch-inactive-btn',
        deleteBtnId: 'batch-delete-btn',
        itemName: 'item'
    };

    // Merge config with defaults
    const options = { ...defaults, ...config };

    // Cache jQuery selectors
    const $table = $('#' + options.tableId);
    const $selectAllCheckbox = $('#' + options.selectAllCheckboxId);
    const $batchActions = $('#' + options.batchActionsId);
    const $activeBtn = $('#' + options.activeBtnId);
    const $inactiveBtn = $('#' + options.inactiveBtnId);
    const $deleteBtn = $('#' + options.deleteBtnId);
    const itemCheckboxSelector = '.' + options.itemCheckboxClass;

    // Attach handlers for batch operations
    function attachBatchSelectHandlers() {
        $selectAllCheckbox.off('change').on('change', function() {
            const isChecked = this.checked;
            $(itemCheckboxSelector).prop('checked', isChecked);
            updateBatchActions();
        });

        $(document).off('change', itemCheckboxSelector).on('change', itemCheckboxSelector, function() {
            updateBatchActions();
            const totalCheckboxes = $(itemCheckboxSelector).length;
            const checkedCheckboxes = $(itemCheckboxSelector + ':checked').length;
            $selectAllCheckbox.prop('checked', totalCheckboxes === checkedCheckboxes && totalCheckboxes > 0);
        });
    }

    function updateBatchActions() {
        const checkedCount = $(itemCheckboxSelector + ':checked').length;
        if (checkedCount > 0) {
            $batchActions.show();
        } else {
            $batchActions.hide();
        }
    }

    function getSelectedIds() {
        const selectedIds = [];
        $(itemCheckboxSelector + ':checked').each(function() {
            selectedIds.push($(this).val());
        });
        return selectedIds;
    }

    function resetCheckboxes() {
        $selectAllCheckbox.prop('checked', false);
        $(itemCheckboxSelector).prop('checked', false);
        $batchActions.hide();
    }

    // Batch delete handler
    $(document).off('click', '#' + options.deleteBtnId).on('click', '#' + options.deleteBtnId, function() {
        const selectedIds = getSelectedIds();

        if (selectedIds.length === 0) {
            if (window.showToast) {
                window.showToast('Please select at least one ' + options.itemName + ' to delete.', 'warning');
            }
            return;
        }

        if (!confirm('Are you sure you want to delete ' + selectedIds.length + ' selected ' + options.itemName + '(s)? This action cannot be undone.')) {
            return;
        }

        const baseUrl = $table.data('url');
        const batchDestroyUrl = baseUrl.replace('/data', '/batch-destroy');

        $.ajax({
            url: batchDestroyUrl,
            method: 'POST',
            data: {
                ids: selectedIds,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $table.DataTable().ajax.reload();

                if (window.showToast) {
                    window.showToast('Selected ' + options.itemName + '(s) deleted successfully.', 'success');
                }

                resetCheckboxes();
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while deleting ' + options.itemName + 's.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (window.showToast) {
                    window.showToast(errorMessage, 'error');
                }
            }
        });
    });

    // Batch activate handler
    $(document).off('click', '#' + options.activeBtnId).on('click', '#' + options.activeBtnId, function() {
        const selectedIds = getSelectedIds();

        if (selectedIds.length === 0) {
            if (window.showToast) {
                window.showToast('Please select at least one ' + options.itemName + ' to activate.', 'warning');
            }
            return;
        }

        batchUpdateStatus(selectedIds, true, 'activated');
    });

    // Batch deactivate handler
    $(document).off('click', '#' + options.inactiveBtnId).on('click', '#' + options.inactiveBtnId, function() {
        const selectedIds = getSelectedIds();

        if (selectedIds.length === 0) {
            if (window.showToast) {
                window.showToast('Please select at least one ' + options.itemName + ' to deactivate.', 'warning');
            }
            return;
        }

        batchUpdateStatus(selectedIds, false, 'deactivated');
    });

    // Batch update status helper
    function batchUpdateStatus(selectedIds, isActive, action) {
        const baseUrl = $table.data('url');
        const batchStatusUrl = baseUrl.replace('/data', '/batch-update-status');

        $.ajax({
            url: batchStatusUrl,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                ids: selectedIds,
                is_active: isActive ? 1 : 0,
                _token: $('meta[name="csrf-token"]').attr('content')
            }),
            success: function(response) {
                $table.DataTable().ajax.reload();

                if (window.showToast) {
                    window.showToast('Selected ' + options.itemName + '(s) ' + action + ' successfully.', 'success');
                }

                resetCheckboxes();
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating ' + options.itemName + ' status.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                if (window.showToast) {
                    window.showToast(errorMessage, 'error');
                }
            }
        });
    }

    // Initial attachment
    attachBatchSelectHandlers();

    // Return public API
    return {
        updateBatchActions: updateBatchActions,
        resetCheckboxes: resetCheckboxes,
        getSelectedIds: getSelectedIds
    };
}
