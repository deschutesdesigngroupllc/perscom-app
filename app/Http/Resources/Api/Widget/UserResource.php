<?php

namespace App\Http\Resources\Api\Widget;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'online' => $this->online,
            'rank' => RankResource::make($this->rank),
            'position' => PositionResource::make($this->position),
            'specialty' => SpecialtyResource::make($this->specialty),
            'status' => StatusResource::make($this->status)
        ];
    }
}
