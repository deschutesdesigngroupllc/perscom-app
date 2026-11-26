<?php

declare(strict_types=1);

namespace App\Contracts;

interface RunsPerTenant
{
    public function work(): void;
}
