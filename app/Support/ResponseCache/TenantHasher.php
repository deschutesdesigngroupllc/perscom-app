<?php

namespace App\Support\ResponseCache;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Spatie\ResponseCache\Hasher\DefaultHasher;

class TenantHasher extends DefaultHasher
{
    public function getHashFor(Request $request): string
    {
        $baseHash = parent::getHashFor($request);

        return optional(tenant(), function (Tenant $tenant) use ($baseHash) {
            return "$baseHash-tenant{$tenant->getTenantKey()}";
        }) ?? $baseHash;
    }
}
