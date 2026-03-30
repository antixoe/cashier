<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ManagementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

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

// Management - Protected routes
Route::middleware('auth')->group(function () {
    Route::get('/management/users', [ManagementController::class, 'users'])->name('management.users');
    Route::post('/management/users', [ManagementController::class, 'userStore'])->name('management.users.store');
    Route::get('/management/users/{id}', [ManagementController::class, 'show'])->name('management.users.show');
    Route::put('/management/users/{id}', [ManagementController::class, 'update'])->name('management.users.update');
    Route::delete('/management/users/{id}', [ManagementController::class, 'destroy'])->name('management.users.destroy');
    Route::get('/management/roles', [ManagementController::class, 'roleIndex'])->name('management.roles');
    Route::post('/management/roles', [ManagementController::class, 'roleStore'])->name('management.roles.store');
    Route::delete('/management/roles/{id}', [ManagementController::class, 'roleDestroy'])->name('management.roles.destroy');
});

// Authentication routes (AJAX only)
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');
