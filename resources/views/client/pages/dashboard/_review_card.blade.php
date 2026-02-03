@php
  $id = $review->id ?? ($review['id'] ?? null);
  $rating = $review->rating ?? ($review['rating'] ?? 0);
@endphp
<div id="review-card-{{ $id }}" class="review-card bg-white rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-primary/30 p-6" data-review="{{ json_encode($review) }}" data-id="{{ $id }}">
  <div class="flex items-start justify-between mb-4">
    <div class="flex items-center gap-3">
      <div class="bg-primary/10 p-2 rounded-full">
        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
        </svg>
      </div>
      <div>
        <h4 class="font-bold text-gray-900">{{ $review->reviewer_name ?? 'Anonymous' }}</h4>
        <div class="flex items-center gap-2 mt-1">
          <div class="flex gap-0.5">
            @for($i = 1; $i <= 5; $i++)
              <svg class="w-4 h-4 {{ $i <= $rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
            @endfor
          </div>
          @if(isset($review->is_verified_purchase) && $review->is_verified_purchase)
            <span class="text-xs font-semibold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">Verified Purchase</span>
          @endif
        </div>
      </div>
    </div>
    <span class="text-sm text-gray-500">{{ $review->created_at ? $review->created_at->diffForHumans() : '' }}</span>
  </div>

  @if(isset($review->title) && $review->title)
    <h5 class="font-semibold text-gray-900 mb-2">{{ $review->title }}</h5>
  @endif

  @if(isset($review->review_text) && $review->review_text)
    <p class="text-gray-700 text-sm leading-relaxed mb-4">{{ $review->review_text }}</p>
  @endif

  @if(isset($review->images) && $review->images->count() > 0)
    <div class="flex gap-2 mb-4 overflow-x-auto">
      @foreach($review->images as $image)
        <img src="{{ asset($image->image_path) }}" alt="Review image" class="w-20 h-20 object-cover rounded-lg border border-gray-200 cursor-pointer hover:scale-105 transition-transform" onclick="openImageModal('{{ asset($image->image_path) }}')">
      @endforeach
    </div>
  @endif

  @if(isset($review->is_approved) && !$review->is_approved)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4">
      <p class="text-sm text-yellow-800 flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Your review is pending approval
      </p>
    </div>
  @endif

  @auth
    @if(isset($review->user_id) && $review->user_id == auth()->id())
      <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
        <button type="button" onclick="openEditReviewFromElem(this)" class="flex items-center gap-2 text-sm font-semibold text-primary hover:text-primary/80 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          Edit
        </button>
        <button type="button" onclick="deleteReview({{ $id }})" class="flex items-center gap-2 text-sm font-semibold text-red-500 hover:text-red-600 transition-colors">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
          </svg>
          Delete
        </button>
      </div>
    @endif
  @endauth
</div>
