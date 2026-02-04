// Product Detail Page - Reviews functionality
(function() {
    'use strict';

    let currentPage = 1;
    let reviewsPerPage = 5;
    let allReviews = [];
    let selectedRating = 0;

    // DOM Elements
    const reviewModal = document.getElementById('reviewModal');
    const writeReviewBtn = document.getElementById('writeReviewBtn');
    const closeReviewModalBtn = document.getElementById('closeReviewModal');
    const cancelReviewBtn = document.getElementById('cancelReview');
    const reviewForm = document.getElementById('reviewForm');
    const starRating = document.getElementById('starRating');
    const ratingInput = document.getElementById('ratingInput');
    const ratingText = document.getElementById('ratingText');
    const ratingError = document.getElementById('ratingError');
    const reviewsList = document.getElementById('reviewsList');
    const loadMoreBtn = document.getElementById('loadMoreReviews');
    const loadMoreContainer = document.getElementById('loadMoreContainer');

    // Initialize when DOM is ready (handle late script loading)
    function initReviews() {
        initializeEventListeners();
        loadReviews();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initReviews);
    } else {
        initReviews();
    }

    function initializeEventListeners() {
        // Modal controls
        if (writeReviewBtn) {
            writeReviewBtn.addEventListener('click', openReviewModal);
        }

        if (closeReviewModalBtn) {
            closeReviewModalBtn.addEventListener('click', closeReviewModal);
        }

        if (cancelReviewBtn) {
            cancelReviewBtn.addEventListener('click', closeReviewModal);
        }

        // Click outside modal to close
        if (reviewModal) {
            reviewModal.addEventListener('click', function(e) {
                if (e.target === reviewModal) {
                    closeReviewModal();
                }
            });
        }

        // Star rating
        if (starRating) {
            const stars = starRating.querySelectorAll('.star');
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    selectRating(parseInt(this.dataset.rating));
                });

                star.addEventListener('mouseenter', function() {
                    hoverRating(parseInt(this.dataset.rating));
                });
            });

            starRating.addEventListener('mouseleave', function() {
                updateStarDisplay(selectedRating);
            });
        }

        // Form submission
        if (reviewForm) {
            reviewForm.addEventListener('submit', handleReviewSubmit);
        }

        // Load more reviews
        if (loadMoreBtn) {
            loadMoreBtn.addEventListener('click', loadMoreReviews);
        }
    }

    function openReviewModal() {
        if (!window.IS_AUTHENTICATED) {
            window.location.href = '/login';
            return;
        }

        // Set product and variant IDs
        document.getElementById('reviewProductId').value = window.PRODUCT_ID;
        document.getElementById('reviewVariantId').value = window.PRODUCT_VARIANT_ID || '';

        reviewModal.classList.remove('hidden');
        reviewModal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeReviewModal() {
        reviewModal.classList.add('hidden');
        reviewModal.classList.remove('flex');
        document.body.style.overflow = '';
        resetReviewForm();
    }

    function resetReviewForm() {
        if (reviewForm) {
            reviewForm.reset();
        }
        selectedRating = 0;
        updateStarDisplay(0);
        ratingInput.value = '';
        ratingText.textContent = '';
        ratingError.classList.add('hidden');
    }

    function selectRating(rating) {
        selectedRating = rating;
        ratingInput.value = rating;
        updateStarDisplay(rating);
        updateRatingText(rating);
        ratingError.classList.add('hidden');
    }

    function hoverRating(rating) {
        updateStarDisplay(rating);
        updateRatingText(rating);
    }

    function updateStarDisplay(rating) {
        const stars = starRating.querySelectorAll('.star');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.textContent = '★';
                star.classList.add('text-yellow-400');
            } else {
                star.textContent = '☆';
                star.classList.remove('text-yellow-400');
            }
        });
    }

    function updateRatingText(rating) {
        const texts = ['', 'Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
        ratingText.textContent = texts[rating] || '';
    }

    async function handleReviewSubmit(e) {
        e.preventDefault();

        // Validate rating
        if (!selectedRating || selectedRating < 1) {
            ratingError.classList.remove('hidden');
            return;
        }

        const submitBtn = document.getElementById('submitReview');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submitting...';

        try {
            const formData = new FormData(reviewForm);

            const response = await fetch('/reviews', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok) {
                showNotification('success', 'Review submitted successfully! It will appear after approval.');
                closeReviewModal();
                // Optionally reload reviews
                setTimeout(() => loadReviews(), 500);
            } else {
                const errorMessage = data.message || 'Failed to submit review. Please try again.';
                showNotification('error', errorMessage);

                // Display validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        console.error(`${key}: ${data.errors[key].join(', ')}`);
                    });
                }
            }
        } catch (error) {
            console.error('Error submitting review:', error);
            showNotification('error', 'An error occurred. Please try again later.');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    async function loadReviews() {
        try {
            const productId = window.PRODUCT_ID;
            const variantId = window.PRODUCT_VARIANT_ID;

            let url = `/reviews?product_id=${productId}`;
            if (variantId) {
                url += `&variant_id=${variantId}`;
            }

            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();

            if (response.ok) {
                allReviews = data.reviews || [];
                currentPage = 1;
                const avgRating = calculateAverageRating(allReviews);
                updateHeaderReviewSummary(avgRating, allReviews.length);
                updateReviewsTabCount(allReviews.length);
                displayReviews();
                updateReviewStats();
            } else {
                console.error('Failed to load reviews:', data);
                showNoReviews();
            }
        } catch (error) {
            console.error('Error loading reviews:', error);
            showNoReviews();
        }
    }

    function displayReviews() {
        if (!allReviews || allReviews.length === 0) {
            showNoReviews();
            return;
        }

        const reviewsToShow = allReviews.slice(0, currentPage * reviewsPerPage);
        reviewsList.innerHTML = '';

        reviewsToShow.forEach(review => {
            reviewsList.appendChild(createReviewElement(review));
        });

        // Show/hide load more button
        if (allReviews.length > reviewsToShow.length) {
            loadMoreContainer.classList.remove('hidden');
        } else {
            loadMoreContainer.classList.add('hidden');
        }
    }

    function createReviewElement(review) {
        const div = document.createElement('div');
        div.className = 'bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow';

        const userName = review.reviewer_name || review.user?.name || 'Anonymous';
        const userInitial = userName.charAt(0).toUpperCase();
        const reviewDate = formatDate(review.created_at);
        const stars = generateStars(review.rating);
        const isVerified = review.is_verified_purchase ? '<span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full ml-2">Verified Purchase</span>' : '';

        div.innerHTML = `
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-linear-to-br from-primary to-primary-dark rounded-full flex items-center justify-center text-white font-bold text-lg shadow-sm">
                        ${userInitial}
                    </div>
                    <div>
                        <div class="flex items-center">
                            <div class="font-bold text-gray-900">${escapeHtml(userName)}</div>
                            ${isVerified}
                        </div>
                        <div class="text-sm text-gray-500">${reviewDate}</div>
                    </div>
                </div>
                <div class="flex items-center text-yellow-400 text-lg">
                    ${stars}
                </div>
            </div>
            ${review.title ? `<h5 class="font-bold text-gray-900 mb-2 text-lg">${escapeHtml(review.title)}</h5>` : ''}
            ${review.review_text ? `<p class="text-gray-700 leading-relaxed">${escapeHtml(review.review_text)}</p>` : ''}
            ${review.images && review.images.length > 0 ? `
                <div class="flex gap-2 mt-4 flex-wrap">
                    ${review.images.map(img => `<img src="${img.image_path}" alt="Review image" class="w-20 h-20 object-cover rounded-lg border border-gray-200">`).join('')}
                </div>
            ` : ''}
        `;

        return div;
    }

    function generateStars(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            stars += i <= rating ? '★' : '☆';
        }
        return stars;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return 'Today';
        if (diffDays === 1) return 'Yesterday';
        if (diffDays < 7) return `${diffDays} days ago`;
        if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
        if (diffDays < 365) return `${Math.floor(diffDays / 30)} months ago`;
        return `${Math.floor(diffDays / 365)} years ago`;
    }

    function updateReviewStats() {
        if (!allReviews || allReviews.length === 0) {
            document.getElementById('avgRating').textContent = '-';
            document.getElementById('avgRatingStars').innerHTML = '☆☆☆☆☆';
            document.getElementById('reviewCount').textContent = 'No reviews yet';
            document.getElementById('ratingBreakdown').innerHTML = '<p class="text-gray-500 text-center py-4">No ratings yet</p>';
            updateHeaderReviewSummary(0, 0);
            updateReviewsTabCount(0);
            return;
        }

        // Calculate average rating
        const totalRating = allReviews.reduce((sum, review) => sum + review.rating, 0);
        const avgRating = (totalRating / allReviews.length).toFixed(1);

        document.getElementById('avgRating').textContent = avgRating;
        document.getElementById('avgRatingStars').innerHTML = generateStars(Math.round(avgRating));
        document.getElementById('reviewCount').textContent = `Based on ${allReviews.length} review${allReviews.length !== 1 ? 's' : ''}`;
        updateHeaderReviewSummary(avgRating, allReviews.length);
        updateReviewsTabCount(allReviews.length);

        // Calculate rating breakdown
        const ratingCounts = [0, 0, 0, 0, 0];
        allReviews.forEach(review => {
            if (review.rating >= 1 && review.rating <= 5) {
                ratingCounts[review.rating - 1]++;
            }
        });

        const breakdownHTML = ratingCounts.map((count, index) => {
            const star = 5 - index;
            const percentage = allReviews.length > 0 ? (count / allReviews.length * 100).toFixed(0) : 0;

            return `
                <div class="flex items-center gap-3">
                    <span class="text-sm font-semibold text-gray-700 w-12">${star} ★</span>
                    <div class="flex-1 bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-yellow-400 h-3 rounded-full transition-all" style="width: ${percentage}%"></div>
                    </div>
                    <span class="text-sm text-gray-600 font-medium w-12 text-right">${count}</span>
                </div>
            `;
        }).reverse().join('');

        document.getElementById('ratingBreakdown').innerHTML = breakdownHTML;
    }

    function showNoReviews() {
        reviewsList.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <p class="text-gray-500 text-lg font-medium mb-2">No reviews yet</p>
                <p class="text-gray-400">Be the first to review this product!</p>
            </div>
        `;
        loadMoreContainer.classList.add('hidden');
        updateReviewStats();
    }

    function updateHeaderReviewSummary(avgRating, reviewCount) {
        const headerStars = document.getElementById('productHeaderStars');
        const headerCount = document.getElementById('productHeaderReviewCount');

        if (headerStars) {
            const rounded = Math.round(parseFloat(avgRating) || 0);
            headerStars.innerHTML = generateStars(rounded);
        }

        if (headerCount) {
            headerCount.textContent = `(${reviewCount} review${reviewCount !== 1 ? 's' : ''})`;
        }
    }

    function updateReviewsTabCount(reviewCount) {
        const tabCount = document.getElementById('reviewsTabCount');
        if (tabCount) {
            tabCount.textContent = reviewCount;
        }
    }

    function calculateAverageRating(reviews) {
        if (!reviews || reviews.length === 0) return 0;
        const totalRating = reviews.reduce((sum, review) => sum + (parseFloat(review.rating) || 0), 0);
        return (totalRating / reviews.length).toFixed(1);
    }

    function loadMoreReviews() {
        currentPage++;
        displayReviews();
    }

    function showNotification(type, message) {
        // Using Toast if available
        if (window.Toast) {
            if (type === 'success') {
                window.Toast.success({ title: 'Success', message });
            } else {
                window.Toast.error({ title: 'Error', message });
            }
            return;
        }

        // Fallback to alert
        alert(message);
    }

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Update variant ID when variant is selected (hook into variant change event)
    document.addEventListener('variantChanged', function(e) {
        if (e.detail && e.detail.variantId) {
            window.PRODUCT_VARIANT_ID = e.detail.variantId;
            loadReviews(); // Reload reviews for the new variant
        }
    });

    // Expose loadReviews function globally for external use
    window.ProductReviews = {
        reload: loadReviews
    };

})();
