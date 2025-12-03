<?php

use App\Http\Controllers\Client\AboutUsController;
use App\Http\Controllers\Client\PrivacyPolicyController;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\FaqController;
use App\Http\Controllers\Client\HomeController;
use App\Http\Controllers\Client\ShippingandReturnsController;
use App\Http\Controllers\Client\TermsController;
use App\Http\Controllers\Client\VerificationController;
use App\Models\Card;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        Session::put('locale', $locale);
    }
    return Redirect::back();
})->name('lang.switch');

Route::get('/test-cards', function () {
    $cards = \App\Models\Card::all();
    return view('client.pages.test-cards', compact('cards'));
});

Route::middleware('guest')->group(function () {
    // Actions
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    // Privacy Policy
    Route::get('/privacy-policy', [PrivacyPolicyController::class, 'index'])->name('privacy-policy');

    // Faq
    Route::get('/faq', [FaqController::class, 'index'])->name('faq');

    // Terms
    Route::get('/terms', [TermsController::class, 'index'])->name('terms');

    // Shipping and Returns
    Route::get('/shipping-returns', [ShippingandReturnsController::class, 'index'])->name('shipping-returns');

    // About Us
    Route::get('/about-us', [AboutUsController::class, 'index'])->name('about');
});

Route::middleware(['auth'])->group(function () {
    // Actions
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Show Notice Page
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    // Handle Link Click (Laravel automatically validates signature)
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify');
    // Resend Email
    Route::post('/email/verification-notification', [VerificationController::class, 'send'])->middleware(['throttle:6,1'])->name('verification.send');
});

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', function () {
    return view('client.pages.products');
})->name('products');

Route::get('/cart', function () {
    return view('client.pages.cart');
})->name('cart');

Route::get('/checkout', function () {
    return view('client.pages.checkout');
})->name('checkout');

Route::get('/login', function () {
    return view('client.auth.login');
})->name('login');

Route::get('/register', function () {
    return view('client.auth.register');
})->name('register');

Route::get('/product-detail', function () {
    return view('client.pages.product-detail');
})->name('product-detail');

Route::get('/checkout-success', function () {
    return view('client.pages.checkout-success');
})->name('checkout-success');

Route::get('/search', function () {
    return view('client.pages.search-results');
})->name('search');

Route::get('/checkout-process', function () {
    return view('client.pages.checkout-process');
})->name('checkout-process');

Route::get('/product-compare', function () {
    return view('client.pages.compare');
})->name('product-compare');

Route::prefix('dashboard')->name('dashboard.')->group(function () {

    // /dashboard (The main overview)
    Route::get('/', function () {
        // Points to /client/pages/dashboard/dashboard.blade.php
        return view('client.pages.dashboard.dashboard');
    })->name('overview');

    // /dashboard/orders
    Route::get('/orders', function () {
        return view('client.pages.dashboard.orders');
    })->name('orders');

    // /dashboard/profile
    Route::get('/profile', function () {
        return view('client.pages.dashboard.profile');
    })->name('profile');

    // /dashboard/addresses
    Route::get('/addresses', function () {
        return view('client.pages.dashboard.addresses');
    })->name('addresses');

    // /dashboard/wishlist
    Route::get('/wishlist', function () {
        return view('client.pages.dashboard.wishlist');
    })->name('wishlist');

    // /dashboard/security
    Route::get('/security', function () {
        return view('client.pages.dashboard.security');
    })->name('security');

    // /dashboard/payments
    Route::get('/payment-methods', function () {
        return view('client.pages.dashboard.payments');
    })->name('payments');

    // /dashboard/rewards
    Route::get('/rewards', function () {
        return view('client.pages.dashboard.rewards');
    })->name('rewards');
});

Route::get('/contact', function () {
    return view('client.pages.contact');
})->name('contact');

Route::get('/showrooms', function () {
    return view('client.pages.showrooms');
})->name('showrooms');

Route::get('/sitemap', function () {
    return view('client.pages.sitemap');
})->name('sitemap');

Route::get('/support', function () {
    return view('client.pages.support');
})->name('support');

Route::get('/new-arrivals', function () {
    return view('client.pages.new-arrivals');
})->name('new-arrivals');

Route::get('/best-sellers', function () {
    return view('client.pages.best-sellers');
})->name('best-sellers');

Route::get('/deals', function () {
    return view('client.pages.deals');
})->name('deals');

Route::get('/brand/{slug}', function ($slug) {
    return view('client.pages.brand-products', ['brand_slug' => $slug]);
})->name('brand-products');

Route::get('/order-tracking', function () {
    return view('client.pages.order-tracking');
})->name('order-tracking');

Route::get('/order-detail/{id}', function ($id) {
    return view('client.pages.order-detail', ['order_id' => $id]);
})->name('order-detail');


