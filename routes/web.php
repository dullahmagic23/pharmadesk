<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BillPaymentController;
use App\Http\Controllers\CashierDashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LicenseCheckController;
use App\Http\Controllers\MedicineCategoryController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockHistoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleExportController;
use App\Http\Controllers\SalePaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StockConversionController;
use App\Http\Controllers\StockReportController;
use App\Http\Controllers\RoleController;

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/license', [LicenseCheckController::class, 'index'])->name('license.index');
Route::get('/license-check', [LicenseCheckController::class, 'index'])->name('license.check');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth', 'verified', 'check.license','log.activity']], function (){

    // Default dashboard
    Route::group(['middleware' => 'role:admin|cashier|pharmacist|doctor'], function () {
        Route::get('/', fn () => Inertia::render('Dashboard'))->name('home');
        Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
    });

    // Dashboards by role
    Route::get('/cashier/dashboard', [CashierDashboardController::class, 'index'])
        ->middleware('role:cashier')->name('cashier.dashboard');

    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->middleware('check.role:admin')->name('admin.dashboard');

    Route::get('/pharmacist/dashboard', fn () => Inertia::render('Pharmacist/Dashboard'))
        ->name('pharmacist.dashboard');

    Route::get('/doctor/dashboard', fn () => Inertia::render('Doctor/Dashboard'))
        ->name('doctor.dashboard');

    // General resources
    Route::resource('receipts', ReceiptController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::resource('patients', PatientController::class);
    Route::resource('products', ProductController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('vendors', VendorController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('equipments', EquipmentController::class);
    Route::resource('bill-payments', BillPaymentController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('sales', SaleController::class);

    // Sales extras
    Route::get('/sales/{sale}/receipt', [SaleController::class, 'printReceipt'])->name('sales.receipt');
    Route::get('/sales/{sale}/add-payments', [SalePaymentController::class, 'addPayments'])->name('sales.add-payments');
    Route::post('/sales/{sale}/payments', [SalePaymentController::class, 'store'])->name('sales.payments.store');
    Route::get('/sales/export/pdf', [SaleExportController::class, 'exportPdf'])->name('sales.export.pdf');
    Route::get('/sales/export/excel', [SaleExportController::class, 'exportExcel'])->name('sales.export.excel');

    // Medicines & categories
    Route::resource('medicine-categories', MedicineCategoryController::class)->middleware('check.role:admin|cashier');
    Route::resource('medicines', MedicineController::class);

    // Stock management (admin only)
    Route::middleware('check.role:admin')->group(function () {
        Route::resource('stocks', StockController::class);
        Route::resource('stock-histories', StockHistoryController::class);
        Route::patch('/stocks/{stock}/expire', [StockController::class, 'markExpired'])->name('stocks.expire');

        Route::get('stock-conversion', [StockConversionController::class, 'create'])->name('stock-conversion');
        Route::post('stock-conversion', [StockConversionController::class, 'store'])->name('stock_conversion.store');

        // Purchases & invoices
        Route::resource('purchases', PurchaseController::class);
        Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
        Route::resource('invoices', InvoiceController::class);
        Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

        // Bills, doctors, prescriptions
        Route::resource('bills', BillController::class);
        Route::resource('doctors', DoctorController::class);
        Route::resource('prescriptions', PrescriptionController::class);
        Route::get('/prescriptions/{prescription}/download', [PrescriptionController::class, 'download'])->name('prescriptions.download');

        // Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', fn () => Inertia::render('Reports/Index'))->name('index');
            Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
            Route::get('/purchases', [ReportController::class, 'purchaseReport'])->name('purchases');
            Route::get('/purchases/pdf', [PurchaseReportController::class, 'exportPdf'])->name('purchases.pdf');
            Route::get('/stock', [StockReportController::class, 'index'])->name('stocks');
        });

        // User management
        Route::get('/users/activity-logs', [UserController::class, 'userActivity'])->name('users.activity');
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
    });

    // Settings file (already grouped under auth)
    require __DIR__ . '/settings.php';
});
