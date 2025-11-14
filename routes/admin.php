<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ProfileController;

Route::middleware(['web'])->prefix('admin')->name('admin.')->group(function () {
    // Guest routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login']);
        Route::get('/forgot-password', function () {
            return view('admin.auth.forgot-password');
        })->name('forgot-password');
    });

    // Authenticated routes
    Route::middleware('admin.auth')->group(function () {
        Route::get('/', function () {
            return view('admin.pages.home');
        })->name('dashboard');

        Route::prefix('user-management')->name('user-management.')->group(function () {
            Route::get('/', function () {
                return view('admin.pages.userManagement.index');
            })->name('index');
            Route::get('/create', function () {
                return view('admin.pages.userManagement.create');
            })->name('create');
            Route::get('/role-permission', function () {
                return view('admin.pages.userManagement.role-permission');
            })->name('role-permisssion');
        });

        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/update-profile', [ProfileController::class, 'updateProfile'])->name('updateProfile');

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');


    });
});
