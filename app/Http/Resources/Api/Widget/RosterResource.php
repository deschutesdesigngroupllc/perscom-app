<?php

namespace App\Http\Resources\Api\Widget;

use Illuminate\Http\Request;
use Orion\Http\Resources\Resource;

class RosterResource extends Resource
{
    /**
     * @param Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'name' => $this->resource->name,
            'users' => UserResource::collection($this->resource->users)
        ];
    }
}
