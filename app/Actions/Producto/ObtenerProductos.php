<?php

declare(strict_types=1);

namespace App\Actions\Producto;

use App\Models\Producto;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Obtiene una lista paginada de productos.
 */
class ObtenerProductos
{
    public function __invoke(array $params): LengthAwarePaginator
    {
        $porPagina = $params['per_page'] ?? 10;
        $ordenarPor = $params['sort_by'] ?? 'id';
        $direccion = $params['sort_direction'] ?? 'desc';

        // Mapeo de campos de API (inglés) a nombres de columnas (español)
        $mapeo = [
            'nombre' => 'name',
            'descripcion' => 'description',
            'precio' => 'price',
            'divisa_id' => 'currency_id',
            'costo_impuesto' => 'tax_cost',
            'costo_fabricacion' => 'manufacturing_cost',
        ];

        $columna = $mapeo[$ordenarPor] ?? $ordenarPor;

        return Producto::orderBy($columna, $direccion)->paginate($porPagina);
    }
}
