<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategorySubCategoryController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\SubCategoryController;

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

        // User Management routes (Admins)
        Route::prefix('user-management')->name('user-management.')->group(function () {
            Route::get('/', [AdminAuthController::class, 'index'])->name('index')->middleware('can:user management.read,admin');
            Route::get('/data', [AdminAuthController::class, 'data'])->name('data')->middleware('can:user management.read,admin');
            Route::get('/create', [AdminAuthController::class, 'create'])->name('create')->middleware('can:user management.create,admin');
            Route::post('/', [AdminAuthController::class, 'store'])->name('store')->middleware('can:user management.create,admin');
            Route::get('/{id}/edit', [AdminAuthController::class, 'edit'])->name('edit')->middleware('can:user management.update,admin');
            Route::put('/{id}', [AdminAuthController::class, 'update'])->name('update')->middleware('can:user management.update,admin');
            Route::patch('/{id}/toggle-status', [AdminAuthController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:user management.update,admin');
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
            Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit')->middleware('can:role.update,admin');
            Route::put('/{id}', [RoleController::class, 'update'])->name('update')->middleware('can:role.update,admin');
            Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy')->middleware('can:role.delete,admin');
        });

        // Hero-Slider routes
        Route::prefix('hero-slider')->name('hero-slider.')->middleware('can:slider.read,admin')->group(function () {
            Route::get('/', [SliderController::class, 'index'])->name('index'); // permission: slider.read
            Route::get('/data', [SliderController::class, 'data'])->name('data');
            Route::get('/show', [SliderController::class, 'show'])->name('show');
            Route::get('/create', [SliderController::class, 'create'])->name('create')->middleware('can:slider.create,admin');
            Route::post('/', [SliderController::class, 'store'])->name('store')->middleware('can:slider.create,admin');
            Route::get('/edit', [SliderController::class, 'edit'])->name('edit')->middleware('can:slider.update,admin');
            Route::put('/update', [SliderController::class, 'update'])->name('update')->middleware('can:slider.update,admin');
            Route::delete('/destroy', [SliderController::class, 'destroy'])->name('destroy')->middleware('can:slider.delete,admin');
            Route::patch('/toggle-status', [SliderController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:slider.update,admin');
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
                Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit')->middleware('can:category.update,admin');
                Route::put('/{id}', [CategoryController::class, 'update'])->name('update')->middleware('can:category.update,admin');
                Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy')->middleware('can:category.delete,admin');
                Route::patch('/{id}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:category.update,admin');
            });

            // --- SubCategories Routes ---
            Route::prefix('subcategories')->name('subcategories.')->middleware('can:subcategory.read,admin')->group(function () {
                Route::get('/data', [SubCategoryController::class, 'data'])->name('data');
                Route::get('/create', [SubCategoryController::class, 'create'])->name('create')->middleware('can:subcategory.create,admin');
                Route::post('/', [SubCategoryController::class, 'store'])->name('store')->middleware('can:subcategory.create,admin');
                Route::get('/{id}/edit', [SubCategoryController::class, 'edit'])->name('edit')->middleware('can:subcategory.update,admin');
                Route::put('/{id}', [SubCategoryController::class, 'update'])->name('update')->middleware('can:subcategory.update,admin');
                Route::delete('/{id}', [SubCategoryController::class, 'destroy'])->name('destroy')->middleware('can:subcategory.delete,admin');
                Route::patch('/{id}/toggle-status', [SubCategoryController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:subcategory.update,admin');
            });
        });
    });
});
