<?php

use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

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
