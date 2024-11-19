<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Enums\NotificationChannel;
use App\Models\Enums\NotificationInterval;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Orion\Http\Requests\Request;

class EventRequest extends Request
{
    /**
     * @return array<string, string|Enum>
     */
    public function commonRules(): array
    {
        return [
            'name' => 'string|max:255',
            'calendar_id' => 'integer|exists:calendars,id',
            'description' => 'nullable|string|max:65535',
            'content' => 'nullable|string|max:65535',
            'location' => 'nullable|string|max:65535',
            'url' => 'nullable|string|max:65535',
            'author_id' => 'integer|exists:users,id',
            'all_day' => 'boolean',
            'starts' => 'datetime',
            'ends' => 'datetime|after:start',
            'repeats' => 'boolean',
            'registrations_enabled' => 'boolean',
            'registrations_deadline' => 'nullable|datetime',
            'notifications_enabled' => 'boolean',
            'notifications_interval' => Rule::enum(NotificationInterval::class),
            'notifications_channels' => Rule::enum(NotificationChannel::class),
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'nullable|date',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        $rules = [];
        if (! $this->route('calendar')) {
            $rules = [
                'calendar_id' => 'required|integer|exists:calendars,id',
            ];
        }

        return array_merge($rules, [
            'author_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|max:255',
            'starts' => 'required|date',
            'ends' => 'required|date|after:start',
            'all_day' => 'required|boolean',
        ]);
    }
}
