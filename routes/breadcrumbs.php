<?php

use App\Models\Categories;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;
use Illuminate\Support\Str;
use App\Models\Showrooms;

// ============================================
// FRONTEND / CLIENT ROUTES
// ============================================

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push(statictranslations('Home'), route('home'));
});

// Home > Products
Breadcrumbs::for('products.index', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Products'), route('products.index'));
});

// Home > Products > Product Detail
Breadcrumbs::for('products.detail', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('products.index');
    $trail->push($product->name, route('products.detail', $product->slug));
});

// Home > Cart
Breadcrumbs::for('cart', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Cart'), route('cart'));
});

// Home > Checkout
Breadcrumbs::for('checkout', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Checkout'), route('checkout'));
});

// Home > Login
Breadcrumbs::for('login', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Login'), route('login'));
});

// Home > Register
Breadcrumbs::for('register', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Register'), route('register'));
});

// Home > Support
Breadcrumbs::for('support', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Support'), route('support'));
});

// Home > Sitemap
Breadcrumbs::for('sitemap', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Sitemap'), route('sitemap'));
});

// Home > Compare
Breadcrumbs::for('product-compare', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Compare'), route('product-compare'));
});

// Home > Contact
Breadcrumbs::for('contact', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Contact Us'), route('contact'));
});

// Home > About
Breadcrumbs::for('about', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('About Us'), route('about'));
});

// Home > FAQ
Breadcrumbs::for('faq', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('FAQ'), route('faq'));
});

// Home > Terms
Breadcrumbs::for('terms', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Terms & Conditions'), route('terms'));
});

// Home > Privacy
Breadcrumbs::for('privacy-policy', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Privacy Policy'), route('privacy-policy'));
});

// Home > Shipping Returns
Breadcrumbs::for('shipping-returns', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Shipping & Returns'), route('shipping-returns'));
});

// Home > Showrooms
Breadcrumbs::for('showrooms', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Our Showrooms'), route('showrooms'));
});

// Home > Showrooms > Showroom Detail
Breadcrumbs::for('showrooms.show', function (BreadcrumbTrail $trail, $showroom) {
    $trail->parent('showrooms');
    $model = $showroom instanceof Showrooms ? $showroom : Showrooms::where('slug', $showroom)->first();
    $name = $model?->getTranslation('name', app()->getLocale())
        ?? $model?->name
        ?? Str::title(str_replace('-', ' ', (string) $showroom))
        ?? statictranslations('Showroom');
    $trail->push($name, route('showrooms.show', $model?->slug ?? $showroom ?? ''));
});

// Home > Dashboard
Breadcrumbs::for('dashboard.overview', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Dashboard'), route('dashboard.overview'));
});

// Home > Dashboard > Orders
Breadcrumbs::for('dashboard.orders', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push(statictranslations('My Orders'), route('dashboard.orders'));
});

// Home > Dashboard > Profile
Breadcrumbs::for('dashboard.profile', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push(statictranslations('Profile'), route('dashboard.profile'));
});

// Home > Dashboard > Addresses
Breadcrumbs::for('dashboard.addresses', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push(statictranslations('Addresses'), route('dashboard.addresses'));
});

// Home > Dashboard > Wishlist
Breadcrumbs::for('dashboard.wishlist', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push(statictranslations('Wishlist'), route('dashboard.wishlist'));
});

// Home > Dashboard > Security
Breadcrumbs::for('dashboard.security', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push(statictranslations('Security'), route('dashboard.security'));
});

// Home > Dashboard > Payment Methods
Breadcrumbs::for('dashboard.payments', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push(statictranslations('Payment Methods'), route('dashboard.payments'));
});

// Home > Dashboard > Rewards
Breadcrumbs::for('dashboard.rewards', function (BreadcrumbTrail $trail) {
    $trail->parent('dashboard.overview');
    $trail->push(statictranslations('Rewards'), route('dashboard.rewards'));
});

// Home > New Arrivals
Breadcrumbs::for('new-arrivals', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('New Arrivals'), route('new-arrivals'));
});

// Home > Best Sellers
Breadcrumbs::for('best-sellers', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Best Sellers'), route('best-sellers'));
});

// Home > Products > Deals
Breadcrumbs::for('products.deals', function (BreadcrumbTrail $trail) {
    $trail->parent('products.index');
    $trail->push(statictranslations('Deals'), route('products.deals'));
});

// Home > All Brands > [Brand Name]
Breadcrumbs::for('brand.products', function (BreadcrumbTrail $trail, $slug) {
    $trail->parent('brands');
    $trail->push(Str::title($slug), route('brand.products', $slug));
});

// Home > All Categories > [Category Name]
Breadcrumbs::for('category.products', function (BreadcrumbTrail $trail, $category) {
    if (is_string($category)) {
        $category = \App\Models\Categories::where('slug', $category)->first();
    }
    $name = $category?->getTranslation('category-name', app()->getLocale()) ?? $category?->category_name ?? $category?->slug ?? 'Category';
    $trail->parent('categories');
    $trail->push($name, route('category.products', $category?->slug ?? $category));
});

// Home > All Categories > Category > [SubCategory Name]
Breadcrumbs::for('subcategory.products', function (BreadcrumbTrail $trail, $slug) {
    if (is_string($slug)) {
        $subcategory = \App\Models\SubCategories::where('slug', $slug)->first();
    } else {
        $subcategory = $slug;
    }
    $name = $subcategory?->getTranslation('sub-category-name', app()->getLocale()) ?? $subcategory?->subcategory_name ?? $subcategory?->slug ?? 'SubCategory';
    $trail->parent('subcategories');
    $trail->push($name, route('subcategory.products', $subcategory?->slug ?? $subcategory));
});

// Home > Order Tracking
Breadcrumbs::for('order-tracking', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('Order Tracking'), route('order-tracking'));
});

// Home > Order Detail
Breadcrumbs::for('order-detail', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $orderId = request('order');
    $trail->push(statictranslations('Order Detail'), route('order-detail', ['order' => $orderId]));
});

// Home > All Brands
Breadcrumbs::for('brands', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('All Brands'), route('brands'));
});

// Home > All Categories
Breadcrumbs::for('categories', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('All Categories'), route('categories'));
});

// Home > All SubCategories
Breadcrumbs::for('subcategories', function (BreadcrumbTrail $trail) {
    $trail->parent('home');
    $trail->push(statictranslations('All SubCategories'), route('subcategories'));
});

// ============================================
// ADMIN DASHBOARD ROUTES
// ============================================

// Admin Dashboard
Breadcrumbs::for('admin.dashboard', function (BreadcrumbTrail $trail) {
    $trail->push(statictranslations('Dashboard'), route('admin.dashboard'));
});

// --- User Management ---
Breadcrumbs::for('admin.user-management.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('User Management'), route('admin.user-management.index'));
});

Breadcrumbs::for('admin.user-management.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.user-management.index');
    $trail->push(statictranslations('Create User'), route('admin.user-management.create'));
});

Breadcrumbs::for('admin.user-management.view', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.user-management.index');
    $trail->push(statictranslations('View User'), route('admin.user-management.view', $id));
});

// No ID required here anymore
Breadcrumbs::for('admin.user-management.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.user-management.index');
    $trail->push(statictranslations('Edit User'), route('admin.user-management.edit'));
});

// --- Roles & Permissions ---
Breadcrumbs::for('admin.roles-permissions.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Roles & Permissions'), route('admin.roles-permissions.index'));
});

Breadcrumbs::for('admin.roles-permissions.view', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.roles-permissions.index');
    $trail->push(statictranslations('View Role'), route('admin.roles-permissions.show', $id));
});

// This is the child route for User Management you asked for
Breadcrumbs::for('admin.user-management.role-permission', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.user-management.index');
    $trail->push(statictranslations('Role & Permission'), route('admin.roles-permissions.index'));
});

Breadcrumbs::for('admin.roles-permissions.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.roles-permissions.index');
    $trail->push(statictranslations('Create Role'), route('admin.roles-permissions.create'));
});

Breadcrumbs::for('admin.roles-permissions.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.roles-permissions.index');
    $trail->push(statictranslations('Edit Role'), route('admin.roles-permissions.edit'));
});

// --- Newsletter Subscribers ---
Breadcrumbs::for('admin.newsletters.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Newsletter Subscribers'), route('admin.newsletter-subscribers.index'));
});

// --- Profile ---
Breadcrumbs::for('admin.profile.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Profile'), route('admin.profile.index'));
});

// Alias for 'admin.profile' if views use it
Breadcrumbs::for('admin.profile', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Profile'), route('admin.profile.index'));
});

Breadcrumbs::for('admin.profile.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.profile.index');
    $trail->push(statictranslations('Edit Profile'), route('admin.profile.index')); // Adjust route if you have a dedicated edit route
});

Breadcrumbs::for('admin.profile.change-password', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.profile.index');
    $trail->push(statictranslations('Change Password'), route('admin.profile.index')); // Adjust route if needed
});

// --- Hero Slider ---
Breadcrumbs::for('admin.hero-slider.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Hero Slider'), route('admin.hero-slider.index'));
});

Breadcrumbs::for('admin.hero-slider.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hero-slider.index');
    $trail->push(statictranslations('Create Slider'), route('admin.hero-slider.create'));
});

Breadcrumbs::for('admin.hero-slider.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.hero-slider.index');
    $trail->push(statictranslations('Edit Slider'), route('admin.hero-slider.edit'));
});

// --- Cards ---
Breadcrumbs::for('admin.cards.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Cards'), route('admin.cards.index'));
});

Breadcrumbs::for('admin.cards.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.cards.index');
    $trail->push(statictranslations('Create Card'), route('admin.cards.create'));
});

Breadcrumbs::for('admin.cards.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.cards.index');
    $trail->push(statictranslations('Edit Card'), route('admin.cards.edit'));
});

// --- Categories ---
Breadcrumbs::for('admin.category-subcategory.categories.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Categories'), route('admin.category-subcategory.categories.index'));
});

Breadcrumbs::for('admin.category-subcategory.categories.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.category-subcategory.categories.index');
    $trail->push(statictranslations('Create Category'), route('admin.category-subcategory.categories.create'));
});

Breadcrumbs::for('admin.category-subcategory.categories.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.category-subcategory.categories.index');
    $trail->push(statictranslations('Edit Category'), route('admin.category-subcategory.categories.edit'));
});

// --- SubCategories ---
Breadcrumbs::for('admin.category-subcategory.subcategories.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('SubCategories'), route('admin.category-subcategory.subcategories.index'));
});

Breadcrumbs::for('admin.category-subcategory.subcategories.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.category-subcategory.subcategories.index');
    $trail->push(statictranslations('Create SubCategory'), route('admin.category-subcategory.subcategories.create'));
});

Breadcrumbs::for('admin.category-subcategory.subcategories.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.category-subcategory.subcategories.index');
    $trail->push(statictranslations('Edit SubCategory'), route('admin.category-subcategory.subcategories.edit'));
});

// --- Brands ---
Breadcrumbs::for('admin.brands.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Brands'), route('admin.brands.index'));
});

Breadcrumbs::for('admin.brands.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.brands.index');
    $trail->push(statictranslations('Create Brand'), route('admin.brands.create'));
});

Breadcrumbs::for('admin.brands.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.brands.index');
    $trail->push(statictranslations('Edit Brand'), route('admin.brands.edit'));
});

// --- Inventory ---
Breadcrumbs::for('admin.inventory.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Inventory Batches'), route('admin.inventory.index'));
});

Breadcrumbs::for('admin.inventory.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.inventory.index');
    $trail->push(statictranslations('Create Inventory Batch'), route('admin.inventory.create'));
});

Breadcrumbs::for('admin.inventory.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.inventory.index');
    $trail->push(statictranslations('Edit Inventory Batch'), route('admin.inventory.edit'));
});

Breadcrumbs::for('admin.inventory.show', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.inventory.index');
    $trail->push(statictranslations('View Inventory Batch'), route('admin.inventory.show', $id));
});

// --- Order ---
Breadcrumbs::for('admin.order.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Orders'), route('admin.order.index'));
});

// --CMS Pages ---

// --- About Us ---
Breadcrumbs::for('admin.about_us.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('About Us'), route('admin.about_us.index'));
});
Breadcrumbs::for('admin.about_us.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.about_us.index');
    $trail->push(statictranslations('Create About Us'), route('admin.about_us.create'));
});
Breadcrumbs::for('admin.about_us.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.about_us.index');
    $trail->push(statictranslations('Edit About Us'), route('admin.about_us.edit'));
});

// --- Quick Stats ---
Breadcrumbs::for('admin.quick-stats.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Quick Stats'), route('admin.quick-stats.index'));
});
Breadcrumbs::for('admin.quick-stats.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.quick-stats.index');
    $trail->push(statictranslations('Create Quick Stat'), route('admin.quick-stats.create'));
});
Breadcrumbs::for('admin.quick-stats.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.quick-stats.index');
    $trail->push(statictranslations('Edit Quick Stat'), route('admin.quick-stats.edit'));
});


// --- Products ---
Breadcrumbs::for('admin.products.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Products'), route('admin.products.index'));
});
Breadcrumbs::for('admin.products.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.products.index');
    $trail->push(statictranslations('Create Product'), route('admin.products.create'));
});
Breadcrumbs::for('admin.products.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.products.index');
    $trail->push(statictranslations('Edit Product'), route('admin.products.edit'));
});
Breadcrumbs::for('admin.products.show', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.products.index');
    $trail->push(statictranslations('View Product'), route('admin.products.show', $id));
});

// --- Stores ---
Breadcrumbs::for('admin.stores.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Stores'), route('admin.stores.index'));
});
Breadcrumbs::for('admin.stores.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.stores.index');
    $trail->push(statictranslations('Create Store'), route('admin.stores.create'));
});
Breadcrumbs::for('admin.stores.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.stores.index');
    $trail->push(statictranslations('Edit Store'), route('admin.stores.edit'));
});
Breadcrumbs::for('admin.stores.show', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.stores.index');
    $trail->push(statictranslations('View Store'), route('admin.stores.show', $id));
});

// --- Warehouse ---
Breadcrumbs::for('admin.warehouses.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Warehouse'), route('admin.warehouses.index'));
});
Breadcrumbs::for('admin.warehouses.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.warehouses.index');
    $trail->push(statictranslations('Create Warehouse'), route('admin.warehouses.create'));
});
Breadcrumbs::for('admin.warehouses.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.warehouses.index');
    $trail->push(statictranslations('Edit Warehouse'), route('admin.warehouses.edit'));
});
Breadcrumbs::for('admin.warehouses.show', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.warehouses.index');
    $trail->push(statictranslations('View Warehouse'), route('admin.warehouses.show', $id));
});

// --- Inventory Transactions ---
Breadcrumbs::for('admin.inventory-transactions.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Inventory Transactions'), route('admin.inventory-transactions.index'));
});

// --- Cities ---
Breadcrumbs::for('admin.cities.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Cities'), route('admin.cities.index'));
});
Breadcrumbs::for('admin.cities.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.cities.index');
    $trail->push(statictranslations('Create City'), route('admin.cities.create'));
});
Breadcrumbs::for('admin.cities.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.cities.index');
    $trail->push(statictranslations('Edit City'), route('admin.cities.edit'));
});

// --- Manage Customers ---
Breadcrumbs::for('admin.customers.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Manage Customers'), route('admin.customers.index'));
});
Breadcrumbs::for('admin.customers.show', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.customers.index');
    $trail->push(statictranslations('View Customer'), route('admin.customers.show', $id));
});
Breadcrumbs::for('admin.customers.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.customers.index');
    $trail->push(statictranslations('Edit Customer'), route('admin.customers.edit', $id));
});

// --- Why Shop With Us ---
Breadcrumbs::for('admin.whyshopwithus.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Why Shop With Us'), route('admin.whyshopwithus.index'));
});
Breadcrumbs::for('admin.whyshopwithus.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.whyshopwithus.index');
    $trail->push(statictranslations('Create Entry'), route('admin.whyshopwithus.create'));
});
Breadcrumbs::for('admin.whyshopwithus.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.whyshopwithus.index');
    $trail->push(statictranslations('Edit Entry'), route('admin.whyshopwithus.edit', $id));
});

// --- Privacy Policy ---
Breadcrumbs::for('admin.privacy-policy.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Privacy Policy'), route('admin.privacy-policy.index'));
});
Breadcrumbs::for('admin.privacy-policy.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.privacy-policy.index');
    $trail->push(statictranslations('Create Entry'), route('admin.privacy-policy.create'));
});
Breadcrumbs::for('admin.privacy-policy.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.privacy-policy.index');
    $trail->push(statictranslations('Edit Entry'), route('admin.privacy-policy.edit', $id));
});

// --- Terms & Conditions ---
Breadcrumbs::for('admin.tncs.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Terms & Conditions'), route('admin.tncs.index'));
});
Breadcrumbs::for('admin.tncs.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.tncs.index');
    $trail->push(statictranslations('Create Entry'), route('admin.tncs.create'));
});
Breadcrumbs::for('admin.tncs.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.tncs.index');
    $trail->push(statictranslations('Edit Entry'), route('admin.tncs.edit', $id));
});

// --- Shipping & Returns ---
Breadcrumbs::for('admin.shipping-return.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Shipping & Returns'), route('admin.shipping-return.index'));
});
Breadcrumbs::for('admin.shipping-return.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.shipping-return.index');
    $trail->push(statictranslations('Create Entry'), route('admin.shipping-return.create'));
});
Breadcrumbs::for('admin.shipping-return.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.shipping-return.index');
    $trail->push(statictranslations('Edit Entry'), route('admin.shipping-return.edit', $id));
});

// --- FAQ Categories ---
Breadcrumbs::for('admin.faq-categories.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('FAQ Categories'), route('admin.faq-categories.index'));
});
Breadcrumbs::for('admin.faq-categories.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.faq-categories.index');
    $trail->push(statictranslations('Create FAQ Category'), route('admin.faq-categories.create'));
});
Breadcrumbs::for('admin.faq-categories.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.faq-categories.index');
    $trail->push(statictranslations('Edit FAQ Category'), route('admin.faq-categories.edit', $id));
});

// --- FAQ Questions ---
Breadcrumbs::for('admin.faqs.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('FAQs'), route('admin.faqs.index'));
});
Breadcrumbs::for('admin.faqs.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.faqs.index');
    $trail->push(statictranslations('Create FAQ'), route('admin.faqs.create'));
});
Breadcrumbs::for('admin.faqs.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.faqs.index');
    $trail->push(statictranslations('Edit FAQ'), route('admin.faqs.edit', $id));
});

// --- Orders ---
Breadcrumbs::for('admin.orders.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Orders'), route('admin.orders.index'));
});
Breadcrumbs::for('admin.orders.show', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.orders.index');
    $trail->push(statictranslations('View Order'), route('admin.orders.show', $id));
});

// --- Dispatch ---
Breadcrumbs::for('admin.dispatch.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Dispatch'), route('admin.dispatch.index'));
});
Breadcrumbs::for('admin.dispatch.create', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dispatch.index');
    $trail->push(statictranslations('Create Dispatch'), route('admin.dispatch.create'));
});

// --- Reviews ---
Breadcrumbs::for('admin.reviews.index', function (BreadcrumbTrail $trail) {
    $trail->parent('admin.dashboard');
    $trail->push(statictranslations('Reviews'), route('admin.reviews.index'));
});
Breadcrumbs::for('admin.reviews.show', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('admin.reviews.index');
    $trail->push(statictranslations('Review Details'), route('admin.reviews.show', $id));
});
