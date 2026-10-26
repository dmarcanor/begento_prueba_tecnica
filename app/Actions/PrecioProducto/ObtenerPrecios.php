<?php

declare(strict_types=1);

namespace App\Actions\PrecioProducto;

use App\Models\PrecioProducto;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Obtiene una lista paginada de precios de un producto.
 */
class ObtenerPrecios
{
    public function __invoke(int $idProducto, array $params): LengthAwarePaginator
    {
        $porPagina = $params['per_page'] ?? 10;
        $ordenarPor = $params['sort_by'] ?? 'created_at';
        $direccion = $params['sort_direction'] ?? 'desc';

        return PrecioProducto::where('product_id', $idProducto)
            ->with('divisa')
            ->orderBy($ordenarPor, $direccion)
            ->paginate($porPagina);
    }
}
