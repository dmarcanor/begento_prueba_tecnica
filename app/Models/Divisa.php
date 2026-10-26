<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\DivisaFactory;

/**
 * Modelo que representa una divisa (moneda) del sistema.
 */
#[Fillable([
    'name',
    'symbol',
    'exchange_rate',
])]
class Divisa extends Model
{
    /** @use HasFactory<DivisaFactory> */
    use HasFactory;

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'exchange_rate' => 'decimal:8',
        ];
    }

    /**
     * Relación: una divisa puede tener muchos productos.
     */
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    /**
     * Relación: una divisa puede tener muchos precios de productos.
     */
    public function preciosProductos(): HasMany
    {
        return $this->hasMany(PrecioProducto::class);
    }
}
