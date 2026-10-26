<?php

declare(strict_types=1);

namespace App\Actions\Divisa;

use App\Models\Divisa;

class CrearDivisa
{
    public function __invoke(array $datos): Divisa
    {
        return Divisa::create($datos);
    }
}
