<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("precios_productos", function (Blueprint $table) {
            $table->id();
            $table->foreignId("product_id")->constrained("productos")->cascadeOnDelete();
            $table->foreignId("currency_id")->constrained("divisas");
            $table->decimal("price", 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("precios_productos");
    }
};
