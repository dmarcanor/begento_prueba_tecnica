<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPrice extends Model
{
    /** @use HasFactory<\Database\Factories\ProductPriceFactory> */
    use HasFactory;

    protected $table = "precios_productos";

    protected $fillable = [
        "product_id",
        "currency_id",
        "price",
    ];

    protected $casts = [
        "price" => "decimal:2",
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, "product_id");
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, "currency_id");
    }
}
