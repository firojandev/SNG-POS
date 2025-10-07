<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\ProfileController;

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

Route::prefix('store')->group(function () {
    Route::get('/', [StoreController::class, 'index'])->name('store.index');
    Route::get('/get-data', [StoreController::class, 'getData'])->name('store.getData');
    Route::post('/', [StoreController::class, 'store'])->name('store.store');
    Route::get('/{store}/edit', [StoreController::class, 'edit'])->name('store.edit');
    Route::put('/{store}', [StoreController::class, 'update'])->name('store.update');
    Route::delete('/{store}', [StoreController::class, 'destroy'])->name('store.destroy');
});

Route::prefix('staff')->group(function () {
    Route::get('/', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/get-data', [StaffController::class, 'getData'])->name('staff.getData');
    Route::get('/get-stores', [StaffController::class, 'getStores'])->name('staff.getStores');
    Route::post('/', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
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
    Route::get('/barcode/download', [ProductController::class, 'downloadBarcode'])->name('barcode.download');
});

// Settings Routes
Route::prefix('settings')->name('admin.settings.')->group(function () {
    Route::get('/', [SettingsController::class, 'index'])->name('index');
    Route::put('/', [SettingsController::class, 'update'])->name('update');
});

// Currency Routes
Route::prefix('currency')->name('admin.currency.')->group(function () {
    Route::get('/', [CurrencyController::class, 'index'])->name('index');
    Route::post('/', [CurrencyController::class, 'store'])->name('store');
    Route::delete('/{currency}', [CurrencyController::class, 'destroy'])->name('destroy');
    Route::post('/set-currency', [CurrencyController::class, 'setCurrency'])->name('set');
});

// Profile Routes
Route::prefix('profile')->name('admin.profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/update', [ProfileController::class, 'updateProfile'])->name('update');
    Route::get('/change-password', [ProfileController::class, 'changePasswordForm'])->name('change-password');
    Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('change-password.update');
});
