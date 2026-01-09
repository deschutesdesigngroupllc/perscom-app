<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\TenantCollection;

interface Repository
{
    public function getAll(): TenantCollection;

    public function findByKey(string $key, mixed $value): Model|Tenant;

    public function findById(string|int $id): array|Collection|Model|Tenant;
}
