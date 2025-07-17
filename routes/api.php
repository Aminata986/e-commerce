<?php
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Auth\ApiAuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('orders', OrderController::class)->except(['store', 'show']);
    Route::apiResource('order-items', OrderItemController::class);
    Route::apiResource('payments', PaymentController::class);
    Route::get('orders/{id}/invoice', [OrderController::class, 'downloadInvoice']);
    Route::post('payments/simulate/{orderId}', [PaymentController::class, 'simulateOnlinePayment']);
    Route::post('payments/delivery/{orderId}', [PaymentController::class, 'markAsPaidOnDelivery']);
    Route::get('dashboard/statistics', [DashboardController::class, 'statistics']);
});

Route::middleware(['validate.api'])->group(function () {
    Route::post('register', [ApiAuthController::class, 'register']);
    Route::post('login', [ApiAuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('logout', [ApiAuthController::class, 'logout']);
    
    Route::middleware(['auth', 'isClient'])->group(function () {
        Route::post('orders', [OrderController::class, 'store']);
        Route::get('orders/{id}', [OrderController::class, 'show']);
        Route::get('cart', [CartController::class, 'show']);
        Route::post('cart/add', [CartController::class, 'add']);
        Route::put('cart/item/{itemId}', [CartController::class, 'update']);
        Route::delete('cart/item/{itemId}', [CartController::class, 'remove']);
    });
});

Route::get('/products', [ProductController::class, 'index']);
Route::get('/categories', [CategoryController::class, 'index']);