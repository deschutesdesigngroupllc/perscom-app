<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Number;

class AiUsageLimitExceededException extends Exception
{
    public function __construct(int $limit)
    {
        parent::__construct(sprintf(
            'Your organization has reached its monthly AI limit of %s tokens. '
                .'The limit resets at the start of next month.',
            Number::format($limit),
        ));
    }
}
