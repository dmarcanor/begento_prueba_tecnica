<?php

declare(strict_types=1);

namespace App\Actions\Divisa;

use App\Models\Divisa;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Obtiene una lista paginada de divisas.
 */
class ObtenerDivisas
{
    public function __invoke(array $params): LengthAwarePaginator
    {
        $porPagina = $params['per_page'] ?? 10;
        $ordenarPor = $params['sort_by'] ?? 'id';
        $direccion = $params['sort_direction'] ?? 'desc';

        return Divisa::orderBy($ordenarPor, $direccion)->paginate($porPagina);
    }
}
