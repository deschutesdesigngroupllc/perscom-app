<?php

declare(strict_types=1);

namespace App\Filament\Admin\Widgets;

use App\Metrics\Metric;
use App\Metrics\TenantCreationMetric;
use App\Metrics\UserCreationMetric;
use App\Metrics\UserLoginMetric;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class MonthlyActiveResources extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $currentUserLogin = Metric::total(UserLoginMetric::class, function (Builder $query) {
            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        });
        $previousUserLogin = Metric::total(UserLoginMetric::class, function (Builder $query) {
            $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonths()->endOfMonth()]);
        });
        $userLoginDifference = Number::percentageDifference($previousUserLogin, $currentUserLogin);
        $userLogins = new UserLoginMetric;
        $userLoginData = Trend::query($userLogins->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        $currentUserCreation = Metric::total(UserCreationMetric::class, function (Builder $query) {
            $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
        });
        $previousUserCreation = Metric::total(UserCreationMetric::class, function (Builder $query) {
            $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonths()->endOfMonth()]);
        });
        $userCreationDifference = Number::percentageDifference($previousUserCreation, $currentUserCreation);
        $userCreations = new UserCreationMetric;
        $userCreationData = Trend::query($userCreations->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        $currentRegistrations = Metric::total(TenantCreationMetric::class, function (Builder $query) {
            $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
        });
        $previousRegistrations = Metric::total(TenantCreationMetric::class, function (Builder $query) {
            $query->whereBetween('created_at', [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()]);
        });
        $registrationDifference = Number::percentageDifference($previousRegistrations, $currentRegistrations);
        $registrations = new TenantCreationMetric;
        $registrationData = Trend::query($registrations->query())
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->sum('count');

        $icon = function ($number): string {
            return match (true) {
                $number >= 0 => 'heroicon-m-arrow-trending-up',
                default => 'heroicon-m-arrow-trending-down'
            };
        };

        return [
            Stat::make('Monthly user logins', Number::format($currentUserLogin))
                ->color('gray')
                ->description(Number::percentage($userLoginDifference))
                ->descriptionIcon($icon($userLoginDifference))
                ->chart($userLoginData->map(fn (TrendValue $value) => $value->aggregate)->toArray()),
            Stat::make('Monthly new users', Number::format($currentUserCreation))
                ->color('gray')
                ->description(Number::percentage($userCreationDifference))
                ->descriptionIcon($icon($userCreationDifference))
                ->chart($userCreationData->map(fn (TrendValue $value) => $value->aggregate)->toArray()),
            Stat::make('Quarterly registrations', Number::format($currentRegistrations))
                ->color('gray')
                ->description(Number::percentage($registrationDifference))
                ->descriptionIcon($icon($registrationDifference))
                ->chart($registrationData->map(fn (TrendValue $value) => $value->aggregate)->toArray()),
        ];
    }
}
