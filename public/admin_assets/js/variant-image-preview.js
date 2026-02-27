// variant-image-preview.js
// Handles preview, drag-sort, and removal for variant image uploads (create/edit)
$(function() {
    // Preview newly added images
    function handleVariantImageInput(input, variantIndex) {
        const previewList = $('#variant-image-preview-list-' + variantIndex);
        previewList.empty();
        const files = Array.from(input.files);
        files.forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imgDiv = $('<div class="variant-image-preview-wrapper position-relative d-inline-block me-1 mb-1" style="width: 50px; height: 50px;">')
                    .attr('data-file-idx', idx)
                    .append($('<img>').attr('src', e.target.result).addClass('rounded border').css({width:'50px',height:'50px','object-fit':'cover','cursor':'move'}));
                // Remove button
                imgDiv.append('<button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 p-1 remove-variant-image" style="font-size:0.7rem;line-height:1;border-radius:50%;"><i class="bi bi-x"></i></button>');
                previewList.append(imgDiv);
            };
            reader.readAsDataURL(file);
        });
        // Enable sortable
        previewList.sortable({
            items: '.variant-image-preview-wrapper',
            handle: 'img',
            update: function() {
                updateInputFileOrder(input, previewList);
            }
        });
    }
    // Update input files order to match preview order
    function updateInputFileOrder(input, previewList) {
        const files = Array.from(input.files);
        const order = previewList.find('.variant-image-preview-wrapper').map(function(){return $(this).data('file-idx');}).get();
        const dt = new DataTransfer();
        order.forEach(idx => dt.items.add(files[idx]));
        input.files = dt.files;
    }
    // Remove image from preview and input (unsaved images)
    $(document).on('click', '.remove-variant-image', function() {
        const wrapper = $(this).closest('.variant-image-preview-wrapper');
        const previewList = wrapper.parent();
        const input = previewList.closest('td').find('input[type="file"]')[0];
        const idxToRemove = wrapper.data('file-idx');
        let files = Array.from(input.files);
        files.splice(idxToRemove, 1);
        const dt = new DataTransfer();
        files.forEach(f => dt.items.add(f));
        input.files = dt.files;
        handleVariantImageInput(input, $(input).data('variant-index'));
    });

    // Delete saved image from DB and remove preview (edit view)
    $(document).on('click', '.delete-variant-image', function() {
        const btn = $(this);
        const wrapper = btn.closest('.variant-image-preview-wrapper');
        const imageId = btn.data('image-id');
        if (!imageId) return;
        btn.prop('disabled', true);
        $.ajax({
            url: window.variantImageDeleteUrl || '/admin/products/deleteVariantImage',
            method: 'POST',
            data: {
                image_id: imageId,
                _token: window.csrfToken
            },
            success: function(resp) {
                if (resp.success) {
                    wrapper.remove();
                } else {
                    btn.prop('disabled', false);
                    alert(resp.message || 'Failed to delete image.');
                }
            },
            error: function() {
                btn.prop('disabled', false);
                alert('Error deleting image.');
            }
        });
    });
    // On file input change
    $(document).on('change', '.variant-image-input', function() {
        handleVariantImageInput(this, $(this).data('variant-index'));
    });
    // Expose for dynamic variant rows
    window.handleVariantImageInput = handleVariantImageInput;
});
