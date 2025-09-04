<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TenantResource\RelationManagers;

use App\Filament\Admin\Resources\TenantResource;
use App\Models\Enums\StripeStatus;
use App\Models\Subscription;
use App\Models\Tenant;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    protected static string|BackedEnum|null $icon = 'heroicon-o-credit-card';

    /**
     * @param  Tenant  $ownerRecord
     */
    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->subscription_plan->getLabel();
    }

    /**
     * @param  Tenant  $ownerRecord
     */
    public static function getBadgeColor(Model $ownerRecord, string $pageClass): ?string
    {
        return $ownerRecord->subscription_plan->getColor();
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('The subscriptions associated with the tenant.')
            ->emptyStateDescription('The tenant has no subscriptions.')
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('type')
                    ->sortable(),
                TextColumn::make('stripe_id')
                    ->label('Stripe ID')
                    ->sortable(),
                TextColumn::make('stripe_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state): string|array|null => StripeStatus::from($state)->getColor())
                    ->formatStateUsing(fn ($state): ?string => StripeStatus::from($state)->getLabel())
                    ->sortable(),
                TextColumn::make('stripe_price')
                    ->label('Price')
                    ->sortable(),
                TextColumn::make('plan')
                    ->getStateUsing(fn (?Subscription $record) => optional($record->owner)->sparkPlan()->name ?? null),
                TextColumn::make('quantity')
                    ->sortable(),
                TextColumn::make('trial_ends_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('stripe')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color(Color::generateV3Palette('#5167FC'))
                    ->openUrlInNewTab()
                    ->url(fn (Subscription $record) => $record->stripe_url),
                DeleteAction::make(),
            ])
            ->headerActions([
                Action::make('billing')
                    ->color(Color::generateV3Palette('#5167FC'))
                    ->visible(function () {
                        /** @var Tenant $tenant */
                        $tenant = $this->getOwnerRecord();

                        return $tenant->hasStripeId();
                    })
                    ->openUrlInNewTab()
                    ->url(function () {
                        /** @var Tenant $tenant */
                        $tenant = $this->getOwnerRecord();

                        return $tenant->billingPortalUrl(TenantResource::getUrl('edit', [
                            'record' => $tenant,
                        ]));
                    }),
            ]);
    }

    protected function can(string $action, ?Model $record = null): bool
    {
        return match ($action) {
            'edit', 'create' => false,
            default => parent::can($action, $record),
        };
    }
}
