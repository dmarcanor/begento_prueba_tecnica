<?php

declare(strict_types=1);

namespace App\Actions\PrecioProducto;

use App\Models\PrecioProducto;
use App\Services\Producto\BuscarProducto;

class CrearPrecio
{
    public function __construct(private BuscarProducto $buscarProducto) {}

    public function __invoke(int $productoId, array $datos): PrecioProducto
    {
        ($this->buscarProducto)($productoId);

        $precio = PrecioProducto::create([
            'product_id' => $productoId,
            'price' => $datos['price'],
            'currency_id' => $datos['currency_id'],
        ]);

        $precio->load('divisa');

        return $precio;
    }
}
