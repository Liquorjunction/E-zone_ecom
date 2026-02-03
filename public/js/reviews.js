// Review Modal Management
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('review-modal');
    const mobileForm = document.getElementById('mobile-review-form');
    if(modal) document.body.appendChild(modal);
    if(mobileForm) document.body.appendChild(mobileForm);
});

// Helper to get CSRF token
function getCsrfToken(){
  const t = document.querySelector('input[name="_token"]');
  return t ? t.value : '';
}

// Open Write Review Modal
function openReviewModal(data = {}){
  if(window.innerWidth < 640) {
    openMobileReviewForm(data);
  } else {
    openDesktopReviewForm(data);
  }
}

// Desktop Review Form
function openDesktopReviewForm(data){
  const isEdit = data.id ? true : false;

  document.getElementById('review-modal-title').textContent = isEdit ? 'Edit Review' : 'Write a Review';
  document.getElementById('review-form').action = '/reviews';
  document.getElementById('review-form-method').value = isEdit ? 'PUT' : 'POST';
  document.getElementById('review-id').value = data.id || '';

  // Set product info
  document.getElementById('review-product-id').value = data.product_id || '';
  document.getElementById('review-product-variant-id').value = data.product_variant_id !== null && data.product_variant_id !== undefined ? data.product_variant_id : '';
  document.getElementById('review-order-id').value = data.order_id || '';
  document.getElementById('review-order-item-id').value = data.order_item_id || '';

  // Display product info if available
  if(data.product_name){
    document.getElementById('review-product-info').classList.remove('hidden');
    document.getElementById('review-product-name').textContent = data.product_name;
    document.getElementById('review-product-variant').textContent = data.variant_name || '';
    if(data.product_image){
      document.getElementById('review-product-image').src = data.product_image;
    }
  } else {
    document.getElementById('review-product-info').classList.add('hidden');
  }

  // Set form values
  document.getElementById('review-title').value = data.title || '';
  document.getElementById('review-text').value = data.review_text || '';
  document.getElementById('review-name').value = data.reviewer_name || '';
  document.getElementById('review-email').value = data.reviewer_email || '';

  // Set rating
  if(data.rating){
    setRating(data.rating);
  } else {
    resetRating();
  }

  // Clear image preview
  const previewContainer = document.getElementById('image-preview-container');
  previewContainer.innerHTML = '';
  previewContainer.classList.add('hidden');
  document.getElementById('review-images').value = '';

  const modal = document.getElementById('review-modal');
  const content = document.getElementById('review-modal-content');

  modal.classList.remove('hidden');
  modal.classList.add('flex');

  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      content.classList.remove('scale-95', 'opacity-0');
      content.classList.add('scale-100', 'opacity-100');
    });
  });
}

// Mobile Review Form
function openMobileReviewForm(data){
  const isEdit = data.id ? true : false;

  document.getElementById('mobile-review-title').textContent = isEdit ? 'Edit Review' : 'Write a Review';
  document.getElementById('mobile-review-form-element').action = '/reviews';
  document.getElementById('mobile-review-form-method').value = isEdit ? 'PUT' : 'POST';
  document.getElementById('mobile-review-id').value = data.id || '';

  document.getElementById('mobile-review-product-id').value = data.product_id || '';
  document.getElementById('mobile-review-product-variant-id').value = data.product_variant_id !== null && data.product_variant_id !== undefined ? data.product_variant_id : '';
  document.getElementById('mobile-review-order-id').value = data.order_id || '';
  document.getElementById('mobile-review-order-item-id').value = data.order_item_id || '';

  if(data.product_name){
    document.getElementById('mobile-review-product-info').classList.remove('hidden');
    document.getElementById('mobile-review-product-name').textContent = data.product_name;
    document.getElementById('mobile-review-product-variant').textContent = data.variant_name || '';
    if(data.product_image){
      document.getElementById('mobile-review-product-image').src = data.product_image;
    }
  } else {
    document.getElementById('mobile-review-product-info').classList.add('hidden');
  }

  document.getElementById('mobile-review-title').value = data.title || '';
  document.getElementById('mobile-review-text').value = data.review_text || '';
  document.getElementById('mobile-review-name').value = data.reviewer_name || '';
  document.getElementById('mobile-review-email').value = data.reviewer_email || '';

  if(data.rating){
    setMobileRating(data.rating);
  } else {
    resetMobileRating();
  }

  const previewContainer = document.getElementById('mobile-image-preview-container');
  previewContainer.innerHTML = '';
  previewContainer.classList.add('hidden');
  document.getElementById('mobile-review-images').value = '';

  const form = document.getElementById('mobile-review-form');
  form.classList.remove('translate-y-full');
  document.body.style.overflow = 'hidden';
}

// Close Modal Functions
function closeReviewModal(){
  const content = document.getElementById('review-modal-content');
  content.classList.add('scale-95', 'opacity-0');
  content.classList.remove('scale-100', 'opacity-100');
  setTimeout(() => {
    document.getElementById('review-modal').classList.add('hidden');
    document.getElementById('review-modal').classList.remove('flex');
  }, 300);
}

function closeMobileReviewForm(){
  const form = document.getElementById('mobile-review-form');
  form.classList.add('translate-y-full');
  document.body.style.overflow = '';
}

function submitMobileReviewForm() {
  document.getElementById('mobile-review-form-element').dispatchEvent(new Event('submit', { cancelable: true }));
}

// Rating Functions
function setRating(rating){
  document.getElementById('review-rating').value = rating;
  const stars = document.querySelectorAll('#star-rating .star-btn');
  const ratingText = document.getElementById('rating-text');

  stars.forEach((star, index) => {
    if(index < rating){
      star.classList.remove('text-gray-300');
      star.classList.add('text-yellow-400');
    } else {
      star.classList.remove('text-yellow-400');
      star.classList.add('text-gray-300');
    }
  });

  const labels = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
  ratingText.textContent = labels[rating - 1] || '';
}

function resetRating(){
  document.getElementById('review-rating').value = '';
  const stars = document.querySelectorAll('#star-rating .star-btn');
  stars.forEach(star => {
    star.classList.remove('text-yellow-400');
    star.classList.add('text-gray-300');
  });
  document.getElementById('rating-text').textContent = '';
}

function setMobileRating(rating){
  document.getElementById('mobile-review-rating').value = rating;
  const stars = document.querySelectorAll('#mobile-star-rating .mobile-star-btn');

  stars.forEach((star, index) => {
    if(index < rating){
      star.classList.remove('text-gray-300');
      star.classList.add('text-yellow-400');
    } else {
      star.classList.remove('text-yellow-400');
      star.classList.add('text-gray-300');
    }
  });
}

function resetMobileRating(){
  document.getElementById('mobile-review-rating').value = '';
  const stars = document.querySelectorAll('#mobile-star-rating .mobile-star-btn');
  stars.forEach(star => {
    star.classList.remove('text-yellow-400');
    star.classList.add('text-gray-300');
  });
}

// Image Preview Functions
function handleImagePreview(input){
  const container = document.getElementById('image-preview-container');
  container.innerHTML = '';

  if(input.files && input.files.length > 0){
    container.classList.remove('hidden');

    Array.from(input.files).forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = function(e){
        const div = document.createElement('div');
        div.className = 'relative group';
        div.innerHTML = `
          <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border-2 border-gray-200">
          <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        `;
        container.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
  } else {
    container.classList.add('hidden');
  }
}

function handleMobileImagePreview(input){
  const container = document.getElementById('mobile-image-preview-container');
  container.innerHTML = '';

  if(input.files && input.files.length > 0){
    container.classList.remove('hidden');

    Array.from(input.files).forEach((file, index) => {
      const reader = new FileReader();
      reader.onload = function(e){
        const div = document.createElement('div');
        div.className = 'relative group';
        div.innerHTML = `
          <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border-2 border-gray-200">
          <button type="button" onclick="removeMobileImage(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        `;
        container.appendChild(div);
      };
      reader.readAsDataURL(file);
    });
  } else {
    container.classList.add('hidden');
  }
}

function removeImage(index){
  const input = document.getElementById('review-images');
  const dt = new DataTransfer();

  Array.from(input.files).forEach((file, i) => {
    if(i !== index) dt.items.add(file);
  });

  input.files = dt.files;
  handleImagePreview(input);
}

function removeMobileImage(index){
  const input = document.getElementById('mobile-review-images');
  const dt = new DataTransfer();

  Array.from(input.files).forEach((file, i) => {
    if(i !== index) dt.items.add(file);
  });

  input.files = dt.files;
  handleMobileImagePreview(input);
}

// Form Submit Handler
async function handleReviewFormSubmit(e) {
  e.preventDefault();
  const form = e.target;
  const isMobile = form.id === 'mobile-review-form-element';
  const url = form.action;
  const method = (form.querySelector('input[name="_method"]').value || 'POST').toUpperCase();
  const token = getCsrfToken();
  const fd = new FormData(form);
  const submitBtn = isMobile ? document.getElementById('mobile-review-submit-btn') : document.getElementById('review-submit-btn');
  const originalBtnHtml = submitBtn.innerHTML;

  // Validate rating
  const rating = isMobile ? document.getElementById('mobile-review-rating').value : document.getElementById('review-rating').value;
  if(!rating){
    showUniversalPopup('Please select a rating before submitting your review', 'error', 3000);
    return;
  }

  submitBtn.disabled = true;
  submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Submitting...';

  try{
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json'
      },
      body: fd
    });

    const json = await res.json();

    if(!res.ok){
      const errorMsg = json.message || 'Failed to submit review';
      const errors = json.errors;
      let errorText = errorMsg;

      if(errors && typeof errors === 'object'){
        errorText += '\n\n' + Object.values(errors).flat().join('\n');
      }

      showUniversalPopup(errorText, 'error', 5000);
      submitBtn.disabled = false;
      submitBtn.innerHTML = originalBtnHtml;
      return;
    }

    // Success
    showUniversalPopup(json.message || 'Review submitted successfully!', 'success', 3000);

    if(isMobile){
      closeMobileReviewForm();
    } else {
      closeReviewModal();
    }

    // Reload page or update UI
    if(typeof updateReviewsList === 'function'){
      updateReviewsList();
    } else {
      window.location.reload();
    }

  }catch(e){
    console.error(e);
    showUniversalPopup('Failed to submit review. Please try again.', 'error', 3000);
    submitBtn.disabled = false;
    submitBtn.innerHTML = originalBtnHtml;
  }
}

// Delete Review
async function deleteReview(id){
  if(!confirm('Are you sure you want to delete this review?')) return;

  const token = getCsrfToken();

  try{
    const res = await fetch('/reviews', {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': token,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ id: id })
    });

    const json = await res.json();

    if(!res.ok){
      showUniversalPopup(json.message || 'Failed to delete review', 'error', 3000);
      return;
    }

    showUniversalPopup(json.message || 'Review deleted successfully', 'success', 3000);

    // Remove review card from UI
    const reviewCard = document.getElementById('review-card-' + id);
    if(reviewCard){
      reviewCard.remove();
    }

    // Reload page or update UI
    if(typeof updateReviewsList === 'function'){
      updateReviewsList();
    } else {
      window.location.reload();
    }

  }catch(e){
    console.error(e);
    showUniversalPopup('Failed to delete review. Please try again.', 'error', 3000);
  }
}

// Open Edit Review Modal from Element
function openEditReviewFromElem(elem){
  const card = elem.closest('.review-card');
  if(!card) return;

  let data = {};
  try{
    data = JSON.parse(card.dataset.review || '{}');
  }catch(e){
    console.error('Failed to parse review data', e);
  }

  if(!data.id){
    showUniversalPopup('Review data is missing', 'error', 3000);
    return;
  }

  openReviewModal(data);
}

// Attach Form Submit Handlers
document.addEventListener('DOMContentLoaded', () => {
  const desktopForm = document.getElementById('review-form');
  const mobileForm = document.getElementById('mobile-review-form-element');

  if(desktopForm){
    desktopForm.addEventListener('submit', handleReviewFormSubmit);
  }

  if(mobileForm){
    mobileForm.addEventListener('submit', handleReviewFormSubmit);
  }
});
