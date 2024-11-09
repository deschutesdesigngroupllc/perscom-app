<?php

declare(strict_types=1);

namespace App\Support\Orion;

use Illuminate\Http\Request;
use Orion\Contracts\KeyResolver as StandardKeyResolver;

class KeyResolver implements StandardKeyResolver
{
    /**
     * @param  string[]  $args
     */
    public function resolveStandardOperationKey(Request $request, array $args): string
    {
        return $args[1];
    }

    /**
     * @param  string[]  $args
     */
    public function resolveRelationOperationParentKey(Request $request, array $args): string
    {
        return $args[1];
    }

    /**
     * @param  string[]  $args
     */
    public function resolveRelationOperationRelatedKey(Request $request, array $args): ?string
    {
        return $args[2] ?? null;
    }
}
