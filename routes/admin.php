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
        Route::get('/', [AdminAuthController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

        // User Management routes
        Route::prefix('user-management')->name('user-management.')->group(function () {
            Route::get('/', [AdminAuthController::class, 'index'])->name('index');
            Route::get('/create', [AdminAuthController::class, 'create'])->name('create');
            Route::post('/', [AdminAuthController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [AdminAuthController::class, 'edit'])->name('edit');
            Route::put('/{id}', [AdminAuthController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status', [AdminAuthController::class, 'toggleStatus'])->name('toggleStatus');
        });

        // Profile routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'index'])->name('index');
            Route::put('/', [ProfileController::class, 'update'])->name('update');
        });

        // Roles & Permissions routes
        Route::prefix('roles-permissions')->name('roles-permissions.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/', [RoleController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{id}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{id}', [RoleController::class, 'destroy'])->name('destroy');
        });
    });
});
