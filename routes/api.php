<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CateringOrderController;
use App\Http\Controllers\Api\IngredientController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — FoodFlow ERP & Central Kitchen REST API
| Menerapkan Standar Kurikulum Pemrograman Web Lanjut (Pertemuan 10)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // 1. Endpoint Autentikasi Publik (Rate Limited 5x/menit per email+IP)
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('api.v1.login');

    // 2. Endpoint Terproteksi Token Sanctum
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('api.v1.logout');

        // Master Bahan Baku
        Route::get('/ingredients', [IngredientController::class, 'index'])
            ->middleware('abilities:ingredients:read')
            ->name('api.v1.ingredients.index');
        Route::get('/ingredients/{ingredient}', [IngredientController::class, 'show'])
            ->middleware('abilities:ingredients:read')
            ->name('api.v1.ingredients.show');

        // Master Menu Hidangan & Nilai Gizi
        Route::get('/products', [ProductController::class, 'index'])
            ->middleware('abilities:products:read')
            ->name('api.v1.products.index');
        Route::get('/products/{product}', [ProductController::class, 'show'])
            ->middleware('abilities:products:read')
            ->name('api.v1.products.show');

        // Transaksi Pesanan Katering dengan Pengaman Idempotency Key
        Route::post('/catering-orders', [CateringOrderController::class, 'store'])
            ->middleware(['abilities:orders:create', 'idempotent'])
            ->name('api.v1.catering-orders.store');
    });
});

// Fallback alias langsung (/api/login, /api/products, dll) untuk kemudahan integrasi
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/products', [ProductController::class, 'index'])->middleware('abilities:products:read');
    Route::get('/ingredients', [IngredientController::class, 'index'])->middleware('abilities:ingredients:read');
});
