$(document).ready(function() {
    // SubCategory Image Preview
    // Fixed ID to match Blade: #sub-category-icon
    $('#sub-category-icon').change(function(){
        let reader = new FileReader();
        reader.onload = (e) => {
            // Fixed ID to match Blade: #sub-category-image-preview
            $('#sub-category-image-preview').attr('src', e.target.result).show();
        }
        if(this.files && this.files[0]) {
            reader.readAsDataURL(this.files[0]);
        }
    });
});
