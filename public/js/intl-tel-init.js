// Custom JS to initialize intl-tel-input for the registration phone field
// Requires intl-tel-input library to be loaded before this script

document.addEventListener('DOMContentLoaded', function () {
    var input = document.querySelector("#phone");
    if (input && window.intlTelInput) {
        window.intlTelInput(input, {
            initialCountry: "ma",
            preferredCountries: ["ma", "us", "in", "gb", "ae", "au"],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            nationalMode: false,
            separateDialCode: true
        });
    }
});
