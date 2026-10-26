<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

/**
 * Se lanza cuando el email o la contraseña no coinciden.
 */
class CredencialesInvalidasException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Unauthorized');
    }
}
