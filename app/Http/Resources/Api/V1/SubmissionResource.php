<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Orion\Http\Resources\Resource;

class SubmissionResource extends Resource
{
    public function toArray($request): array
    {
        $response = parent::toArray($request);

        if (filled(data_get($response, 'statuses'))) {
            data_set($response, 'statuses', StatusResource::collection($this->resource->statuses)->toArray($request));
        }

        return $response;
    }
}
