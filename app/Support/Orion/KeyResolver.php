<?php

declare(strict_types=1);

namespace App\Support\Orion;

use Illuminate\Http\Request;
use Orion\Contracts\KeyResolver as StandardKeyResolver;

class KeyResolver implements StandardKeyResolver
{
    public function resolveStandardOperationKey(Request $request, array $args)
    {
        return $args[1];
    }

    public function resolveRelationOperationParentKey(Request $request, array $args)
    {
        return $args[1];
    }

    public function resolveRelationOperationRelatedKey(Request $request, array $args)
    {
        return $args[2] ?? null;
    }
}
