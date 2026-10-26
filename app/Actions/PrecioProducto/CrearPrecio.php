<?php

declare(strict_types=1);

namespace App\Actions\PrecioProducto;

use App\Models\PrecioProducto;
use App\Services\Producto\BuscarProducto;

/**
 * Crea un nuevo precio para un producto.
 */
class CrearPrecio
{
    public function __construct(private BuscarProducto $buscarProducto) {}

    public function __invoke(int $idProducto, array $datos): PrecioProducto
    {
        ($this->buscarProducto)($idProducto);

        $precio = PrecioProducto::create([
            'product_id' => $idProducto,
            'price' => $datos['price'],
            'currency_id' => $datos['currency_id'],
        ]);

        $precio->load('divisa');

        return $precio;
    }
}
