<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Actions\Features\StartFeature;
use App\Actions\Features\StopFeature;
use App\Contracts\PremiumFeature;
use App\Filament\App\Clusters\Settings;
use App\Models\Feature;
use App\Models\Tenant;
use App\Settings\FeatureSettings;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Number;
use Laravel\Pennant\Feature as Pennant;

class Features extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static string $view = 'filament.app.clusters.settings.pages.features';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        /** @var Tenant $tenant * */
        $tenant = Filament::getTenant();

        return parent::canAccess()
            && $tenant->subscribed()
            && ! $tenant->onTrial()
            && ! App::isDemo()
            && ! App::isAdmin()
            && Auth::user()->hasRole(Utils::getSuperAdminName());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Feature::query())
            ->description('Manage your account addons and features here.')
            ->columns([
                TextColumn::make('name')
                    ->sortable(),
                TextColumn::make('description')
                    ->wrap()
                    ->sortable(),
                TextColumn::make('price')
                    ->badge()
                    ->color('info')
                    ->formatStateUsing(fn ($state) => Number::currency($state))
                    ->sortable(),
                TextColumn::make('term')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
            ])
            ->actions([
                Action::make('subscribe')
                    ->color('success')
                    ->icon('heroicon-o-credit-card')
                    ->successNotificationTitle('The feature has been successfully added to your account and your card on file has been charged. You may cancel at anytime.')
                    ->failureNotificationTitle('We were unable to charge your card on file. Please check your payment method and try again.')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Feature $record) => "Subscribe to $record->name")
                    ->modalSubmitActionLabel('Subscribe')
                    ->modalDescription(function (Feature $record) {
                        $price = Number::currency($record->price);

                        return "We will charge your card on file $price {$record->term->value}. Please confirm you would like to proceed.";
                    })
                    ->visible(function (Feature $record) {
                        /** @var PremiumFeature|string $feature */
                        $feature = $record->feature;

                        return Pennant::inactive($feature) && $feature::canSubscribe();
                    })
                    ->action(function (Action $action, Feature $record) {
                        /** @var Tenant $tenant */
                        $tenant = Filament::getTenant();

                        $result = StartFeature::handle($tenant, $record);

                        if (! $result) {
                            $action->failure();
                        } else {
                            $action->success();
                        }
                    }),
                Action::make('unsubscribe')
                    ->color('danger')
                    ->icon('heroicon-o-credit-card')
                    ->successNotificationTitle('The feature has been successfully stopped. Any unused time will be credited on your next invoice. You can resubscribe at anytime to resume using the features.')
                    ->failureNotificationTitle('We were unable to stop the subscription. Please reach out to support to assist.')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Feature $record) => "Unsubscribe from $record->name")
                    ->modalSubmitActionLabel('Unsubscribe')
                    ->modalDescription(fn (Feature $record) => "Are you sure you would like to unsubscribe from $record->name? Any unused time will be credited on your next invoice.")
                    ->visible(function (Feature $record) {
                        /** @var PremiumFeature|string $feature */
                        $feature = $record->feature;

                        return Pennant::active($feature) && $feature::canUnsubscribe();
                    })
                    ->action(function (Action $action, Feature $record) {
                        /** @var Tenant $tenant */
                        $tenant = Filament::getTenant();

                        $result = StopFeature::handle($tenant, $record);

                        if (! $result) {
                            $action->failure();
                        } else {
                            $action->success();
                        }
                    }),
                Action::make('settings')
                    ->visible(fn (Feature $record) => Pennant::active($record->feature))
                    ->color('gray')
                    ->icon('heroicon-o-cog')
                    ->fillForm(function (Feature $record) {
                        /** @var PremiumFeature $feature */
                        $feature = $record->feature;
                        $key = $feature::settingsKey();

                        /** @var FeatureSettings $settings */
                        $settings = app()->make(FeatureSettings::class);

                        return $settings->$key;
                    })
                    ->mutateFormDataUsing(function (Feature $record, array $data) {
                        /** @var PremiumFeature $feature */
                        $feature = $record->feature;
                        $key = $feature::settingsKey();

                        /** @var FeatureSettings $settings */
                        $settings = app()->make(FeatureSettings::class);
                        $settings->$key = $data;
                        $settings->save();

                        return $data;
                    })
                    ->modalSubmitActionLabel('Save')
                    ->successNotificationTitle('Saved')
                    ->action(fn (Action $action) => $action->success())
                    ->form(function (Feature $record) {
                        /** @var PremiumFeature $feature */
                        $feature = $record->feature;

                        return $feature::settingsForm();
                    }),
            ]);
    }
}
