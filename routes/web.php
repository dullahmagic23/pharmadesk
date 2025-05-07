<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\BillPaymentController;
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
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockHistoryController;

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('home')->middleware('auth');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';


Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('medicines', MedicineController::class);
});
Route::resource('medicine-categories', MedicineCategoryController::class);
Route::resource('products', ProductController::class)->middleware('auth');
Route::resource('stocks',StockController::class)->middleware('auth');
Route::resource('stock-histories',StockHistoryController::class)->middleware('auth');
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
Route::resource('bill-payments',BillPaymentController::class)->middleware('auth');
Route::resource('expenses',ExpenseController::class)->middleware('auth');
