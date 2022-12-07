<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Indicates if the resource's collection keys should be preserved.
     *
     * @var bool
     */
    public $preserveKeys = true;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'online' => $this->online,
            'position' => PositionResource::make($this->position),
            'rank' => RankResource::make($this->rank),
            'specialty' => SpecialtyResource::make($this->specialty),
            'status' => StatusResource::make($this->status),
            'unit' => UnitResource::make($this->unit),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'last_seen_at' => $this->last_seen_at,
            'time_in_grade' => $this->time_in_grade,
            'time_in_assignment' => $this->time_in_assignment,
            'notes' => $this->notes,
            'notes_updated_at' => $this->notes_updated_at,
	        'url' => $this->url,
            'records' => [
                'assignments' => $this->assignment_records,
                'awards' => $this->award_records,
                'combat' => $this->combat_records,
                'qualifications' => $this->qualification_records,
                'ranks' => $this->rank_records,
                'service' => $this->service_records,
                'status' => $this->statuses,
            ],
        ];
    }
}
