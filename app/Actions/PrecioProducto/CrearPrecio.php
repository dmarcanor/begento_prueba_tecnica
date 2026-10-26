<?php

declare(strict_types=1);

namespace App\Actions\PrecioProducto;

use App\Models\PrecioProducto;

/**
 * Crea un nuevo precio para un producto.
 */
class CrearPrecio
{
    public function __invoke(int $idProducto, array $datos): PrecioProducto
    {
        $precio = PrecioProducto::create([
            'product_id' => $idProducto,
            'price' => $datos['price'],
            'currency_id' => $datos['currency_id'],
        ]);

        $precio->load('divisa');

        return $precio;
    }
}
