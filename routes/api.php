<?php

use App\Http\Controllers\DivisaController;
use App\Http\Controllers\PrecioProductoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// Rutas de autenticación
Route::post('/login', [UsuarioController::class, 'inicioSesion']);

// Rutas protegidas que requieren autenticación
Route::middleware('auth:sanctum')->group(function () {
    // Endpoints de productos
    Route::post('/products', [ProductoController::class, 'store']);
    Route::get('/products', [ProductoController::class, 'index']);
    Route::get('/products/{producto}', [ProductoController::class, 'show'])->whereNumber('producto');
    Route::put('/products/{producto}', [ProductoController::class, 'update'])->whereNumber('producto');
    Route::delete('/products/{producto}', [ProductoController::class, 'destroy'])->whereNumber('producto');
    
    // Endpoints de precios de productos
    Route::get('/products/{idProducto}/prices', [PrecioProductoController::class, 'index'])->whereNumber('idProducto');
    Route::post('/products/{idProducto}/prices', [PrecioProductoController::class, 'store'])->whereNumber('idProducto');

    // Endpoints de divisas
    Route::get('/divisas', [DivisaController::class, 'index']);
    Route::post('/divisas', [DivisaController::class, 'store']);
});
