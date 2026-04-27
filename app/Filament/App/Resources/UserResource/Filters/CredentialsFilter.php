<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Filters;

use App\Models\Credential;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class CredentialsFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('credentials')
            ->label('Credentials')
            ->multiple()
            ->preload()
            ->options(fn (): array => Credential::query()->orderBy('name')->pluck('name', 'id')->all())
            ->query(function (Builder $query, array $data): Builder {
                if (blank($data['values'] ?? [])) {
                    return $query;
                }

                return $query->whereHas('credentials', function (Builder $query) use ($data): void {
                    $query->whereIn('credentials.id', $data['values']);
                });
            });
    }
}
