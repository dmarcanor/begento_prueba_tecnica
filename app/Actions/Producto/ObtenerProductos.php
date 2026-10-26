<?php

declare(strict_types=1);

namespace App\Actions\Producto;

use App\Models\Producto;
use Illuminate\Pagination\LengthAwarePaginator;

class ObtenerProductos
{
    public function __invoke(array $params): LengthAwarePaginator
    {
        $porPagina = $params['per_page'] ?? 10;
        $ordenarPor = $params['sort_by'] ?? 'id';
        $direccion = $params['sort_direction'] ?? 'desc';

        return Producto::orderBy($ordenarPor, $direccion)->paginate($porPagina);
    }
}
