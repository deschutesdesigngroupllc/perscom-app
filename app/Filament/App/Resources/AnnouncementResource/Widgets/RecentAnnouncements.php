<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AnnouncementResource\Widgets;

use App\Filament\App\Resources\AnnouncementResource;
use App\Filament\App\Widgets\RecentPostsWidget;
use App\Models\Announcement;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;

class RecentAnnouncements extends RecentPostsWidget
{
    use HasWidgetShield;

    protected static ?int $sort = -2;

    public function heading(): string
    {
        return 'Recent Announcements';
    }

    public function description(): string
    {
        return 'What leadership wants everyone to know.';
    }

    public function icon(): string|BackedEnum
    {
        return Heroicon::OutlinedMegaphone;
    }

    public function emptyHeading(): string
    {
        return 'No announcements yet';
    }

    public function emptyDescription(): string
    {
        return 'Announcements from your organization will show up here.';
    }

    public function allPostsLabel(): string
    {
        return 'View All Announcements';
    }

    public function allPostsUrl(): ?string
    {
        return AnnouncementResource::canViewAny() ? AnnouncementResource::getUrl() : null;
    }

    /**
     * @return array<int, Model>
     */
    protected function records(): array
    {
        return Announcement::query()->latest()->limit(static::LIMIT)->get()->all();
    }

    protected function findRecord(mixed $key): ?Model
    {
        return Announcement::query()->find($key);
    }

    protected function titleFor(Model $record): string
    {
        /** @var Announcement $record */
        return $record->title;
    }

    protected function bodyFor(Model $record): string
    {
        /** @var Announcement $record */
        return $record->content;
    }

    protected function colorFor(Model $record): string
    {
        /** @var Announcement $record */
        return $record->color;
    }

    protected function noteFor(Model $record): ?string
    {
        /** @var Announcement $record */
        if (! $record->expires_at) {
            return null;
        }

        return $record->expires_at->isPast()
            ? 'Expired '.$record->expires_at->diffForHumans()
            : 'Expires '.$record->expires_at->diffForHumans();
    }
}
