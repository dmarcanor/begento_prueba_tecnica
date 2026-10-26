<?php

declare(strict_types=1);

namespace App\Actions\Producto;

use App\Models\Producto;

/**
 * Crea un nuevo producto.
 */
class CrearProducto
{
    public function __invoke(array $datos): Producto
    {
        return Producto::create($datos);
    }
}
