<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

Route::get('/products', function () {
    return view('product_form');
});
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/data', [ProductController::class, 'index']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);
Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
Route::delete('/categories/{id}/image', [CategoryController::class, 'deleteImage']);
