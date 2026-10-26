<?php

declare(strict_types=1);

namespace App\Actions\Producto;

use App\Models\Producto;
use App\Services\Producto\BuscarProducto;

class ActualizarProducto
{
    public function __construct(private BuscarProducto $buscarProducto) {}

    public function __invoke(int $id, array $datos): Producto
    {
        $producto = ($this->buscarProducto)($id);

        $producto->update($datos);
        
        return $producto->fresh();
    }
}
