{{-- Write Review Modal --}}
<div id="review-modal" style="z-index: 99999;" class="hidden fixed inset-0 items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-300">
  <div class="bg-white rounded-3xl w-full max-w-3xl shadow-2xl transform transition-all duration-300 scale-95 opacity-0 max-h-[90vh] overflow-hidden flex flex-col" id="review-modal-content">
    <div class="flex items-center justify-between px-8 py-6 border-b border-gray-200 bg-linear-to-r from-primary/5 to-primary/10">
      <div class="flex items-center gap-3">
        <div class="bg-primary/10 p-2.5 rounded-xl">
          <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
          </svg>
        </div>
        <h3 id="review-modal-title" class="text-2xl font-bold text-gray-900">Write a Review</h3>
      </div>
      <button type="button" onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-lg transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <form id="review-form" method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data" class="overflow-y-auto flex-1">
      @csrf
      <input type="hidden" name="_method" id="review-form-method" value="POST">
      <input type="hidden" name="id" id="review-id" value="">
      <input type="hidden" name="product_id" id="review-product-id" value="">
      <input type="hidden" name="product_variant_id" id="review-product-variant-id" value="">
      <input type="hidden" name="order_id" id="review-order-id" value="">
      <input type="hidden" name="order_item_id" id="review-order-item-id" value="">

      <div class="px-8 py-6 space-y-6">
        {{-- Product Info Display --}}
        <div id="review-product-info" class="bg-gray-50 rounded-xl p-4 border border-gray-200 hidden">
          <div class="flex items-center gap-4">
            <img id="review-product-image" src="" alt="" class="w-16 h-16 object-cover rounded-lg">
            <div class="flex-1">
              <h4 id="review-product-name" class="font-semibold text-gray-900"></h4>
              <p id="review-product-variant" class="text-sm text-gray-600"></p>
            </div>
          </div>
        </div>

        {{-- Rating --}}
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Overall Rating <span class="text-red-500">*</span></label>
          <p class="text-xs text-gray-500 mb-2">Select 1 to 5 stars to rate this product.</p>
          <div class="flex items-center gap-2">
            <div class="flex gap-1" id="star-rating">
              @for($i = 1; $i <= 5; $i++)
                <button type="button" onclick="setRating({{ $i }})" class="star-btn text-gray-300 hover:text-yellow-400 transition-colors" data-rating="{{ $i }}">
                  <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                  </svg>
                </button>
              @endfor
            </div>
            <span id="rating-text" class="text-sm font-medium text-gray-600 ml-2"></span>
          </div>
          <p id="review-rating-error" class="mt-2 text-xs text-red-600 hidden">Please select a rating.</p>
          <input type="hidden" name="rating" id="review-rating" value="" required>
        </div>

        {{-- Title --}}
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Review Title</label>
          <p class="text-xs text-gray-500 mb-2">Summarize your experience in a short headline.</p>
          <input type="text" name="title" id="review-title" placeholder="Summarize your experience" maxlength="255" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
        </div>

        {{-- Review Text --}}
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-1">Your Review</label>
          <p class="text-xs text-gray-500 mb-2">Share what you liked, what could be better, and any tips for others.</p>
          <textarea name="review_text" id="review-text" rows="5" placeholder="Share your thoughts about this product..." class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
          {{-- Reviewer Name --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Display Name</label>
            <p class="text-xs text-gray-500 mb-2">Shown publicly with your review.</p>
            <input type="text" name="reviewer_name" id="review-name" placeholder="Your name" maxlength="255" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
          </div>

          {{-- Reviewer Email --}}
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span id="review-email-optional">(Optional)</span></label>
            <p class="text-xs text-gray-500 mb-2">Used for verification only, not displayed.</p>
            <input type="email" name="reviewer_email" id="review-email" placeholder="your@email.com" maxlength="255" class="block w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" />
          </div>
        </div>

        {{-- Images --}}
        <div id="review-image-section">
          <label class="block text-sm font-semibold text-gray-700 mb-2">Add Photos <span id="review-images-optional" class="text-gray-400 font-normal">(Optional)</span></label>
          <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-primary hover:bg-primary/5 transition-all cursor-pointer" id="image-upload-area">
            <input type="file" name="images[]" id="review-images" multiple accept="image/*" class="hidden" onchange="handleImagePreview(this)">
            <label for="review-images" class="cursor-pointer">
              <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
              </svg>
              <p class="text-sm font-medium text-gray-600">Click to upload images</p>
              <p class="text-xs text-gray-400 mt-1">Max 2MB per image</p>
            </label>
          </div>
          <div id="image-preview-container" class="grid grid-cols-4 gap-3 mt-4 hidden"></div>
        </div>
      </div>

      <div class="flex items-center justify-end gap-3 px-8 py-6 bg-gray-50 border-t border-gray-200">
        <button type="button" id="review-cancel-btn" onclick="closeReviewModal()" class="px-6 py-3 rounded-xl bg-white border-2 border-gray-200 text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-300 transition-all">
          Cancel
        </button>
        <button type="submit" id="review-submit-btn" class="px-6 py-3 rounded-xl bg-primary text-white font-semibold hover:shadow-lg hover:scale-105 transition-all flex items-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Submit Review
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Mobile Review Form --}}
<div id="mobile-review-form" style="z-index: 99999;" class="sm:hidden fixed inset-0 bg-white transform translate-y-full transition-transform duration-300">
  <div class="sticky top-0 z-10 bg-linear-to-r from-primary/5 to-primary/10 border-b border-gray-200">
    <div class="flex items-center justify-between px-4 py-4">
      <button type="button" onclick="closeMobileReviewForm()" class="p-2 hover:bg-white/50 rounded-lg transition-colors">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        <span id="mobile-review-back-label" class="sr-only">Back</span>
      </button>
      <h3 id="mobile-review-modal-title" class="text-lg font-bold text-gray-900">Write a Review</h3>
      <div class="w-10"></div>
    </div>
  </div>

  <div class="overflow-y-auto h-[calc(100vh-73px)] pb-24">
    <form id="mobile-review-form-element" method="POST" action="{{ route('reviews.store') }}" enctype="multipart/form-data" class="p-4 space-y-5">
      @csrf
      <input type="hidden" name="_method" id="mobile-review-form-method" value="POST">
      <input type="hidden" name="id" id="mobile-review-id" value="">
      <input type="hidden" name="product_id" id="mobile-review-product-id" value="">
      <input type="hidden" name="product_variant_id" id="mobile-review-product-variant-id" value="">
      <input type="hidden" name="order_id" id="mobile-review-order-id" value="">
      <input type="hidden" name="order_item_id" id="mobile-review-order-item-id" value="">

      {{-- Mobile Product Info --}}
      <div id="mobile-review-product-info" class="bg-gray-50 rounded-xl p-4 border border-gray-200 hidden">
        <div class="flex items-center gap-4">
          <img id="mobile-review-product-image" src="" alt="" class="w-16 h-16 object-cover rounded-lg">
          <div class="flex-1">
            <h4 id="mobile-review-product-name" class="font-semibold text-gray-900"></h4>
            <p id="mobile-review-product-variant" class="text-sm text-gray-600"></p>
          </div>
        </div>
      </div>

      {{-- Mobile Rating --}}
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Overall Rating <span class="text-red-500">*</span></label>
        <p class="text-xs text-gray-500 mb-2">Select 1 to 5 stars to rate this product.</p>
        <div class="flex items-center gap-2">
          <div class="flex gap-1" id="mobile-star-rating">
            @for($i = 1; $i <= 5; $i++)
              <button type="button" onclick="setMobileRating({{ $i }})" class="mobile-star-btn text-gray-300 hover:text-yellow-400 transition-colors" data-rating="{{ $i }}">
                <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                  <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
              </button>
            @endfor
          </div>
        </div>
        <p id="mobile-review-rating-error" class="mt-2 text-xs text-red-600 hidden">Please select a rating.</p>
        <input type="hidden" name="rating" id="mobile-review-rating" value="" required>
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Review Title</label>
        <p class="text-xs text-gray-500 mb-2">Summarize your experience in a short headline.</p>
        <input type="text" name="title" id="mobile-review-title" placeholder="Summarize your experience" maxlength="255" class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-base" />
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Your Review</label>
        <p class="text-xs text-gray-500 mb-2">Share what you liked, what could be better, and any tips for others.</p>
        <textarea name="review_text" id="mobile-review-text" rows="5" placeholder="Share your thoughts..." class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all resize-none text-base"></textarea>
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Display Name</label>
        <p class="text-xs text-gray-500 mb-2">Shown publicly with your review.</p>
        <input type="text" name="reviewer_name" id="mobile-review-name" placeholder="Your name" maxlength="255" class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-base" />
      </div>

      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span id="mobile-review-email-optional">(Optional)</span></label>
        <p class="text-xs text-gray-500 mb-2">Used for verification only, not displayed.</p>
        <input type="email" name="reviewer_email" id="mobile-review-email" placeholder="your@email.com" maxlength="255" class="block w-full px-4 py-3.5 rounded-xl border-2 border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-base" />
      </div>

      <div id="mobile-review-image-section">
        <label class="block text-sm font-semibold text-gray-700 mb-2">Add Photos <span id="mobile-review-images-optional">(Optional)</span></label>
        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center" id="mobile-image-upload-area">
          <input type="file" name="images[]" id="mobile-review-images" multiple accept="image/*" class="hidden" onchange="handleMobileImagePreview(this)">
          <label for="mobile-review-images" class="cursor-pointer">
            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="text-sm font-medium text-gray-600">Click to upload</p>
          </label>
        </div>
        <div id="mobile-image-preview-container" class="grid grid-cols-3 gap-2 mt-3 hidden"></div>
      </div>
    </form>
  </div>

  <div class="fixed bottom-0 left-0 right-0 bg-white border-t-2 border-gray-200 p-4 shadow-2xl">
    <button type="button" onclick="submitMobileReviewForm()" id="mobile-review-submit-btn" class="w-full py-4 rounded-xl bg-primary text-white font-bold text-base hover:shadow-lg transition-all flex items-center justify-center gap-2">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
      </svg>
      Submit Review
    </button>
  </div>
</div>
