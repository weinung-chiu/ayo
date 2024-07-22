<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OrderController;

Route::get('/', function () {
    return 'Hello, World!';
});
Route::post('/orders', [OrderController::class, 'store']);
