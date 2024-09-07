<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Panel;
use Filament\Support\Enums\IconPosition;
use Webbingbrasil\FilamentCopyActions\Pages\Actions\CopyAction;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'PERSCOM Personnel Management System';

    protected ?string $subheading = 'Personnel management made easy for high-performing, results-driven organizations.';

    public static function isTenantSubscriptionRequired(Panel $panel): bool
    {
        return false;
    }

    protected function getActions(): array
    {
        return [
            CopyAction::make('test')
                ->label(fn () => 'PERSCOM ID: '.tenant('id'))
                ->copyable((string) tenant('id'))
                ->icon('heroicon-o-document-duplicate')
                ->iconPosition(IconPosition::After)
                ->color('gray'),
        ];
    }
}
