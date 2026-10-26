<?php

declare(strict_types=1);

namespace App\Actions\Producto;

use App\Models\Producto;
use App\Services\Producto\BuscarProducto;

class ObtenerProducto
{
    public function __construct(private BuscarProducto $buscarProducto) {}

    public function __invoke(int $id): Producto
    {
        return ($this->buscarProducto)($id);
    }
}
