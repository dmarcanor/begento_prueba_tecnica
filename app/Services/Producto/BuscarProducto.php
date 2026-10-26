<?php

declare(strict_types=1);

namespace App\Services\Producto;

use App\Exceptions\ProductoNoEncontradoException;
use App\Models\Producto;

class BuscarProducto
{
    public function __invoke(int $id): Producto
    {
        $producto = Producto::find($id);

        if (!$producto) {
            throw new ProductoNoEncontradoException();
        }

        return $producto;
    }
}
