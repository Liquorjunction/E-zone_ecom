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

Route::get('/user-dashboard', function () {
    return view('client.pages.dashboard');
})->name('user-dashboard');
