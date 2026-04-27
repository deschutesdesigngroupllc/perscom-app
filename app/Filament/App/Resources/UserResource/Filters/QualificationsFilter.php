<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\UserResource\Filters;

use App\Models\Qualification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class QualificationsFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('qualifications')
            ->label('Qualifications')
            ->multiple()
            ->preload()
            ->options(fn (): array => Qualification::query()->orderBy('name')->pluck('name', 'id')->all())
            ->query(function (Builder $query, array $data): Builder {
                if (blank($data['values'] ?? [])) {
                    return $query;
                }

                return $query->whereHas('qualifications', function (Builder $query) use ($data): void {
                    $query->whereIn('qualifications.id', $data['values']);
                });
            });
    }
}
