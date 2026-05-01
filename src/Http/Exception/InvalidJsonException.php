<?php

declare(strict_types=1);

namespace App\Http\Exception;

use RuntimeException;

final class InvalidJsonException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Request body must be valid JSON');
    }
}