<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\ApiResource;
use Illuminate\Http\Request;

class SubmissionResource extends ApiResource
{
    public function toArray(Request $request): array
    {
        $response = parent::toArray($request);

        if (filled(data_get($response, 'statuses'))) {
            data_set($response, 'statuses', StatusResource::collection($this->resource->statuses)->toArray($request));
        }

        return $response;
    }
}
