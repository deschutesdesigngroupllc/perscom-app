<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Features\BillingFeature;
use App\Filament\App\Clusters\Settings;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Laravel\Pennant\Feature;

class Billing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.app.clusters.settings.pages.billing';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Settings::class;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?Model $tenant = null): string
    {
        return route('spark.portal');
    }

    public static function canAccess(): bool
    {
        return Feature::active(BillingFeature::class);
    }
}
