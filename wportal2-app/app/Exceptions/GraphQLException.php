<?php

declare(strict_types=1);

namespace App\Exceptions;

class GraphQLException extends ExceptionBase
{
    /**
     * @return false
     */
    public function render(mixed $request): bool
    {
        return false;
    }
}
