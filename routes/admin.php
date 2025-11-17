<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RoleController;

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

        // User Management routes
        Route::prefix('user-management')->name('user-management.')->group(function () {

            // FIX: Protected 'create admin' routes
            Route::get('/', [AdminAuthController::class, 'index'])->name('index')->middleware('can:create admins,admin');
            Route::get('/create', [AdminAuthController::class, 'create'])->name('create')->middleware('can:create admins,admin');
            Route::post('/', [AdminAuthController::class, 'store'])->name('store')->middleware('can:create admins,admin');

            // FIX: Protected 'edit admin' routes
            Route::get('/{id}/edit', [AdminAuthController::class, 'edit'])->name('edit')->middleware('can:edit any admin,admin');
            Route::put('/{id}', [AdminAuthController::class, 'update'])->name('update')->middleware('can:edit any admin,admin');

            // FIX: Protected 'toggle status' route
            Route::patch('/{id}/toggle-status', [AdminAuthController::class, 'toggleStatus'])->name('toggleStatus')->middleware('can:toggle admin status,admin');
        });

        // Profile routes
        // (No 'can' middleware needed, as this is for the logged-in user)
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
        });

        // Roles & Permissions routes
        // FIX: Protected this entire group. Only admins who can 'create roles' can access any of these.
        Route::prefix('roles-permissions')->name('roles-permissions.')->middleware('can:create roles,admin')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{id}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
        });
    });
});
