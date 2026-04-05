<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CalendarResource\RelationManagers;

use App\Models\User;
use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static string|BackedEnum|null $icon = 'heroicon-o-calendar-days';

    public function table(Table $table): Table
    {
        return $table
            ->description('The events assigned to the calendar.')
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('author.display_name')
                    ->label('Organizer')
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy(User::select('name')->whereColumn('users.id', 'events.author_id'), $direction)),
                TextColumn::make('start')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = resolve(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('end')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = resolve(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('all_day')
                    ->sortable(),
                IconColumn::make('repeats')
                    ->sortable(),
                IconColumn::make('has_passed')
                    ->sortable(),
            ]);
    }
}
