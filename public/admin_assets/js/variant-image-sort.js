// This file enables drag-and-drop sorting for variant images in the product edit form.
$(function() {
    // Enable sortable for each variant's image container
    $(document).on('mouseenter', '.d-flex.gap-2.flex-wrap', function() {
        if (!$(this).hasClass('ui-sortable')) {
            $(this).sortable({
                items: '.variant-image-wrapper',
                handle: 'img',
                update: function(event, ui) {
                    var imageOrder = [];
                    $(this).find('.variant-image-wrapper').each(function(idx, el) {
                        imageOrder.push($(el).data('image-id'));
                    });
                    var variantId = $(this).closest('tr').find('input[name^="variants"][name$="[id]"]').val();
                    if (variantId && imageOrder.length > 0) {
                        $.ajax({
                            url: window.variantImageReorderUrl,
                            type: 'POST',
                            data: {
                                variant_id: variantId,
                                order: imageOrder,
                                _token: window.csrfToken
                            },
                            success: function(response) {
                                // Optionally show a toast or notification
                            },
                            error: function(xhr) {
                                alert('Failed to reorder images.');
                            }
                        });
                    }
                }
            });
        }
    });
});
