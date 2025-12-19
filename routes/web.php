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

// Home (public)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public content pages (controller-based)
Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');
Route::get('/terms', [TermsController::class, 'index'])->name('terms');
Route::get('/shipping-returns', [ShippingandReturnsController::class, 'index'])->name('shipping-returns');
Route::get('/about-us', [AboutUsController::class, 'index'])->name('about');
Route::get('/brands', [BrandController::class, 'index'])->name('brands');

// Products (public)
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'productDetail'])->name('detail');
});

// Auth pages (guest-only)
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('client.auth.login');
    })->name('login');
    Route::get('/register', function () {
        return view('client.auth.register');
    })->name('register');

    // Auth actions
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

// Authenticated-only actions
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Email verification
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'send'])->middleware(['throttle:6,1'])->name('verification.send');
});

// Cart & checkout views are served by controllers to keep behavior consistent
// Route::view('/cart', 'client.pages.cart')->name('cart');
// Route::view('/checkout', 'client.pages.checkout')->name('checkout');

// login/register GET are defined in guest group above

Route::view('/checkout-success', 'client.pages.checkout-success')->name('checkout-success');
Route::view('/search', 'client.pages.search-results')->name('search');
Route::view('/checkout-process', 'client.pages.checkout-process')->name('checkout-process');
// Design-only checkout view (demo / design preview)
Route::view('/checkout-demo', 'client.pages.checkout')->name('checkout.demo');
Route::view('/product-compare', 'client.pages.compare')->name('product-compare');

// Dashboard static views (can be moved to controller later)
Route::prefix('dashboard')->name('dashboard.')->group(function () {
    Route::view('/', 'client.pages.dashboard.dashboard')->name('overview');
    Route::view('/orders', 'client.pages.dashboard.orders')->name('orders');
    Route::view('/profile', 'client.pages.dashboard.profile')->name('profile');
    Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
    Route::view('/wishlist', 'client.pages.dashboard.wishlist')->name('wishlist');
    Route::view('/security', 'client.pages.dashboard.security')->name('security');
    Route::view('/payment-methods', 'client.pages.dashboard.payments')->name('payments');
    Route::view('/rewards', 'client.pages.dashboard.rewards')->name('rewards');
});

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

// Cart endpoints
// GET: Redirect to cart page (only accessible to authenticated users)
Route::get('/cart/add', function () {
    return redirect()->route('cart');
})->middleware('auth');

// POST: Require authentication for adding to cart so guests are redirected to login
Route::post('/cart/add', [CartController::class, 'add'])->middleware('auth')->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->middleware('auth')->name('cart.update');
Route::post('/cart/remove', [CartController::class, 'remove'])->middleware('auth')->name('cart.remove');
Route::get('/cart', [CartController::class, 'show'])->name('cart');

// Checkout (process)
Route::post('/checkout', [OrderController::class, 'checkout'])->middleware('auth')->name('checkout.process');
// Show checkout form (requires auth)
Route::get('/checkout', [OrderController::class, 'show'])->middleware('auth')->name('checkout');

// Client addresses endpoints (AJAX-driven from dashboard)
Route::post('/client/addresses', [AddressController::class, 'store'])->middleware('auth');
Route::put('/client/addresses/{id}', [AddressController::class, 'update'])->middleware('auth');
Route::delete('/client/addresses/{id}', [AddressController::class, 'destroy'])->middleware('auth');
Route::post('/client/addresses/{id}/default', [AddressController::class, 'setDefault'])->middleware('auth');
