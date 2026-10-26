<?php

declare(strict_types=1);

namespace App\Actions\Producto;

use App\Models\Producto;

/**
 * Actualiza un producto existente.
 */
class ActualizarProducto
{
    public function __invoke(Producto $producto, array $datos): Producto
    {
        $producto->update($datos);
        return $producto->fresh();
    }
}
