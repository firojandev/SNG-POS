<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\ProductController;

Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/get-data', [CategoryController::class, 'getData'])->name('category.getData');
    Route::post('/', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
});

Route::prefix('unit')->group(function () {
    Route::get('/', [UnitController::class, 'index'])->name('unit.index');
    Route::get('/get-data', [UnitController::class, 'getData'])->name('unit.getData');
    Route::post('/', [UnitController::class, 'store'])->name('unit.store');
    Route::get('/{unit}/edit', [UnitController::class, 'edit'])->name('unit.edit');
    Route::put('/{unit}', [UnitController::class, 'update'])->name('unit.update');
    Route::delete('/{unit}', [UnitController::class, 'destroy'])->name('unit.destroy');
});

Route::prefix('tax')->group(function () {
    Route::get('/', [TaxController::class, 'index'])->name('tax.index');
    Route::get('/get-data', [TaxController::class, 'getData'])->name('tax.getData');
    Route::post('/', [TaxController::class, 'store'])->name('tax.store');
    Route::get('/{tax}/edit', [TaxController::class, 'edit'])->name('tax.edit');
    Route::put('/{tax}', [TaxController::class, 'update'])->name('tax.update');
    Route::delete('/{tax}', [TaxController::class, 'destroy'])->name('tax.destroy');
});

Route::prefix('products')->name('admin.products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/create', [ProductController::class, 'create'])->name('create');
    Route::post('/', [ProductController::class, 'store'])->name('store');
    Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
    Route::put('/{product}', [ProductController::class, 'update'])->name('update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
    Route::get('/export/csv', [ProductController::class, 'export'])->name('export');
    Route::get('/import/form', [ProductController::class, 'importForm'])->name('import.form');
    Route::post('/import/csv', [ProductController::class, 'import'])->name('import');
});
