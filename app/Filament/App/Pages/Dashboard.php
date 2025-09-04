<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Filament\Actions\CopyAction;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Panel;
use Filament\Support\Enums\IconPosition;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'PERSCOM Personnel Management System';

    protected ?string $subheading = 'Personnel management made easy for high-performing, results-driven organizations.';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }

    protected function getActions(): array
    {
        return [
            CopyAction::make('perscom_id')
                ->label(fn (): string => 'PERSCOM ID: '.tenant()->getTenantKey())
                ->copyable((string) tenant('id'))
                ->icon('heroicon-o-document-duplicate')
                ->iconPosition(IconPosition::After)
                ->color('gray'),
        ];
    }
}
