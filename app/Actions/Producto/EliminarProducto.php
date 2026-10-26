<?php

declare(strict_types=1);

namespace App\Actions\Producto;

use App\Models\Producto;

/**
 * Elimina un producto (soft delete).
 */
class EliminarProducto
{
    public function __invoke(Producto $producto): bool
    {
        return $producto->delete();
    }
}
