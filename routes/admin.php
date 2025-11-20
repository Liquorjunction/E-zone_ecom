<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CategorySubCategoryController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SliderController;

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
        Route::prefix('category-subcategory')->name('category-subcategory.')->middleware('can:category-subcategory.read,admin')->group(function () {
            Route::get('/', [CategorySubCategoryController::class, 'index'])->name('index');
            Route::get('/data-category', [CategorySubCategoryController::class, 'dataCategory'])->name('dataCategory');
            Route::get('/data-subcategory', [CategorySubCategoryController::class, 'dataSubCategory'])->name('dataSubCategory');
            Route::get('/create', [CategorySubCategoryController::class, 'create'])->name('create')->middleware('can:category-subcategory.create,admin');
            Route::post('/category', [CategorySubCategoryController::class, 'storeCategory'])->name('storeCategory')->middleware('can:category-subcategory.create,admin');
            Route::post('/subcategory', [CategorySubCategoryController::class, 'storeSubCategory'])->name('storeSubCategory')->middleware('can:category-subcategory.create,admin');
            Route::get('/edit', [CategorySubCategoryController::class, 'edit'])->name('edit')->middleware('can:category-subcategory.update,admin');
            Route::put('/update', [CategorySubCategoryController::class, 'update'])->name('update')->middleware('can:category-subcategory.update,admin');
            Route::delete('/destroy', [CategorySubCategoryController::class, 'destroy'])->name('destroy')->middleware('can:category-subcategory.delete,admin');
            Route::patch('/{type}/{id}/toggle-status', [CategorySubCategoryController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:category-subcategory.update,admin');
        });
    });
});
