<?php

namespace App\Repositories;

use App\Contracts\RepositoryContract;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Stancl\Tenancy\Database\TenantCollection;

class TenantRepository implements RepositoryContract
{
    public function getAll(): TenantCollection
    {
        return Tenant::all();
    }

    public function findByKey(string $key, mixed $value): Tenant
    {
        return Tenant::where($key, '=', $value)->firstOrFail();
    }

    public function findById(mixed $id): Builder|Collection|Tenant
    {
        return Tenant::findOrFail($id);
    }
}
