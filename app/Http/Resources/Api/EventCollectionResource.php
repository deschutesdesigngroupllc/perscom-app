<?php

namespace App\Http\Resources\Api;

use App\Models\Event;
use Carbon\Carbon;
use Orion\Http\Resources\CollectionResource;

class EventCollectionResource extends CollectionResource
{
    /**
     * @param $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $recurringEvents = collect();
        $this->collection->filter->repeats->each(function (Event $event) use (&$recurringEvents) {
            $recurringEvents = $recurringEvents->mergeRecursive($event->occurences->mapToGroups(function (Carbon $occurence) use ($event) {
                return [$occurence->format('Y-m-d') => $event->name];
            }));

            echo '<pre>';
            print_r($recurringEvents->toArray());
            exit;
        });

        echo '<pre>';
        print_r($recurringEvents->toArray());
        exit;

        return $recurringEvents->mergeRecursive($this->collection->filter(function (Event $event) {
            return ! $event->repeats;
        })->mapToGroups(function (Event $event, $key) {
            return [Carbon::parse($event->start)->format('Y-m-d') => $event];
        }));
    }
}
