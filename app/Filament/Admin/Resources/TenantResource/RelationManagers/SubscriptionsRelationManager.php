<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TenantResource\RelationManagers;

use App\Models\Subscription;
use App\Models\Tenant;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    protected static ?string $icon = 'heroicon-o-credit-card';

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
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stripe_id')
                    ->label('Stripe ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stripe_status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stripe_price')
                    ->label('Price')
                    ->sortable(),
                Tables\Columns\TextColumn::make('plan')
                    ->getStateUsing(fn (?Subscription $record) => optional($record->owner)->sparkPlan()->name ?? null),
                Tables\Columns\TextColumn::make('quantity')
                    ->sortable(),
                Tables\Columns\TextColumn::make('trial_ends_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
            ]);
    }

    protected function can(string $action, ?Model $record = null): bool
    {
        return match ($action) {
            'edit', 'delete', 'create' => false,
            default => parent::can($action, $record),
        };
    }
}
