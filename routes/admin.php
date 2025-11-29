<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\PrivacyPolicyController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SubCategoryController;
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
    });
});
