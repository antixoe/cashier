<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [PosController::class, 'index'])->name('pos.index');
    Route::post('/add-to-cart', [PosController::class, 'addToCart'])->name('pos.addToCart');
    Route::post('/remove-from-cart', [PosController::class, 'removeFromCart'])->name('pos.removeFromCart');
    Route::post('/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
});

Route::get('/pos', function () {
    return redirect()->route('pos.index');
});
Route::post('/remove-from-cart', [PosController::class, 'removeFromCart'])->name('pos.removeFromCart');
Route::post('/checkout', [PosController::class, 'checkout'])->name('pos.checkout');

// Management - Admin-only routes
Route::middleware('auth', 'is_admin')->group(function () {
    Route::get('/management/users', [ManagementController::class, 'users'])->name('management.users');
    Route::post('/management/users', [ManagementController::class, 'userStore'])->name('management.users.store');
    Route::get('/management/users/{id}', [ManagementController::class, 'show'])->name('management.users.show');
    Route::put('/management/users/{id}', [ManagementController::class, 'update'])->name('management.users.update');
    Route::delete('/management/users/{id}', [ManagementController::class, 'destroy'])->name('management.users.destroy');
    Route::get('/management/roles', [ManagementController::class, 'roleIndex'])->name('management.roles');
    Route::post('/management/roles', [ManagementController::class, 'roleStore'])->name('management.roles.store');
    Route::get('/management/roles/{id}', [ManagementController::class, 'roleShow'])->name('management.roles.show');
    Route::put('/management/roles/{id}', [ManagementController::class, 'roleUpdate'])->name('management.roles.update');
    Route::delete('/management/roles/{id}', [ManagementController::class, 'roleDestroy'])->name('management.roles.destroy');

    // Products Management
    Route::get('/management/products', [ProductController::class, 'index'])->name('management.products.index');
    Route::get('/management/products/create', [ProductController::class, 'create'])->name('management.products.create');
    Route::post('/management/products', [ProductController::class, 'store'])->name('management.products.store');
    Route::get('/management/products/{id}', [ProductController::class, 'show'])->name('management.products.show');
    Route::get('/management/products/{id}/edit', [ProductController::class, 'edit'])->name('management.products.edit');
    Route::put('/management/products/{id}', [ProductController::class, 'update'])->name('management.products.update');
    Route::delete('/management/products/{id}', [ProductController::class, 'destroy'])->name('management.products.destroy');
    Route::post('/management/products/{id}/restore', [ProductController::class, 'restore'])->name('management.products.restore');
    Route::delete('/management/products/{id}/force', [ProductController::class, 'forceDelete'])->name('management.products.forceDelete');

    // Categories Management
    Route::get('/management/categories', [CategoryController::class, 'index'])->name('management.categories');
    Route::post('/management/categories', [CategoryController::class, 'store'])->name('management.categories.store');
    Route::get('/management/categories/{id}', [CategoryController::class, 'show'])->name('management.categories.show');
    Route::put('/management/categories/{id}', [CategoryController::class, 'update'])->name('management.categories.update');
    Route::delete('/management/categories/{id}', [CategoryController::class, 'destroy'])->name('management.categories.destroy');

    // Activity Logs
    Route::get('/management/activity-logs', [ActivityLogController::class, 'index'])->name('management.activity-logs');
    Route::get('/management/activity-logs/user/{userId}', [ActivityLogController::class, 'userLogs'])->name('management.user-activity-logs');
    Route::get('/management/activity-logs/export', [ActivityLogController::class, 'export'])->name('management.activity-logs.export');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export-sales', [ReportController::class, 'exportSalesReport'])->name('reports.export-sales');
    Route::get('/reports/export-products', [ReportController::class, 'exportProductReport'])->name('reports.export-products');

    // Settings & Profile
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.updateProfile');
    Route::post('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.updatePassword');
    Route::get('/settings/user', [SettingsController::class, 'getUser'])->name('settings.getUser');
});

// Authentication routes
Route::get('/login', function () {
    return redirect()->route('home', ['openLogin' => 1]);
})->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Registration routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
