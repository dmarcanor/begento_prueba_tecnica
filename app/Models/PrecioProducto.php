<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Database\Factories\PrecioProductoFactory;

#[Table('precios_productos')]
#[Fillable([
    'product_id',
    'currency_id',
    'price',
])]
class PrecioProducto extends Model
{
    /** @use HasFactory<PrecioProductoFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'product_id');
    }

    public function divisa(): BelongsTo
    {
        return $this->belongsTo(Divisa::class, 'currency_id');
    }
}
