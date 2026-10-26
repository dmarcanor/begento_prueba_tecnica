<?php

declare(strict_types=1);

namespace App\Actions\Producto;

use App\Models\Producto;

/**
 * Obtiene un producto específico por ID.
 */
class ObtenerProducto
{
    public function __invoke(int $id): ?Producto
    {
        return Producto::find($id);
    }
}
