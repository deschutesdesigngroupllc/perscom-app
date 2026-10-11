<?php

declare(strict_types=1);

namespace App\Data\Organization;

use Spatie\LaravelData\Data;

class CredentialData extends Data
{
    /**
     * @param  string  $type  One of the CredentialType values: certification, license, other.
     */
    public function __construct(
        public string $name,
        public ?string $description = null,
        public string $type = 'certification',
        public ?string $issuer = null,
    ) {}
}
