<?php
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PaymentController;

Route::apiResource('orders', OrderController::class);
Route::apiResource('order-items', OrderItemController::class);
Route::apiResource('payments', PaymentController::class);
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/test', function () {
    return response()->json(['message' => 'API test OK']);
});

Route::get('/products', [ProductController::class, 'index']);


Route::get('orders/{id}/invoice', [OrderController::class, 'downloadInvoice']);