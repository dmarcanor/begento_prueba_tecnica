<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Database\Factories\ProductoFactory;

/**
 * Modelo que representa un producto del sistema.
 */
#[Fillable([
    'name',
    'description',
    'price',
    'currency_id',
    'tax_cost',
    'manufacturing_cost',
])]
class Producto extends Model
{
    /** @use HasFactory<ProductoFactory> */
    use HasFactory, SoftDeletes;

    /**
     * Los atributos que deben ser convertidos a tipos nativos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'tax_cost' => 'decimal:2',
            'manufacturing_cost' => 'decimal:2',
        ];
    }

    /**
     * Relación: un producto pertenece a una divisa.
     */
    public function divisa(): BelongsTo
    {
        return $this->belongsTo(Divisa::class, 'currency_id');
    }

    /**
     * Relación: un producto puede tener muchos precios.
     */
    public function preciosProductos(): HasMany
    {
        return $this->hasMany(PrecioProducto::class);
    }
}
