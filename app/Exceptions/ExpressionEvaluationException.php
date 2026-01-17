<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class ExpressionEvaluationException extends Exception
{
    public function __construct(string $expression, string $message, ?Throwable $previous = null)
    {
        $fullMessage = sprintf("Failed to evaluate expression '%s': %s", $expression, $message);

        parent::__construct($fullMessage, 0, $previous);
    }
}
