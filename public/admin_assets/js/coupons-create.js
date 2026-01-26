$(document).ready(function() {
    function lockStartDateField() {
        var $startInput = $("#starts-at-field");
        var $hiddenInput = $("#starts-at-hidden");
        if ($startInput.length && $hiddenInput.length) {
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
    $(window).on('pageshow', setMinStartDate);
});
