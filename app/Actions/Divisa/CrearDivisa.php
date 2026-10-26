<?php

declare(strict_types=1);

namespace App\Actions\Divisa;

use App\Models\Divisa;

/**
 * Crea una nueva divisa.
 */
class CrearDivisa
{
    public function __invoke(array $datos): Divisa
    {
        return Divisa::create($datos);
    }
}
