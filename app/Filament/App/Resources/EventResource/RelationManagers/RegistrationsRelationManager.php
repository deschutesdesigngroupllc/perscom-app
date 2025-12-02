<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\RelationManagers;

use App\Services\UserSettingsService;
use App\Settings\OrganizationSettings;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'registrations';

    protected static string|BackedEnum|null $icon = 'heroicon-o-user-plus';

    protected $listeners = ['refreshRegistrations' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->description('The users that are registered for the event.')
            ->emptyStateHeading('No registrations')
            ->emptyStateDescription('There are no users registered.')
            ->description('The users registered for the event.')
            ->columns([
                TextColumn::make('registration.user.name'),
                TextColumn::make('registration.status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('registration.created_at')
                    ->label('Registered')
                    ->timezone(UserSettingsService::get('timezone', function () {
                        /** @var OrganizationSettings $settings */
                        $settings = app(OrganizationSettings::class);

                        return $settings->timezone ?? config('app.timezone');
                    }))
                    ->dateTime(),
            ])
            ->defaultSort('events_registrations.created_at', 'desc');
    }
}
