<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;

Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

Route::prefix('category')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('category.index');
    Route::get('/get-data', [CategoryController::class, 'getData'])->name('category.getData');
    Route::post('/', [CategoryController::class, 'store'])->name('category.store');
    Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
});
