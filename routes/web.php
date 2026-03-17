<?php

use App\Http\Controllers\Client\AboutUsController;
use App\Http\Controllers\Client\AddressController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\BrandController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\CategoryController;
use App\Http\Controllers\Client\ContactUsController;
use App\Http\Controllers\Client\CompareController;
use App\Http\Controllers\Client\FaqController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\InventoryController;
use App\Http\Controllers\Client\NewsletterController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\PrivacyPolicyController;
use App\Http\Controllers\Client\ProductController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\ReviewController;
use App\Http\Controllers\Client\SearchController;
use App\Http\Controllers\Client\SecurityController;
use App\Http\Controllers\Client\ShowroomController;
use App\Http\Controllers\Client\ShippingAndReturnsController;
use App\Http\Controllers\Client\SubCategoryController;
use App\Http\Controllers\Client\TermsController;
use App\Http\Controllers\Client\WishlistController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Language Switching
|--------------------------------------------------------------------------
*/

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        Session::put('locale', $locale);
        Session::put('locale_user_selected', true);
    }
    return Redirect::back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Home & Static Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [AboutUsController::class, 'index'])->name('about');
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::get('/terms', [TermsController::class, 'index'])->name('terms');
Route::get('/shipping-returns', [ShippingAndReturnsController::class, 'index'])->name('shipping-returns');
Route::view('/support', 'client.pages.support')->name('support');
Route::get('/showrooms', [ShowroomController::class, 'index'])->name('showrooms');
Route::get('/showrooms/{slug}', [ShowroomController::class, 'show'])->name('showrooms.show');
Route::view('/sitemap', 'client.pages.sitemap')->name('sitemap');

// Brands
Route::get('/brands', [BrandController::class, 'index'])->name('brands');
Route::get('/brand/{slug}', [BrandController::class, 'productsByBrand'])->name('brand.products');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
Route::get('/category/{slug}', [CategoryController::class, 'productsByCategory'])->name('category.products');

// Subcategories
Route::get('/subcategories', [SubCategoryController::class, 'index'])->name('subcategories');
Route::get('/subcategory/{slug}', [SubCategoryController::class, 'productsBySubCategory'])->name('subcategory.products');

// Products
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/deals', [ProductController::class, 'deals'])->name('deals');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('detail');
    Route::post('/variant-discount', [ProductController::class, 'getVariantDiscount'])->name('variant-discount');
    Route::get('/{category}/{subcategory}/products', [ProductController::class, 'index'])->name('index.with-category');
});

// SEO-friendly product detail route (should be after all more specific routes)
Route::get('/{category}/{subcategory}/{slug}', [ProductController::class, 'show'])->name('products.detail.with-category');

// Product Discovery
Route::get('/ajax/search', [SearchController::class, 'ajaxSearch']);
Route::view('/new-arrivals', 'client.pages.new-arrivals')->name('new-arrivals');
Route::view('/best-sellers', 'client.pages.best-sellers')->name('best-sellers');
Route::get('/product-compare', [CompareController::class, 'index'])->name('product-compare');
Route::prefix('compare')->group(function () {
    Route::post('/add', [CompareController::class, 'add'])->name('compare.add');
    Route::post('/remove', [CompareController::class, 'remove'])->name('compare.remove');
    Route::post('/get', [CompareController::class, 'get'])->name('compare.get');
    Route::post('/count', [CompareController::class, 'count'])->name('compare.count');
    Route::post('/clear', [CompareController::class, 'clear'])->name('compare.clear');
});

// Reviews (Public Read)
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
Route::get('/reviews/show', [ReviewController::class, 'show'])->name('reviews.show');

// Cart (Public View)
Route::get('/cart', [CartController::class, 'show'])->name('cart');

// Inventory (Public)
Route::post('/inventory/transactions', [InventoryController::class, 'transactions'])->name('inventory.transactions');

// Newsletter (Public)
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

// Demo/Process Pages
Route::view('/checkout-process', 'client.pages.checkout-process')->name('checkout-process');
Route::view('/checkout-demo', 'client.pages.checkout')->name('checkout.demo');
Route::view('/order-tracking', 'client.pages.order-tracking')->name('order-tracking');

// Email & Password Routes (Accessible to all users - authenticated & guests)
Route::get('/email/verify', [AuthController::class, 'verifyEmail'])->name('email.verify');

// Contact Us Form Submission (Public)
Route::get('/contact', [ContactUsController::class, 'index'])->name('contact');
Route::post('/contact', [ContactUsController::class, 'store'])->name('contact.submit');

/*
|--------------------------------------------------------------------------
| Guest Routes (Unauthenticated Users Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::view('/login', 'client.auth.login')->name('login');
    Route::view('/register', 'client.auth.register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/register/edit', [AuthController::class, 'editPendingRegistration'])->name('register.edit');
    Route::view('/register/success', 'client.partials.register-success')->name('register.success');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Client Users)
|--------------------------------------------------------------------------
*/
Route::middleware('client.auth')->group(function () {

    // Authentication
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email Verification (Accessible to unverified users)
    Route::get('/email/verification-pending', [AuthController::class, 'pendingVerification'])->name('verification.pending');
    Route::post('/email/resend-verification', [AuthController::class, 'resendVerificationEmail'])->middleware('throttle:3,5')->name('verification.resend');

    // Protected Routes (Require Email Verification)
    Route::middleware('verified.email')->group(function () {
        // Dashboard
        Route::prefix('dashboard')->name('dashboard.')->group(function () {
            Route::get('/', [HomeController::class, 'dashboard'])->name('overview');

            // Profile
            Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
            Route::get('/read', [ProfileController::class, 'readProfile'])->name('profile.read');
            Route::get('/edit', [ProfileController::class, 'editProfile'])->name('profile.edit');
            Route::post('/update', [ProfileController::class, 'updateProfile'])->name('profile.update');

            // Security
            Route::get('/security', [SecurityController::class, 'index'])->name('security');
            Route::get('/security/sessions-count', [SecurityController::class, 'sessionsCount'])->name('security.sessions-count');
            Route::post('/security/password', [SecurityController::class, 'updatePassword'])->name('security.update-password');
            Route::post('/security/logout-all', [SecurityController::class, 'logoutAllSessions'])->name('security.logout-all');
            Route::post('/security/revoke-session', [SecurityController::class, 'revokeSession'])->name('security.revoke-session');

            // Dashboard Pages
            Route::get('/orders', [OrderController::class, 'index'])->name('orders');
            Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
            Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
            Route::view('/payment-methods', 'client.pages.dashboard.payments')->name('payments');
            Route::view('/rewards', 'client.pages.dashboard.rewards')->name('rewards');
        });

        // Orders & Checkout
        Route::get('/checkout', [OrderController::class, 'show'])->name('checkout');
        Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.process');
        Route::get('/order-detail', [OrderController::class, 'detail'])->name('order-detail');
        Route::get('/client/orders/invoice', [OrderController::class, 'invoice'])->name('client.orders.invoice');
        Route::get('/client/orders/invoice/download', [OrderController::class, 'downloadInvoice'])->name('client.orders.invoice.download');
        Route::post('/client/orders/cancel', [OrderController::class, 'cancel'])->name('client.orders.cancel');

        // Cart Operations
        Route::get('/cart/add', function () {
            return redirect()->route('cart');
        });
        Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
        Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
        Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
        Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.removeCoupon');

        // Reviews
        Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::put('/reviews', [ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews', [ReviewController::class, 'destroy'])->name('reviews.destroy');
        Route::get('/my-reviews', [ReviewController::class, 'myReviews'])->name('reviews.my');

        // Inventory Operations
        Route::post('/inventory/reserve', [InventoryController::class, 'reserve'])->name('inventory.reserve');
        Route::post('/inventory/release', [InventoryController::class, 'release'])->name('inventory.release');
        Route::post('/inventory/return', [InventoryController::class, 'processReturn'])->name('inventory.return');

        // Addresses
        Route::prefix('client')->group(function () {
            Route::post('/addresses', [AddressController::class, 'store']);
            Route::post('/addresses/show', [AddressController::class, 'show']);
            Route::post('/addresses/update', [AddressController::class, 'update']);
            Route::post('/addresses/delete', [AddressController::class, 'destroy']);
            Route::post('/addresses/default', [AddressController::class, 'setDefault']);

            // Wishlist
            Route::post('/wishlist/add', [WishlistController::class, 'addToWishlist']);
            Route::post('/wishlist/remove', [WishlistController::class, 'removeFromWishlist']);
            Route::post('/wishlist/get', [WishlistController::class, 'getWishlist']);
            Route::post('/wishlist/count', [WishlistController::class, 'countWishlist']);
            Route::post('/wishlist/clear', [WishlistController::class, 'clearWishlist']);
        });
    });
});
