<?php

declare(strict_types=1);

namespace App\Actions\Producto;

use App\Models\Producto;
use App\Services\Producto\BuscarProducto;

/**
 * Elimina un producto (soft delete).
 */
class EliminarProducto
{
    public function __construct(private BuscarProducto $buscarProducto) {}

    public function __invoke(int $id): bool
    {
        $producto = ($this->buscarProducto)($id);
        return $producto->delete();
    }
}
