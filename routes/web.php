<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('client.pages.home');
})->name('home');

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

Route::get('/about', function () {
    return view('client.pages.about');
})->name('about');

Route::get('/faq', function () {
    return view('client.pages.faq');
})->name('faq');

Route::get('/terms', function () {
    return view('client.pages.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('client.pages.privacy');
})->name('privacy');

Route::get('/shipping-returns', function () {
    return view('client.pages.shipping-returns');
})->name('shipping-returns');

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

Route::get('/admin', function () {
    return view('admin.layouts.app');
})->name('admin-dashboard');

Route::get('/admin/auth', function () {
    return view('admin.auth.auth');
})->name('admin-home');

Route::get('/admin/auth/home', function () {
    return view('admin.pages.home');
})->name('admin-home');

Route::get('/admin/auth/login', function () {
    return view('admin.auth.login');
})->name('admin-login');


