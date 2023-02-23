<?php

namespace App\Http\Resources\Api;

use Orion\Http\Resources\Resource;

class RosterResource extends Resource
{
    /**
     * @param $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->toArrayWithMerge($request, [
            'users' => $this->resource->users,
        ]);
    }
}
