<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Se lanza cuando un producto solicitado no existe.
 */
class ProductoNoEncontradoException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Product not found');
    }
}
