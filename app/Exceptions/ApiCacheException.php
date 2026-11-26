<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class ApiCacheException extends Exception
{
    public function __construct(protected mixed $body = null)
    {
        parent::__construct('The API cache purge failed.');
    }

    public function context(): array
    {
        if ($this->body === null) {
            return [];
        }

        return [
            'body' => $this->body,
        ];
    }
}
