<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Orion\Http\Resources\Resource;

class RosterResource extends Resource
{
    /**
     * @param  Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'users' => $this->resource->users,
        ];
    }
}
