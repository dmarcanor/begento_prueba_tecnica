<?php

declare(strict_types=1);

namespace App\Actions\PrecioProducto;

use App\Models\PrecioProducto;
use App\Services\Producto\BuscarProducto;
use Illuminate\Pagination\LengthAwarePaginator;

class ObtenerPrecios
{
    public function __construct(private BuscarProducto $buscarProducto) {}

    public function __invoke(int $productoId, array $params): LengthAwarePaginator
    {
        ($this->buscarProducto)($productoId);

        $porPagina = $params['per_page'] ?? 10;
        $ordenarPor = $params['sort_by'] ?? 'created_at';
        $direccion = $params['sort_direction'] ?? 'desc';

        return PrecioProducto::where('product_id', $productoId)
            ->with('divisa')
            ->orderBy($ordenarPor, $direccion)
            ->paginate($porPagina);
    }
}
