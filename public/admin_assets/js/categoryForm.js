$(document).ready(function() {
    // Category Image Preview
    // Fixed ID to match Blade: #category-icon
    $('#category-icon').change(function(){
        let reader = new FileReader();
        reader.onload = (e) => {
            // Ensure your Blade file has an img tag with id="category-image-preview"
            $('#category-image-preview').attr('src', e.target.result).show();
        }
        if(this.files && this.files[0]) {
            reader.readAsDataURL(this.files[0]);
        }
    });
});
