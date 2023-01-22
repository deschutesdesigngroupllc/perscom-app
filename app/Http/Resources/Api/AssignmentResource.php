<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class AssignmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'unit' => UnitResource::make($this->unit),
            'position' => PositionResource::make($this->position),
            'specialty' => SpecialtyResource::make($this->specialty),
            'document' => DocumentResource::make($this->document), //'author' => $this->author,
            'text' => $this->text,
        ];
    }
}
