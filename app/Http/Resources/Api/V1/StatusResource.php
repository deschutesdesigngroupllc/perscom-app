<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use App\Models\Status;
use Orion\Http\Resources\Resource;

class StatusResource extends Resource
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
