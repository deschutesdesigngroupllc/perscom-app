<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RepositoryContract;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\TenantCollection;

class TenantRepository implements RepositoryContract
{
    public function getAll(): TenantCollection
    {
        return Tenant::all();
    }

    public function findByKey(string $key, mixed $value): Model|Tenant
    {
        return Tenant::where($key, '=', $value)->firstOrFail();
    }

    public function findById(string|int $id): array|Collection|Model|Tenant
    {
        return Tenant::findOrFail($id);
    }
}
