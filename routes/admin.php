<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UnitController;

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
