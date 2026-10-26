<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

class ProductoNoEncontradoException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Product not found');
    }
}
