<?php

declare(strict_types=1);

namespace App\Events;

class ApiPurgedSuccessful
{
    public function __construct(public array $tags, public string $event)
    {
        //
    }
}
