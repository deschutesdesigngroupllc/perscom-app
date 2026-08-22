<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        /** @var Settings $resource */
        $resource = $this->resource;

        return $this->toArrayWithMerge($request, [
            'payload' => json_decode((string) $resource->payload),
        ]);
    }
}
