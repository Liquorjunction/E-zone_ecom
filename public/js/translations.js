window.translations = {
    "Please select a shipping address": "{{ statictranslations('Please select a shipping address') }}",
    "reviews": "{{ statictranslations('reviews') }}",
    "No reviews yet.": "{{ statictranslations('No reviews yet.') }}",
    "No ratings yet.": "{{ statictranslations('No ratings yet.') }}",
    "Be the first to review this product!": "{{ statictranslations('Be the first to review this product!') }}",
    "OFF": "{{ statictranslations('OFF') }}",
    "No deals found.": "{{ statictranslations('No deals found.') }}",
    "Login to Review": "{{ statictranslations('Login to Review') }}",
    "You have a coupon applied. Leave without completing your purchase?": "{{ statictranslations('You have a coupon applied. Leave without completing your purchase?') }}"
};

console.log('Loaded translations:', window.translations);

//Debug to test the statictranslations function
console.log('Testing statictranslations function:');
console.log('Original: "Please select a shipping address"');
console.log('Translated:', statictranslations('Please select a shipping address'));
