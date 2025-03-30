<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\CalendarResource\RelationManagers;

use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $icon = 'heroicon-o-calendar-days';

    public function table(Table $table): Table
    {
        return $table
            ->description('The events assigned to this calendar.')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label('Organizer')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('all_day')
                    ->sortable(),
                Tables\Columns\IconColumn::make('repeats')
                    ->sortable(),
                Tables\Columns\IconColumn::make('has_passed')
                    ->sortable(),
            ]);
    }
}
