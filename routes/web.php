<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');

});


// route resource for product
Route::resource('/products', \App\Http\Controllers\ProductController::class);