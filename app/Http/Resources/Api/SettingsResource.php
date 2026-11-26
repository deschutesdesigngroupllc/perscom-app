<?php

declare(strict_types=1);

namespace App\Http\Resources\Api;

use App\Models\Settings;
use Illuminate\Http\Request;
use JsonException;

class SettingsResource extends ApiResource
{
    /**
     * Transform the setting into an array with its decoded payload.
     *
     * @return array<string, mixed>
     *
     * @throws JsonException when the stored payload is not valid JSON
     */
    public function toArray(Request $request): array
    {
        /** @var Settings $resource */
        $resource = $this->resource;

        return $this->toArrayWithMerge($request, [
            'payload' => filled($resource->payload)
                ? json_decode($resource->payload, flags: JSON_THROW_ON_ERROR)
                : null,
        ]);
    }
}
