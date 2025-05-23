<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BillPaymentController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MedicineCategoryController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
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
use App\Http\Controllers\StockReportController;

require __DIR__ . '/auth.php';

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('home')->middleware('auth');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role'])->name('dashboard');

require __DIR__ . '/settings.php';


Route::middleware(['auth', 'log.activity'])->group(function () {
    Route::middleware(['auth', 'verified'])->group(function () {
        Route::resource('medicines', MedicineController::class);
    });
    Route::resource('medicine-categories', MedicineCategoryController::class);
    Route::resource('products', ProductController::class)->middleware('auth');
    Route::resource('stocks', StockController::class)->middleware('auth');
    Route::resource('stock-histories', StockHistoryController::class)->middleware('auth');
    Route::resource('patients', PatientController::class)->middleware('auth');
    Route::resource('doctors', DoctorController::class)->middleware('auth');
    Route::resource('prescriptions', PrescriptionController::class)->middleware('auth');
    Route::get('/prescriptions/{prescription}/download', [PrescriptionController::class, 'download'])->name('prescriptions.download');
    Route::resource('invoices', InvoiceController::class)->middleware('auth');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');
    Route::resource('payments', PaymentController::class)->middleware('auth');
    Route::resource('vendors', VendorController::class)->middleware('auth');
    Route::resource('purchases', PurchaseController::class)->middleware('auth');
    Route::get('/purchases/{purchase}/print', [PurchaseController::class, 'print'])->name('purchases.print');
    Route::resource('suppliers', SupplierController::class)->middleware('auth');
    Route::resource('equipments', EquipmentController::class)->middleware('auth');
    Route::resource('bills', BillController::class)->middleware('auth');
    Route::resource('bill-payments', BillPaymentController::class)->middleware('auth');
    Route::resource('expenses', ExpenseController::class)->middleware('auth');
    Route::resource('customers', CustomerController::class)->middleware('auth');
    Route::resource('sales', SaleController::class)->middleware('auth');
    Route::get('/sales/{sale}/add-payments', [SalePaymentController::class, 'addPayments'])->name('sales.add-payments')->middleware('auth');
    Route::post('/sales/{sale}/payments', [SalePaymentController::class, 'store'])->name('sales.payments.store');
    Route::get('/users/activity-logs', [UserController::class, 'userActivity'])->middleware('auth');
    Route::resource('users', UserController::class)->middleware('auth');
    Route::get('/sales/export/pdf', [SaleExportController::class, 'exportPdf'])->name('sales.export.pdf');
    Route::get('/sales/export/excel', [SaleExportController::class, 'exportExcel'])->name('sales.export.excel');
    Route::middleware(['auth'])->get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::middleware(['auth'])->get('/pharmacist/dashboard', fn() => Inertia::render('Pharmacist/Dashboard'))->name('pharmacist.dashboard');
    Route::middleware(['auth'])->get('/doctor/dashboard', fn() => Inertia::render('Doctor/Dashboard'))->name('doctor.dashboard');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales')->middleware('auth');
    Route::get('/reports/purchases', [ReportController::class, 'purchaseReport'])->name('reports.purchases')->middleware('auth');
    Route::get('/reports/stock', [StockReportController::class, 'index'])->name('reports.stocks')->middleware('auth');
    Route::get('/reports', fn() => Inertia::render('Reports/Index'))->name('reports.index');
    Route::get('/reports/purchases/pdf', [PurchaseReportController::class, 'exportPdf'])->name('reports.purchases.pdf');

});

