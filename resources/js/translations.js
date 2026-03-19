window.translations = {
    "Please select a shipping address": "{{ statictranslations('Please select a shipping address') }}",
    "reviews": "{{ statictranslations('reviews') }}",
    "No reviews yet.": "{{ statictranslations('No reviews yet.') }}",
    "No ratings yet.": "{{ statictranslations('No ratings yet.') }}",
    "Be the first to review this product!": "{{ statictranslations('Be the first to review this product!') }}",
    "OFF": "{{ statictranslations('OFF') }}",
    "No deals found.": "{{ statictranslations('No deals found.') }}",
    "Login to Review": "{{ statictranslations('Login to Review') }}"
};

function trans(key) {
    return window.translations[key] ?? key;
}
window.trans = trans;
