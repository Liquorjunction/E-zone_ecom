// Mobile filter modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterButton = document.getElementById('mobile-filter-btn');
    const modal = document.getElementById('mobile-filter-modal');
    const modalContent = document.getElementById('modal-content');
    const closeButton = document.getElementById('close-modal');

    if (filterButton && modal && modalContent && closeButton) {
        // Open modal
        filterButton.addEventListener('click', function() {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('translate-y-full');
            }, 10);
            document.body.style.overflow = 'hidden';
        });

        // Close modal
        closeButton.addEventListener('click', function() {
            modalContent.classList.add('translate-y-full');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }, 300);
        });

        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modalContent.classList.add('translate-y-full');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        });
    }

    // Category subcategories toggle functionality
    const toggleButtons = document.querySelectorAll('.toggle-subcategories');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryId = this.getAttribute('data-category-id');
            const subcategoriesContainer = document.querySelector(`.subcategories-container[data-category-id="${categoryId}"]`);
            const icon = this.querySelector('svg');

            if (subcategoriesContainer && icon) {
                subcategoriesContainer.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            }
        });
    });

    // Category checkbox functionality - check/uncheck all subcategories
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const categoryId = this.getAttribute('data-category-id');
            const subcategoryCheckboxes = document.querySelectorAll(`.subcategory-checkbox[data-category-id="${categoryId}"]`);

            subcategoryCheckboxes.forEach(subCheckbox => {
                subCheckbox.checked = this.checked;
            });
        });
    });

    // Subcategory checkbox functionality - uncheck category if all subcategories unchecked
    const subcategoryCheckboxes = document.querySelectorAll('.subcategory-checkbox');
    subcategoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const categoryId = this.getAttribute('data-category-id');
            const categoryCheckbox = document.querySelector(`.category-checkbox[data-category-id="${categoryId}"]`);
            const allSubcategoryCheckboxes = document.querySelectorAll(`.subcategory-checkbox[data-category-id="${categoryId}"]`);
            const checkedSubcategories = document.querySelectorAll(`.subcategory-checkbox[data-category-id="${categoryId}"]:checked`);

            if (categoryCheckbox) {
                categoryCheckbox.checked = checkedSubcategories.length === allSubcategoryCheckboxes.length;
            }
        });
    });
});
