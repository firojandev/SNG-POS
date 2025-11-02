<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\TaxController;
use App\Http\Controllers\Admin\VatController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\IncomeCategoryController;
use App\Http\Controllers\Admin\IncomeController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\PaymentToSupplierController;

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

Route::prefix('vat')->group(function () {
    Route::get('/', [VatController::class, 'index'])->name('vat.index');
    Route::get('/get-data', [VatController::class, 'getData'])->name('vat.getData');
    Route::post('/', [VatController::class, 'store'])->name('vat.store');
    Route::get('/{vat}/edit', [VatController::class, 'edit'])->name('vat.edit');
    Route::put('/{vat}', [VatController::class, 'update'])->name('vat.update');
    Route::delete('/{vat}', [VatController::class, 'destroy'])->name('vat.destroy');
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

Route::prefix('purchase')->group(function () {
    Route::get('/', [PurchaseController::class, 'index'])->name('purchase.index');
    Route::get('get-data', [PurchaseController::class, 'getData'])->name('purchase.api.getData');
    Route::get('create', [PurchaseController::class, 'create'])->name('purchase.create');
    Route::post('/', [PurchaseController::class, 'store'])->name('purchase.store');
    Route::get('{purchase:uuid}', [PurchaseController::class, 'show'])->name('purchase.show');
    Route::get('{purchase:uuid}/edit', [PurchaseController::class, 'edit'])->name('purchase.edit');
    Route::put('{purchase:uuid}', [PurchaseController::class, 'update'])->name('purchase.update');
    Route::delete('{purchase:uuid}', [PurchaseController::class, 'destroy'])->name('purchase.destroy');
    Route::post('{purchase:uuid}/make-payment', [PurchaseController::class, 'makePayment'])->name('purchase.makePayment');

    // API routes for AJAX requests
    Route::get('api/products', [PurchaseController::class, 'getProducts'])->name('purchase.api.products');
    Route::post('api/calculate-unit-total', [PurchaseController::class, 'calculateUnitTotal'])->name('purchase.api.calculate-unit-total');
});

// Invoice Routes
Route::prefix('invoice')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('get-data', [\App\Http\Controllers\Admin\InvoiceController::class, 'getData'])->name('invoice.api.getData');
    Route::get('create', [\App\Http\Controllers\Admin\InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('/', [\App\Http\Controllers\Admin\InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('{invoice:uuid}', [\App\Http\Controllers\Admin\InvoiceController::class, 'show'])->name('invoice.show');
    Route::get('{invoice:uuid}/edit', [\App\Http\Controllers\Admin\InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::put('{invoice:uuid}', [\App\Http\Controllers\Admin\InvoiceController::class, 'update'])->name('invoice.update');
    Route::delete('{invoice:uuid}', [\App\Http\Controllers\Admin\InvoiceController::class, 'destroy'])->name('invoice.destroy');

    // Sale Return and Cancel routes
    Route::post('{invoice:uuid}/return', [\App\Http\Controllers\Admin\InvoiceController::class, 'returnInvoice'])->name('invoice.return');
    Route::post('{invoice:uuid}/cancel', [\App\Http\Controllers\Admin\InvoiceController::class, 'cancelInvoice'])->name('invoice.cancel');

    // API routes for AJAX requests
    Route::get('api/products', [\App\Http\Controllers\Admin\InvoiceController::class, 'getProducts'])->name('invoice.api.products');
    Route::post('api/calculate-unit-total', [\App\Http\Controllers\Admin\InvoiceController::class, 'calculateUnitTotal'])->name('invoice.api.calculate-unit-total');
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

// Supplier Routes
Route::prefix('suppliers')->group(function () {
    Route::get('/', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/get-data', [SupplierController::class, 'getData'])->name('suppliers.getData');
    Route::post('/', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/{supplier}/view', [SupplierController::class, 'view'])->name('suppliers.view');
    Route::get('/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
});

// Customer Routes
Route::prefix('customers')->group(function () {
    Route::get('/', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/get-data', [CustomerController::class, 'getData'])->name('customers.getData');
    Route::post('/', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
});

// Expense Category Routes
Route::prefix('expense-category')->group(function () {
    Route::get('/', [ExpenseCategoryController::class, 'index'])->name('expense-category.index');
    Route::get('/get-data', [ExpenseCategoryController::class, 'getData'])->name('expense-category.getData');
    Route::post('/', [ExpenseCategoryController::class, 'store'])->name('expense-category.store');
    Route::get('/{expenseCategory}/edit', [ExpenseCategoryController::class, 'edit'])->name('expense-category.edit');
    Route::put('/{expenseCategory}', [ExpenseCategoryController::class, 'update'])->name('expense-category.update');
    Route::delete('/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])->name('expense-category.destroy');
});

// Expense Routes
Route::prefix('expenses')->group(function () {
    Route::get('/', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::get('/get-data', [ExpenseController::class, 'getData'])->name('expenses.getData');
    Route::post('/', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
});

// Income Category Routes
Route::prefix('income-category')->group(function () {
    Route::get('/', [IncomeCategoryController::class, 'index'])->name('income-category.index');
    Route::get('/get-data', [IncomeCategoryController::class, 'getData'])->name('income-category.getData');
    Route::post('/', [IncomeCategoryController::class, 'store'])->name('income-category.store');
    Route::get('/{incomeCategory}/edit', [IncomeCategoryController::class, 'edit'])->name('income-category.edit');
    Route::put('/{incomeCategory}', [IncomeCategoryController::class, 'update'])->name('income-category.update');
    Route::delete('/{incomeCategory}', [IncomeCategoryController::class, 'destroy'])->name('income-category.destroy');
});

// Income Routes
Route::prefix('incomes')->group(function () {
    Route::get('/', [IncomeController::class, 'index'])->name('incomes.index');
    Route::get('/get-data', [IncomeController::class, 'getData'])->name('incomes.getData');
    Route::post('/', [IncomeController::class, 'store'])->name('incomes.store');
    Route::get('/{income}/edit', [IncomeController::class, 'edit'])->name('incomes.edit');
    Route::put('/{income}', [IncomeController::class, 'update'])->name('incomes.update');
    Route::delete('/{income}', [IncomeController::class, 'destroy'])->name('incomes.destroy');
});

// Payment to Supplier Routes
Route::prefix('payment-to-supplier')->group(function () {
    Route::get('/', [PaymentToSupplierController::class, 'index'])->name('payment-to-supplier.index');
    Route::get('/get-data', [PaymentToSupplierController::class, 'getData'])->name('payment-to-supplier.getData');
    Route::get('/get-supplier-balance', [PaymentToSupplierController::class, 'getSupplierBalance'])->name('payment-to-supplier.getSupplierBalance');
    Route::post('/', [PaymentToSupplierController::class, 'store'])->name('payment-to-supplier.store');
    Route::get('/{paymentToSupplier}/edit', [PaymentToSupplierController::class, 'edit'])->name('payment-to-supplier.edit');
    Route::put('/{paymentToSupplier}', [PaymentToSupplierController::class, 'update'])->name('payment-to-supplier.update');
    Route::delete('/{paymentToSupplier}', [PaymentToSupplierController::class, 'destroy'])->name('payment-to-supplier.destroy');
});

// Profile Routes
Route::prefix('profile')->name('admin.profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/update', [ProfileController::class, 'updateProfile'])->name('update');
    Route::get('/change-password', [ProfileController::class, 'changePasswordForm'])->name('change-password');
    Route::put('/change-password', [ProfileController::class, 'changePassword'])->name('change-password.update');
});
