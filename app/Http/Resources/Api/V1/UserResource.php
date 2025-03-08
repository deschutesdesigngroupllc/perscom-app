<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\ApiResource;

class UserResource extends ApiResource
{
    public function toArray(\Illuminate\Http\Request $request): array
    {
        $response = parent::toArray($request);

        if (filled(data_get($response, 'status'))) {
            data_set($response, 'status', StatusResource::make($this->resource->status)->toArray($request));
        }

        return $response;
    }
}
