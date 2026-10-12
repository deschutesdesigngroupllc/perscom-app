<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Pages;

use App\Filament\App\Resources\EventResource;
use App\Services\UserSettingsService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Date;
use Throwable;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    /**
     * Prefill the form from the calendar's quick-create "More Options" link. Dates arrive in the
     * app timezone and are shown in the member's timezone, like the form's date pickers.
     */
    protected function afterFill(): void
    {
        $allDay = request()->boolean('all_day');
        $timezone = UserSettingsService::timezone();

        collect([
            'name' => request()->string('name')->trim()->toString(),
            'calendar_id' => request()->integer('calendar_id') ?: null,
            'all_day' => request()->has('all_day') ? $allDay : null,
            'starts' => $this->prefillDate(request()->string('starts')->toString(), $allDay, $timezone),
            'ends' => $this->prefillDate(request()->string('ends')->toString(), $allDay, $timezone),
        ])
            ->filter(fn (mixed $value): bool => filled($value) || is_bool($value))
            ->each(function (mixed $value, string $key): void {
                data_set($this->data, $key, $value);
            });
    }

    protected function prefillDate(string $value, bool $allDay, string $timezone): ?string
    {
        if (blank($value)) {
            return null;
        }

        try {
            $date = Date::parse($value, config('app.timezone'));
        } catch (Throwable) {
            return null;
        }

        return $allDay
            ? $date->toDateString()
            : $date->setTimezone($timezone)->format('Y-m-d H:i:s');
    }
}
