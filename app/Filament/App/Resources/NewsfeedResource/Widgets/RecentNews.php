<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\NewsfeedResource\Widgets;

use App\Filament\App\Resources\NewsfeedResource;
use App\Filament\App\Widgets\RecentPostsWidget;
use App\Models\Newsfeed;
use App\Models\User;
use App\Support\Brand;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class RecentNews extends RecentPostsWidget
{
    use HasWidgetShield;

    protected static ?int $sort = -2;

    public function heading(): string
    {
        return 'Recent News';
    }

    public function description(): string
    {
        return 'The latest from across your organization.';
    }

    public function icon(): string|BackedEnum
    {
        return Heroicon::OutlinedNewspaper;
    }

    public function emptyHeading(): string
    {
        return 'No news yet';
    }

    public function emptyDescription(): string
    {
        return 'News posted to your organization will show up here.';
    }

    public function allPostsLabel(): string
    {
        return 'View All News';
    }

    public function allPostsUrl(): ?string
    {
        return NewsfeedResource::canViewAny() ? NewsfeedResource::getUrl() : null;
    }

    /**
     * @return array<int, Model>
     */
    protected function records(): array
    {
        return Newsfeed::query()->with('causer')->latest()->limit(static::LIMIT)->get()->all();
    }

    protected function findRecord(mixed $key): ?Model
    {
        return Newsfeed::query()->with('causer')->find($key);
    }

    protected function titleFor(Model $record): string
    {
        /** @var Newsfeed $record */
        return (string) $record->headline;
    }

    protected function bodyFor(Model $record): string
    {
        /** @var Newsfeed $record */
        return (string) $record->text;
    }

    protected function colorFor(Model $record): string
    {
        /** @var Newsfeed $record */
        return $record->color ?: Brand::PRIMARY_HEX;
    }

    protected function authorFor(Model $record): ?User
    {
        /** @var Newsfeed $record */
        return $record->causer instanceof User ? $record->causer : null;
    }
}
