<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    /** @use HasFactory<\Database\Factories\CurrencyFactory> */
    use HasFactory;

    protected $table = "divisas";

    protected $fillable = [
        "name",
        "symbol",
        "exchange_rate",
    ];

    protected $casts = [
        "exchange_rate" => "decimal:8",
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, "currency_id");
    }

    public function productPrices(): HasMany
    {
        return $this->hasMany(ProductPrice::class, "currency_id");
    }
}
