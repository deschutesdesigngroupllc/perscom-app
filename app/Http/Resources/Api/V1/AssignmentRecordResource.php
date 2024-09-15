<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Orion\Http\Resources\Resource;

class AssignmentRecordResource extends Resource
{
    public function toArray($request): array
    {
        $response = parent::toArray($request);

        if (filled(data_get($response, 'status'))) {
            data_set($response, 'status', StatusResource::make($this->resource->status)->toArray($request));
        }

        return $response;
    }
}
