<?php

namespace App\Http\Resources\Api\Widget;

use Orion\Http\Resources\Resource;

class RankResource extends Resource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'description' => $this->resource->description,
            'image_url' => $this->resource->image->image_url ?? null,
        ];
    }
}
