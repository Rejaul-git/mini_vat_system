<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Profile
Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
Route::put('/profile/update', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

// Dashboard
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

// Products
Route::resource('products', App\Http\Controllers\ProductController::class);
Route::get('/api/products', [App\Http\Controllers\ProductController::class, 'getProducts'])->name('products.api');

// Purchases
Route::resource('purchases', App\Http\Controllers\PurchaseController::class);

// Sales
Route::resource('sales', App\Http\Controllers\SaleController::class);

// Returns
Route::get('/returns/create', [App\Http\Controllers\ReturnController::class, 'create'])->name('returns.create');
Route::post('/returns', [App\Http\Controllers\ReturnController::class, 'store'])->name('returns.store');
Route::delete('/returns/{return}', [App\Http\Controllers\ReturnController::class, 'destroy'])->name('returns.destroy');
Route::get('/api/sales/{saleId}/items', [App\Http\Controllers\ReturnController::class, 'getSaleItems'])->name('returns.getSaleItems');

// Reports
Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
Route::get('/reports/export', [App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
