<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Filters;

use App\Models\Award;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class AwardsFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('awards')
            ->label('Awards')
            ->multiple()
            ->preload()
            ->options(fn (): array => Award::query()->orderBy('name')->pluck('name', 'id')->all())
            ->query(function (Builder $query, array $data): Builder {
                if (blank($data['values'] ?? [])) {
                    return $query;
                }

                return $query->whereHas('awards', function (Builder $query) use ($data): void {
                    $query->whereIn('awards.id', $data['values']);
                });
            });
    }
}
