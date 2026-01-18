<?php

declare(strict_types=1);

namespace App\Support\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class MathExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('increment', $this->increment(...)),
            new TwigFilter('decrement', $this->decrement(...)),
        ];
    }

    public function increment(mixed $value, int|float $amount = 1): int|float
    {
        if (! is_numeric($value)) {
            return $amount;
        }

        return $value + $amount;
    }

    public function decrement(mixed $value, int|float $amount = 1): int|float
    {
        if (! is_numeric($value)) {
            return -$amount;
        }

        return $value - $amount;
    }
}
