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
     * @return Tenant|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function findByKey($key, $value)
    {
        return Tenant::where($key, '=', $value)->firstOrFail();
    }

    /**
     * @return Tenant|Tenant[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function findById($id)
    {
        return Tenant::findOrFail($id);
    }
}
