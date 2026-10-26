<?php

use App\Http\Controllers\Autenticacion\LoginPostController;
use App\Http\Controllers\Divisa\DivisaPostController;
use App\Http\Controllers\Divisa\DivisasGetController;
use App\Http\Controllers\PrecioProducto\PrecioProductoPostController;
use App\Http\Controllers\PrecioProducto\PrecioProductosGetController;
use App\Http\Controllers\Producto\ProductoDeleteController;
use App\Http\Controllers\Producto\ProductoGetController;
use App\Http\Controllers\Producto\ProductoPostController;
use App\Http\Controllers\Producto\ProductoPutController;
use App\Http\Controllers\Producto\ProductosGetController;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación
Route::post('/login', LoginPostController::class);

// Rutas protegidas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Endpoints de productos
    Route::get('/products', ProductosGetController::class);
    Route::post('/products', ProductoPostController::class);
    Route::get('/products/{productoId}', ProductoGetController::class)->whereNumber('productoId');
    Route::put('/products/{productoId}', ProductoPutController::class)->whereNumber('productoId');
    Route::delete('/products/{productoId}', ProductoDeleteController::class)->whereNumber('productoId');

    // Endpoints de precios de productos
    Route::get('/products/{productoId}/prices', PrecioProductosGetController::class)->whereNumber('productoId');
    Route::post('/products/{productoId}/prices', PrecioProductoPostController::class)->whereNumber('productoId');

    // Endpoints de divisas
    Route::get('/divisas', DivisasGetController::class);
    Route::post('/divisas', DivisaPostController::class);
});
