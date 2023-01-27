<?php

namespace App\Http\Resources\Api\Widget;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'online' => $this->online,
            'url' => $this->url,
            'rank' => RankResource::make($this->rank),
            'position' => PositionResource::make($this->position),
            'specialty' => SpecialtyResource::make($this->specialty),
            'status' => StatusResource::make($this->status),
        ];
    }
}
