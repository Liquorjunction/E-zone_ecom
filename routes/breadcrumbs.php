<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Str;

// ============================================
// FRONTEND / CLIENT ROUTES
// ============================================

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home'));
});

// Home > Products
Breadcrumbs::for('products.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('Products', route('products.index'));
});

// Home > Products > Product Detail
Breadcrumbs::for('products.detail', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('products.index');
    $trail->push($product->name, route('products.detail', $product->slug));
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
Breadcrumbs::for('privacy-policy', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('privacy-policy', route('privacy-policy'));
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
    $trail->parent('products');
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

// Home > All Brands
Breadcrumbs::for('brands', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push('All Brands', route('brands'));
});


// ============================================
// ADMIN DASHBOARD ROUTES
// ============================================

// Admin Dashboard
Breadcrumbs::for('admin.dashboard', function (BreadcrumbTrail $trail) {
    $trail->push('Dashboard', route('admin.dashboard'));
});

// --- User Management ---
Breadcrumbs::for('admin.user-management.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('User Management', route('admin.user-management.index'));
});

Breadcrumbs::for('admin.user-management.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.user-management.index');
    $trail->push('Create User', route('admin.user-management.create'));
});

// No ID required here anymore
Breadcrumbs::for('admin.user-management.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.user-management.index');
    $trail->push('Edit User', route('admin.user-management.edit'));
});

// --- Roles & Permissions ---
Breadcrumbs::for('admin.roles-permissions.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Roles & Permissions', route('admin.roles-permissions.index'));
});

// This is the child route for User Management you asked for
Breadcrumbs::for('admin.user-management.role-permission', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.user-management.index');
    $trail->push('Role & Permission', route('admin.roles-permissions.index'));
});

Breadcrumbs::for('admin.roles-permissions.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.roles-permissions.index');
    $trail->push('Create Role', route('admin.roles-permissions.create'));
});

Breadcrumbs::for('admin.roles-permissions.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.roles-permissions.index');
    $trail->push('Edit Role', route('admin.roles-permissions.edit'));
});

// --- Profile ---
Breadcrumbs::for('admin.profile.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Profile', route('admin.profile.index'));
});

// Alias for 'admin.profile' if views use it
Breadcrumbs::for('admin.profile', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Profile', route('admin.profile.index'));
});

Breadcrumbs::for('admin.profile.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.profile.index');
    $trail->push('Edit Profile', route('admin.profile.index')); // Adjust route if you have a dedicated edit route
});

Breadcrumbs::for('admin.profile.change-password', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.profile.index');
    $trail->push('Change Password', route('admin.profile.index')); // Adjust route if needed
});

// --- Hero Slider ---
Breadcrumbs::for('admin.hero-slider.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Hero Slider', route('admin.hero-slider.index'));
});

Breadcrumbs::for('admin.hero-slider.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hero-slider.index');
    $trail->push('Create Slider', route('admin.hero-slider.create'));
});

Breadcrumbs::for('admin.hero-slider.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hero-slider.index');
    $trail->push('Edit Slider', route('admin.hero-slider.edit'));
});

// --- Cards ---
Breadcrumbs::for('admin.cards.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Cards', route('admin.cards.index'));
});

Breadcrumbs::for('admin.cards.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.cards.index');
    $trail->push('Create Card', route('admin.cards.create'));
});

Breadcrumbs::for('admin.cards.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.cards.index');
    $trail->push('Edit Card', route('admin.cards.edit'));
});

// --- Categories ---
Breadcrumbs::for('admin.category-subcategory.categories.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('Categories', route('admin.category-subcategory.categories.index'));
});

Breadcrumbs::for('admin.category-subcategory.categories.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.category-subcategory.categories.index');
    $trail->push('Create Category', route('admin.category-subcategory.categories.create'));
});

Breadcrumbs::for('admin.category-subcategory.categories.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.category-subcategory.categories.index');
    $trail->push('Edit Category', route('admin.category-subcategory.categories.edit'));
});

// --- SubCategories ---
Breadcrumbs::for('admin.category-subcategory.subcategories.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push('SubCategories', route('admin.category-subcategory.subcategories.index'));
});

Breadcrumbs::for('admin.category-subcategory.subcategories.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.category-subcategory.subcategories.index');
    $trail->push('Create SubCategory', route('admin.category-subcategory.subcategories.create'));
});

Breadcrumbs::for('admin.category-subcategory.subcategories.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.category-subcategory.subcategories.index');
    $trail->push('Edit SubCategory', route('admin.category-subcategory.subcategories.edit'));
});
