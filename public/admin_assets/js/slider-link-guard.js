(function () {
    function setupButtonLinkGuard(config) {
        if (!config) return;

        var form = document.querySelector(config.formSelector);
        var linkInput = document.querySelector(config.linkSelector);
        var textInputs = document.querySelectorAll(config.textSelector);
        if (!form || !linkInput || textInputs.length === 0) return;

        var message = config.message || 'Button link is required when button text is provided.';

        var updateLinkRequirement = function () {
            var hasText = Array.prototype.some.call(textInputs, function (input) {
                return input.value.trim() !== '';
            });
            linkInput.required = hasText;
            if (!hasText) {
                linkInput.setCustomValidity('');
            }
        };

        Array.prototype.forEach.call(textInputs, function (input) {
            input.addEventListener('input', updateLinkRequirement);
        });

        form.addEventListener('submit', function (e) {
            if (linkInput.required && linkInput.value.trim() === '') {
                linkInput.setCustomValidity(message);
                linkInput.reportValidity();
                e.preventDefault();
            } else {
                linkInput.setCustomValidity('');
            }
        });

        linkInput.addEventListener('input', function () {
            linkInput.setCustomValidity('');
        });

        updateLinkRequirement();
    }

    window.setupButtonLinkGuard = setupButtonLinkGuard;
})();
