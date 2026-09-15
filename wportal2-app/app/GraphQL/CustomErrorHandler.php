<?php

declare(strict_types=1);

namespace App\GraphQL;

use App\Exceptions\GraphQLException;
use Closure;
use GraphQL\Error\Error;
use GraphQL\Error\InvariantViolation;
use Nuwave\Lighthouse\Execution\ErrorHandler;

final class CustomErrorHandler implements ErrorHandler
{
    /**
     * @throws GraphQLException
     */
    public function __invoke(?Error $error, Closure $next): ?array
    {
        if ($error === null) {
            return $next(null);
        }

        // InvariantViolationの場合はpath表示をエラー情報に追記して再スロー.
        $invariantViolation = $error->getPrevious();
        if ($invariantViolation !== null) {
            $className = $invariantViolation::class;
            if ($invariantViolation instanceof InvariantViolation) {
                $path = implode('/', $error->path);

                throw new GraphQLException(
                    $error->getMessage().' => '.$path."($className)",
                    $invariantViolation->getCode(),
                    $invariantViolation
                );
            }
        }

        // Keep the pipeline going, last step formats the error into an array
        return $next($error);
    }
}
