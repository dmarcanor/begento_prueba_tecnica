<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\DivisaFactory;

#[Fillable([
    'name',
    'symbol',
    'exchange_rate',
])]
class Divisa extends Model
{
    /** @use HasFactory<DivisaFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:8',
        ];
    }

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    public function preciosProductos(): HasMany
    {
        return $this->hasMany(PrecioProducto::class);
    }
}
