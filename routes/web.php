<?php

use App\Http\Controllers\Client\AboutUsController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\BrandController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\FaqController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\AddressController;
use App\Http\Controllers\Client\PrivacyPolicyController;
use App\Http\Controllers\Client\ProductController;
use App\Http\Controllers\Client\WishlistController;
use App\Http\Controllers\Client\ShippingandReturnsController;
use App\Http\Controllers\Client\TermsController;
use App\Http\Controllers\Client\VerificationController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
// Locale switch (public)
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        Session::put('locale', $locale);
    }

    return Redirect::back();
})->name('lang.switch');

// Public routes: home, content pages, misc views
Route::group([], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq');
    Route::get('/terms', [TermsController::class, 'index'])->name('terms');
    Route::get('/shipping-returns', [ShippingandReturnsController::class, 'index'])->name('shipping-returns');
    Route::get('/about-us', [AboutUsController::class, 'index'])->name('about');
    Route::get('/brands', [BrandController::class, 'index'])->name('brands');

    // Remove legacy `/checkout-success` route — use multi-step checkout `checkout?step=3`
    Route::view('/search', 'client.pages.search-results')->name('search');
    Route::view('/checkout-process', 'client.pages.checkout-process')->name('checkout-process');
    Route::view('/checkout-demo', 'client.pages.checkout')->name('checkout.demo');
    Route::view('/product-compare', 'client.pages.compare')->name('product-compare');

    Route::view('/contact', 'client.pages.contact')->name('contact');
    Route::view('/showrooms', 'client.pages.showrooms')->name('showrooms');
    Route::view('/sitemap', 'client.pages.sitemap')->name('sitemap');
    Route::view('/support', 'client.pages.support')->name('support');
    Route::view('/new-arrivals', 'client.pages.new-arrivals')->name('new-arrivals');
    Route::view('/best-sellers', 'client.pages.best-sellers')->name('best-sellers');
    Route::view('/deals', 'client.pages.deals')->name('deals');
    Route::view('/order-tracking', 'client.pages.order-tracking')->name('order-tracking');

    Route::get('/brand/{slug}', function ($slug) {
        return view('client.pages.brand-products', ['brand_slug' => $slug]);
    })->name('brand-products');

    Route::get('/order-detail/{id}', function ($id) {
        return view('client.pages.order-detail', ['order_id' => $id]);
    })->name('order-detail');
});

// Products
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'productDetail'])->name('detail');
});

// Guest-only auth pages (login / register views + submit actions)
Route::middleware('guest')->group(function () {
    Route::view('/login', 'client.auth.login')->name('login');
    Route::view('/register', 'client.auth.register')->name('register');

    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// Authentication-required routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email verification
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'send'])->middleware(['throttle:6,1'])->name('verification.send');

    // Dashboard (authenticated)
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [HomeController::class, 'dashboard'])->name('overview');
        Route::get('/orders', [HomeController::class, 'orders'])->name('orders');
        Route::view('/profile', 'client.pages.dashboard.profile')->name('profile');
        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::view('/security', 'client.pages.dashboard.security')->name('security');
        Route::view('/payment-methods', 'client.pages.dashboard.payments')->name('payments');
        Route::view('/rewards', 'client.pages.dashboard.rewards')->name('rewards');
    });

    // Checkout (requires auth)
    Route::get('/checkout', [OrderController::class, 'show'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.process');
});

// Cart endpoints: keep `GET /cart` public but require auth for modifications
Route::get('/cart', [CartController::class, 'show'])->name('cart');
Route::get('/cart/add', function () {
    return redirect()->route('cart');
})->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
});

// Client addresses endpoints (AJAX-driven from dashboard)
Route::prefix('client')->middleware('auth')->group(function () {
    Route::post('/addresses', [AddressController::class, 'store']);
    // AJAX endpoints without URL parameters: id is passed in request body
    Route::post('/addresses/show', [AddressController::class, 'show']);
    Route::post('/addresses/update', [AddressController::class, 'update']);
    Route::post('/addresses/delete', [AddressController::class, 'destroy']);
    Route::post('/addresses/default', [AddressController::class, 'setDefault']);
    // Wishlist AJAX endpoints
    Route::post('/wishlist/add', [WishlistController::class, 'addToWishlist']);
    Route::post('/wishlist/remove', [WishlistController::class, 'removeFromWishlist']);
    Route::post('/wishlist/get', [WishlistController::class, 'getWishlist']);
    Route::post('/wishlist/count', [WishlistController::class, 'countWishlist']);
    Route::post('/wishlist/clear', [WishlistController::class, 'clearWishlist']);
});
