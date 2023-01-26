<?php

namespace App\Http\Resources\Api\Widget;

use Illuminate\Http\Resources\Json\JsonResource;

class RankResource extends JsonResource
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
            'abbreviation' => $this->abbreviation,
            'paygrade' => $this->paygrade,
            'image_url' => $this->image->image_url ?? null
        ];
    }
}
