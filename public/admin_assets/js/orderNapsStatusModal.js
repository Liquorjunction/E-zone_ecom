$(document).on('click', '.naps-status-action', function () {
    var url = $(this).data('naps-url');
    var $btn = $(this);

    $btn.prop('disabled', true);

    $.ajax({
        url: url,
        type: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function (response) {
            if (response.success === false) {
                alert(response.message || 'Could not retrieve NAPS status.');
                return;
            }

            $('#naps-modal-container').html(response.modal_html);

            var el = document.getElementById('napsModal');
            if (el && el.parentElement !== document.body) {
                document.body.appendChild(el);
            }

            var modal = new bootstrap.Modal(el);
            modal.show();
        },
        error: function (xhr) {
            var msg = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to fetch NAPS status.';
            alert(msg);
        },
        complete: function () {
            $btn.prop('disabled', false);
        }
    });
});
