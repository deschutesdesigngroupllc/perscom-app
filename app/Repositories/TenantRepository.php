<?php

namespace App\Repositories;

use App\Contracts\RepositoryContract;
use App\Models\Tenant;

class TenantRepository implements RepositoryContract
{
    /**
     * @return mixed|\Stancl\Tenancy\Database\TenantCollection
     */
    public function getAll()
    {
        return Tenant::all();
    }

    /**
     * @return Tenant
     */
    public function findByKey($key, $value)
    {
        return Tenant::where($key, '=', $value)->firstOrFail();
    }

    /**
     * @return Tenant
     */
    public function findById($id)
    {
        return Tenant::findOrFail($id);
    }
}
