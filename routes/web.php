<?php

use App\Http\Controllers\MedicineCategoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

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