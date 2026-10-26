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
    Route::get('/products/{producto}', ProductoGetController::class)->whereNumber('producto');
    Route::put('/products/{producto}', ProductoPutController::class)->whereNumber('producto');
    Route::delete('/products/{producto}', ProductoDeleteController::class)->whereNumber('producto');
    
    // Endpoints de precios de productos
    Route::get('/products/{idProducto}/prices', PrecioProductosGetController::class)->whereNumber('idProducto');
    Route::post('/products/{idProducto}/prices', PrecioProductoPostController::class)->whereNumber('idProducto');

    // Endpoints de divisas
    Route::get('/divisas', DivisasGetController::class);
    Route::post('/divisas', DivisaPostController::class);
});
