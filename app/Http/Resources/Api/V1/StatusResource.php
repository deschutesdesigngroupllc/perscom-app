<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\ApiResource;
use App\Models\Status;

class StatusResource extends ApiResource
{
    public function toArray($request): array
    {
        /** @var Status $status */
        $status = $this->resource;

        return $this->toArrayWithMerge($request, [
            'text_color' => $status->color,
            'bg_color' => $status->color,
        ]);
    }
}
