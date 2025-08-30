<?php

use App\Http\Controllers\Api\ApiCustomerController;
use App\Http\Controllers\Api\ApiSalesController;
use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Customer api routes

Route::resource('customers', ApiCustomerController::class);
Route::get('/sales/{sale}', [ApiSalesController::class,'destroy']);
