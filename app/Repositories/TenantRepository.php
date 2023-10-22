<?php

namespace App\Repositories;

use App\Contracts\RepositoryContract;
use App\Models\Tenant;

class TenantRepository implements RepositoryContract
{
    public function getAll(): mixed
    {
        return Tenant::all();
    }

    public function findByKey($key, $value): mixed
    {
        return Tenant::where($key, '=', $value)->firstOrFail();
    }

    public function findById($id): mixed
    {
        return Tenant::findOrFail($id);
    }
}
