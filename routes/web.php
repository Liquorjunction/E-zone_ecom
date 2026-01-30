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
use App\Http\Controllers\Client\ShippingAndReturnsController;
use App\Http\Controllers\Client\TermsController;
use App\Http\Controllers\Client\VerificationController;
use App\Http\Controllers\Client\InventoryController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\ReviewController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Client\SecurityController;
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        Session::put('locale', $locale);
    }

    return Redirect::back();
})->name('lang.switch');

Route::group([], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');
    Route::get('/faq', [FaqController::class, 'index'])->name('faq');
    Route::get('/terms', [TermsController::class, 'index'])->name('terms');
    Route::get('/shipping-returns', [ShippingAndReturnsController::class, 'index'])->name('shipping-returns');
    Route::get('/about-us', [AboutUsController::class, 'index'])->name('about');
    Route::get('/brands', [BrandController::class, 'index'])->name('brands');
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
    Route::view('/order-tracking', 'client.pages.order-tracking')->name('order-tracking');
    Route::get('/brand/{slug}', [BrandController::class, 'productsByBrand'])->name('brand.products');
});

Route::post('/inventory/transactions', [InventoryController::class, 'transactions'])->name('inventory.transactions');

Route::middleware('auth')->group(function () {
    Route::post('/inventory/reserve', [InventoryController::class, 'reserve'])->name('inventory.reserve');
    Route::post('/inventory/release', [InventoryController::class, 'release'])->name('inventory.release');
    Route::post('/inventory/return', [InventoryController::class, 'processReturn'])->name('inventory.return');
});

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/deals', [ProductController::class, 'deals'])->name('deals');
    Route::post('/variant-discount', [ProductController::class, 'getVariantDiscount'])->name('variant-discount');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('detail');
});

Route::middleware('guest')->group(function () {
    Route::view('/login', 'client.auth.login')->name('login');
    Route::view('/register', 'client.auth.register')->name('register');

    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'send'])->middleware(['throttle:6,1'])->name('verification.send');

    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [HomeController::class, 'dashboard'])->name('overview');
        Route::get('/read', [ProfileController::class, 'readProfile'])->name('profile.read');
        Route::get('/edit', [ProfileController::class, 'editProfile'])->name('profile.edit');
        Route::post('/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/profile', [ProfileController::class, 'showProfile'])->name('profile');
        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::get('/security', [SecurityController::class, 'index'])->name('security');
        Route::post('/security/password', [SecurityController::class, 'updatePassword'])->name('security.update-password');
        Route::post('/security/logout-all', [SecurityController::class, 'logoutAllSessions'])->name('security.logout-all');
        Route::post('/security/revoke-session', [SecurityController::class, 'revokeSession'])->name('security.revoke-session');
        Route::view('/payment-methods', 'client.pages.dashboard.payments')->name('payments');
        Route::view('/rewards', 'client.pages.dashboard.rewards')->name('rewards');
    });

    Route::get('/checkout', [OrderController::class, 'show'])->name('checkout');
    Route::post('/checkout', [OrderController::class, 'checkout'])->name('checkout.process');
    Route::get('/client/orders/invoice', [OrderController::class, 'invoice'])->name('client.orders.invoice');
    Route::post('/client/orders/cancel', [OrderController::class, 'cancel'])->name('client.orders.cancel');
    Route::get('/order-detail', [OrderController::class, 'detail'])->name('order-detail');

    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
});

Route::get('/cart', [CartController::class, 'show'])->name('cart');
Route::get('/cart/add', function () {
    return redirect()->route('cart');
})->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/apply-coupon', [CartController::class, 'applyCoupon'])->name('cart.applyCoupon');
    Route::post('/cart/remove-coupon', [CartController::class, 'removeCoupon'])->name('cart.removeCoupon');
});

Route::prefix('client')->middleware('auth')->group(function () {
    Route::post('/addresses', [AddressController::class, 'store']);
    Route::post('/addresses/show', [AddressController::class, 'show']);
    Route::post('/addresses/update', [AddressController::class, 'update']);
    Route::post('/addresses/delete', [AddressController::class, 'destroy']);
    Route::post('/addresses/default', [AddressController::class, 'setDefault']);
    Route::post('/wishlist/add', [WishlistController::class, 'addToWishlist']);
    Route::post('/wishlist/remove', [WishlistController::class, 'removeFromWishlist']);
    Route::post('/wishlist/get', [WishlistController::class, 'getWishlist']);
    Route::post('/wishlist/count', [WishlistController::class, 'countWishlist']);
    Route::post('/wishlist/clear', [WishlistController::class, 'clearWishlist']);
});
