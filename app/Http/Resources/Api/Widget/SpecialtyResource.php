<?php

namespace App\Http\Resources\Api\Widget;

use Illuminate\Http\Resources\Json\JsonResource;

class SpecialtyResource extends JsonResource
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
            'abbreviation' => $this->abbreviation
        ];
    }
}
