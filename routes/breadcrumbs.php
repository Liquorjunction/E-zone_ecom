<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Str;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

// Home > Products
Breadcrumbs::for('products', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Products', route('products'));
});

// Home > Products > Product Detail
Breadcrumbs::for('product-detail', function (BreadcrumbTrail $trail) {
    $trail->parent('products');
    $trail->push('Product Detail', route('product-detail'));
});

// Home > Cart
Breadcrumbs::for('cart', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Cart', route('cart'));
});

// Home > Checkout
Breadcrumbs::for('checkout', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Checkout', route('checkout'));
});

// Home > Login
Breadcrumbs::for('login', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Login', route('login'));
});

// Home > Register
Breadcrumbs::for('register', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Register', route('register'));
});

// Home > Checkout Success
Breadcrumbs::for('checkout-success', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Checkout Success', route('checkout-success'));
});

// Home > Support
Breadcrumbs::for('support', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Support', route('support'));
});

// Home > Sitemap
Breadcrumbs::for('sitemap', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Sitemap', route('sitemap'));
});

// Home > Compare
Breadcrumbs::for('product-compare', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Compare', route('product-compare'));
});

// Home > Contact
Breadcrumbs::for('contact', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Contact', route('contact'));
});

// Home > About
Breadcrumbs::for('about', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('About', route('about'));
});

// Home > FAQ
Breadcrumbs::for('faq', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('FAQ', route('faq'));
});

// Home > Terms
Breadcrumbs::for('terms', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Terms', route('terms'));
});

// Home > Privacy
Breadcrumbs::for('privacy', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Privacy', route('privacy'));
});

// Home > Shipping Returns
Breadcrumbs::for('shipping-returns', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Shipping Returns', route('shipping-returns'));
});

// Home > Showrooms
Breadcrumbs::for('showrooms', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Showrooms', route('showrooms'));
});

// Home > Dashboard
Breadcrumbs::for('dashboard.overview', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Dashboard', route('dashboard.overview'));
});

// Home > Dashboard > Orders
Breadcrumbs::for('dashboard.orders', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push('Orders', route('dashboard.orders'));
});

// Home > Dashboard > Profile
Breadcrumbs::for('dashboard.profile', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push('Profile', route('dashboard.profile'));
});

// Home > Dashboard > Addresses
Breadcrumbs::for('dashboard.addresses', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push('Addresses', route('dashboard.addresses'));
});

// Home > Dashboard > Wishlist
Breadcrumbs::for('dashboard.wishlist', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push('Wishlist', route('dashboard.wishlist'));
});

// Home > Dashboard > Security
Breadcrumbs::for('dashboard.security', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push('Security', route('dashboard.security'));
});

// Home > Dashboard > Payment Methods
Breadcrumbs::for('dashboard.payments', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push('Payment Methods', route('dashboard.payments'));
});

// Home > Dashboard > Rewards
Breadcrumbs::for('dashboard.rewards', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push('Rewards', route('dashboard.rewards'));
});

// Home > Search
Breadcrumbs::for('search', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Search', route('search'));
});

// Home > New Arrivals
Breadcrumbs::for('new-arrivals', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('New Arrivals', route('new-arrivals'));
});

// Home > Best Sellers
Breadcrumbs::for('best-sellers', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Best Sellers', route('best-sellers'));
});

// Home > Deals
Breadcrumbs::for('deals', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Deals', route('deals'));
});

// Home > Products > [Brand Name]
Breadcrumbs::for('brand-products', function (BreadcrumbTrail $trail, $slug) {
    $trail->parent('products'); // Or 'home' if you prefer
    $trail->push(Str::title($slug), route('brand-products', $slug));
});

// Home > Order Tracking
Breadcrumbs::for('order-tracking', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Order Tracking', route('order-tracking'));
});

// Home > Order Detail
Breadcrumbs::for('order-detail', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('home');
    $trail->push('Order Detail', route('order-detail', $id));
});
