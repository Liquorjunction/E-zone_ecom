<?php

use App\Http\Controllers\Admin\AboutUsController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ShippingAndReturnController;
use App\Http\Controllers\Admin\ShowroomController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SubCategoryController;
use App\Http\Controllers\Admin\TncController;
use App\Http\Controllers\Admin\TranslationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
    });

    // Authenticated routes
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', [AdminAuthController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // Translation Routes
        Route::post('/translate-content', [TranslationController::class, 'translate'])->name('translate.content');
        Route::post('/translate-batch', [TranslationController::class, 'translateBatch'])->name('translate.batch');

        // User Management routes (Admins)
        Route::prefix('user-management')->name('user-management.')->group(function () {
            Route::get('/', [AdminAuthController::class, 'index'])->name('index')->middleware('can:user management.read,admin');
            Route::get('/data', [AdminAuthController::class, 'data'])->name('data')->middleware('can:user management.read,admin');
            Route::get('/create', [AdminAuthController::class, 'create'])->name('create')->middleware('can:user management.create,admin');
            Route::post('/', [AdminAuthController::class, 'store'])->name('store')->middleware('can:user management.create,admin');
            Route::get('/edit', [AdminAuthController::class, 'edit'])->name('edit')->middleware('can:user management.update,admin');
            Route::put('/update', [AdminAuthController::class, 'update'])->name('update')->middleware('can:user management.update,admin');
            Route::patch('/toggle-status', [AdminAuthController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:user management.update,admin');
        });

        // Profile routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
        });

        // Roles & Permissions routes
        Route::prefix('roles-permissions')->name('roles-permissions.')->middleware('can:role.read,admin')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index'); // permission: role.read
            Route::get('/data', [RoleController::class, 'data'])->name('data');
            Route::get('/create', [RoleController::class, 'create'])->name('create')->middleware('can:role.create,admin');
            Route::post('/', [RoleController::class, 'store'])->name('store')->middleware('can:role.create,admin');
            Route::get('/edit', [RoleController::class, 'edit'])->name('edit')->middleware('can:role.update,admin');
            Route::put('/update', [RoleController::class, 'update'])->name('update')->middleware('can:role.update,admin');
            Route::delete('/destroy', [RoleController::class, 'destroy'])->name('destroy')->middleware('can:role.delete,admin');
        });

        // Hero-Slider routes
        Route::prefix('hero-slider')->name('hero-slider.')->middleware('can:slider.read,admin')->group(function () {
            Route::get('/', [SliderController::class, 'index'])->name('index'); // permission: slider.read
            Route::get('/data', [SliderController::class, 'data'])->name('data');
            Route::get('/create', [SliderController::class, 'create'])->name('create')->middleware('can:slider.create,admin');
            Route::post('/', [SliderController::class, 'store'])->name('store')->middleware('can:slider.create,admin');
            Route::get('/edit', [SliderController::class, 'edit'])->name('edit')->middleware('can:slider.update,admin');
            Route::put('/update', [SliderController::class, 'update'])->name('update')->middleware('can:slider.update,admin');
            Route::delete('/destroy', [SliderController::class, 'destroy'])->name('destroy')->middleware('can:slider.delete,admin');
            Route::patch('/toggle-status', [SliderController::class, 'updateStatus'])->name('toggleStatus')->middleware('can:slider.update,admin');
        });

        // Categories & Subcategories routes
        Route::prefix('category-subcategory')->name('category-subcategory.')->group(function () {

            // Separate Index Pages
            Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
            Route::get('/subcategories', [SubCategoryController::class, 'index'])->name('subcategories.index');

            // --- Categories Routes ---
            Route::prefix('categories')->name('categories.')->middleware('can:category.read,admin')->group(function () {
                Route::get('/data', [CategoryController::class, 'data'])->name('data');
                Route::get('/create', [CategoryController::class, 'create'])->name('create')->middleware('can:category.create,admin');
                Route::post('/', [CategoryController::class, 'store'])->name('store')->middleware('can:category.create,admin');
                Route::get('/edit', [CategoryController::class, 'edit'])->name('edit')->middleware('can:category.update,admin');
                Route::put('/update', [CategoryController::class, 'update'])->name('update')->middleware('can:category.update,admin');
                Route::delete('/destroy', [CategoryController::class, 'destroy'])->name('destroy')->middleware('can:category.delete,admin');
                Route::patch('/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:category.update,admin');
            });

            // --- SubCategories Routes ---
            Route::prefix('subcategories')->name('subcategories.')->middleware('can:subcategory.read,admin')->group(function () {
                Route::get('/data', [SubCategoryController::class, 'data'])->name('data');
                Route::get('/create', [SubCategoryController::class, 'create'])->name('create')->middleware('can:subcategory.create,admin');
                Route::post('/', [SubCategoryController::class, 'store'])->name('store')->middleware('can:subcategory.create,admin');
                Route::get('/edit', [SubCategoryController::class, 'edit'])->name('edit')->middleware('can:subcategory.update,admin');
                Route::put('/update', [SubCategoryController::class, 'update'])->name('update')->middleware('can:subcategory.update,admin');
                Route::delete('/destroy', [SubCategoryController::class, 'destroy'])->name('destroy')->middleware('can:subcategory.delete,admin');
                Route::patch('/toggle-status', [SubCategoryController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:subcategory.update,admin');
            });
        });

        // Card Routes
        Route::prefix('cards')->name('cards.')->middleware('can:card.read,admin')->group(function () {
            Route::get('/', [CardController::class, 'index'])->name('index'); // permission: card.read
            Route::get('/data', [CardController::class, 'data'])->name('data');
            Route::get('/create', [CardController::class, 'create'])->name('create')->middleware('can:card.create,admin');
            Route::post('/', [CardController::class, 'store'])->name('store')->middleware('can:card.create,admin');
            Route::get('/edit', [CardController::class, 'edit'])->name('edit')->middleware('can:card.update,admin');
            Route::put('/update', [CardController::class, 'update'])->name('update')->middleware('can:card.update,admin');
            Route::delete('/destroy', [CardController::class, 'destroy'])->name('destroy')->middleware('can:card.delete,admin');
            Route::patch('/toggle-status', [CardController::class, 'status'])->name('toggleStatus')->middleware('can:card.update,admin');
        });

        // Privacy Policy Routes
        Route::prefix('privacy-policy')->name('privacy-policy.')->middleware('can:privacy_policies.read,admin')->group(function () {
            Route::get('/', [PrivacyPolicyController::class, 'index'])->name('index');
            Route::get('/data', [PrivacyPolicyController::class, 'data'])->name('data');
            Route::get('/create', [PrivacyPolicyController::class, 'create'])->name('create')->middleware('can:privacy_policies.create,admin');
            Route::post('/', [PrivacyPolicyController::class, 'store'])->name('store')->middleware('can:privacy_policies.create,admin');
            Route::get('/edit', [PrivacyPolicyController::class, 'edit'])->name('edit')->middleware('can:privacy_policies.update,admin');
            Route::put('/update', [PrivacyPolicyController::class, 'update'])->name('update')->middleware('can:privacy_policies.update,admin');
            Route::delete('/destroy', [PrivacyPolicyController::class, 'destroy'])->name('destroy')->middleware('can:privacy_policies.delete,admin');
        });

        // FAQ Category Routes
        Route::prefix('faq-categories')->name('faq-categories.')->middleware('can:faqs.read,admin')->group(function () {
            Route::get('/', [FaqCategoryController::class, 'index'])->name('index');
            Route::get('/data', [FaqCategoryController::class, 'data'])->name('data');
            Route::get('/create', [FaqCategoryController::class, 'create'])->name('create')->middleware('can:faqs.create,admin');
            Route::post('/', [FaqCategoryController::class, 'store'])->name('store')->middleware('can:faqs.create,admin');
            Route::get('/edit', [FaqCategoryController::class, 'edit'])->name('edit')->middleware('can:faqs.update,admin');
            Route::put('/update', [FaqCategoryController::class, 'update'])->name('update')->middleware('can:faqs.update,admin');
            Route::delete('/destroy', [FaqCategoryController::class, 'destroy'])->name('destroy')->middleware('can:faqs.delete,admin');
            Route::patch('/toggle-status', [FaqCategoryController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:faqs.update,admin');
        });

        // FAQ Routes
        Route::prefix('faqs')->name('faqs.')->middleware('can:faqs.read,admin')->group(function () {
            Route::get('/', [FaqController::class, 'index'])->name('index');
            Route::get('/data', [FaqController::class, 'data'])->name('data');
            Route::get('/create', [FaqController::class, 'create'])->name('create')->middleware('can:faqs.create,admin');
            Route::post('/', [FaqController::class, 'store'])->name('store')->middleware('can:faqs.create,admin');
            Route::get('/edit', [FaqController::class, 'edit'])->name('edit')->middleware('can:faqs.update,admin');
            Route::put('/update', [FaqController::class, 'update'])->name('update')->middleware('can:faqs.update,admin');
            Route::delete('/destroy', [FaqController::class, 'destroy'])->name('destroy')->middleware('can:faqs.delete,admin');
            Route::patch('/toggle-status', [FaqController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:faqs.update,admin');
        });

        // Terms and Conditions Routes
        Route::prefix('tncs')->name('tncs.')->middleware('can:tnc.read,admin')->group(function () {
            Route::get('/', [TncController::class, 'index'])->name('index');
            Route::get('/data', [TncController::class, 'data'])->name('data');
            Route::get('/create', [TncController::class, 'create'])->name('create')->middleware('can:tnc.create,admin');
            Route::post('/', [TncController::class, 'store'])->name('store')->middleware('can:tnc.create,admin');
            Route::get('/edit', [TncController::class, 'edit'])->name('edit')->middleware('can:tnc.update,admin');
            Route::put('/update', [TncController::class, 'update'])->name('update')->middleware('can:tnc.update,admin');
            Route::delete('/destroy', [TncController::class, 'destroy'])->name('destroy')->middleware('can:tnc.delete,admin');
            Route::patch('/toggle-status', [TncController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:tnc.update,admin');
        });

        // Shipping & Return Routes
        Route::group(['prefix' => 'shipping-return', 'as' => 'shipping-return.'], function () {
            Route::get('/', [ShippingAndReturnController::class, 'index'])->name('index')->can('shipping_return.read');
            Route::get('/create', [ShippingAndReturnController::class, 'create'])->name('create')->can('shipping_return.create');
            Route::post('/store', [ShippingAndReturnController::class, 'store'])->name('store')->can('shipping_return.create');
            Route::get('/{id}/edit', [ShippingAndReturnController::class, 'edit'])->name('edit')->can('shipping_return.update');
            Route::put('/update', [ShippingAndReturnController::class, 'update'])->name('update')->can('shipping_return.update');
            Route::delete('/destroy', [ShippingAndReturnController::class, 'destroy'])->name('destroy')->can('shipping_return.delete');
            Route::get('/data', [ShippingAndReturnController::class, 'data'])->name('data')->can('shipping_return.read');
            Route::patch('/status/{id}', [ShippingAndReturnController::class, 'status'])->name('toggleStatus')->can('shipping_return.update');
        });

        // About Us Routes
        Route::prefix('about-us')->name('about_us.')->middleware('can:about_us.read,admin')->group(function () {
            Route::get('/', [AboutUsController::class, 'index'])->name('index'); // permission: about_us.read
            Route::get('/data', [AboutUsController::class, 'data'])->name('data');
            Route::get('/create', [AboutUsController::class, 'create'])->name('create')->middleware('can:about_us.create,admin');
            Route::post('/', [AboutUsController::class, 'store'])->name('store')->middleware('can:about_us.create,admin');
            Route::get('/edit', [AboutUsController::class, 'edit'])->name('edit')->middleware('can:about_us.update,admin');
            Route::put('/update', [AboutUsController::class, 'update'])->name('update')->middleware('can:about_us.update,admin');
            Route::delete('/destroy', [AboutUsController::class, 'destroy'])->name('destroy')->middleware('can:about_us.delete,admin');
            Route::patch('/toggle-status', [AboutUsController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:about_us.update,admin');
        });

        // Showroom Routes
        Route::prefix('showrooms')->name('showrooms.')->middleware('can:showrooms.read,admin')->group(function () {
            Route::get('/', [ShowroomController::class, 'index'])->name('index'); // permission: showrooms.read
            Route::get('/data', [ShowroomController::class, 'data'])->name('data');
            Route::get('/show', [ShowroomController::class, 'show'])->name('show')->middleware('can:showrooms.read,admin');
            Route::get('/create', [ShowroomController::class, 'create'])->name('create')->middleware('can:showrooms.create,admin');
            Route::post('/', [ShowroomController::class, 'store'])->name('store')->middleware('can:showrooms.create,admin');
            Route::get('/edit', [ShowroomController::class, 'edit'])->name('edit')->middleware('can:showrooms.update,admin');
            Route::put('/update', [ShowroomController::class, 'update'])->name('update')->middleware('can:showrooms.update,admin');
            Route::delete('/destroy', [ShowroomController::class, 'destroy'])->name('destroy')->middleware('can:showrooms.delete,admin');
            Route::patch('/toggle-status', [ShowroomController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:showrooms.update,admin');
        });

        // Brands Routes
        Route::prefix('brands')->name('brands.')->middleware('can:brands.read,admin')->group(function () {
            Route::get('/', [BrandController::class, 'index'])->name('index'); // permission: brands.read
            Route::get('/data', [BrandController::class, 'data'])->name('data');
            Route::get('/create', [BrandController::class, 'create'])->name('create')->middleware('can:brands.create,admin');
            Route::post('/', [BrandController::class, 'store'])->name('store')->middleware('can:brands.create,admin');
            Route::get('/edit', [BrandController::class, 'edit'])->name('edit')->middleware('can:brands.update,admin');
            Route::put('/update', [BrandController::class, 'update'])->name('update')->middleware('can:brands.update,admin');
            Route::delete('/destroy', [BrandController::class, 'destroy'])->name('destroy')->middleware('can:brands.delete,admin');
            Route::patch('/toggle-status', [BrandController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:brands.update,admin');
        });

        // Product Routes
        Route::prefix('products')->name('products.')->middleware('can:products.read,admin')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index'); // permission: products.read
            Route::get('/data', [ProductController::class, 'data'])->name('data');
            Route::get('/create', [ProductController::class, 'create'])->name('create')->middleware('can:products.create,admin');
            Route::post('/', [ProductController::class, 'store'])->name('store')->middleware('can:products.create,admin');
            Route::get('/edit', [ProductController::class, 'edit'])->name('edit')->middleware('can:products.update,admin');
            Route::put('/update', [ProductController::class, 'update'])->name('update')->middleware('can:products.update,admin');
            Route::delete('/destroy', [ProductController::class, 'destroy'])->name('destroy')->middleware('can:products.delete,admin');
            Route::patch('/toggle-status', [ProductController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:products.update,admin');
        });
    });
});
