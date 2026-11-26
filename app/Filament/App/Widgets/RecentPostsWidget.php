<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Data\Widgets\RecentPostData;
use App\Models\User;
use BackedEnum;
use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Show the latest posts, featuring the newest and opening each in a modal.
 */
abstract class RecentPostsWidget extends Widget implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public const int LIMIT = 5;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.recent-posts-widget';

    abstract public function heading(): string;

    abstract public function description(): string;

    abstract public function icon(): string|BackedEnum;

    abstract public function emptyHeading(): string;

    abstract public function emptyDescription(): string;

    abstract public function allPostsLabel(): string;

    abstract public function allPostsUrl(): ?string;

    /**
     * Get the member's private RSS link for this feed.
     */
    abstract public function rssFeedUrl(): ?string;

    /**
     * Get the latest records to show, newest first.
     *
     * @return array<int, Model>
     */
    abstract protected function records(): array;

    abstract protected function findRecord(mixed $key): ?Model;

    abstract protected function titleFor(Model $record): string;

    abstract protected function bodyFor(Model $record): string;

    /**
     * Get the post's accent color, or null to show a neutral gray accent.
     */
    abstract protected function colorFor(Model $record): ?string;

    /**
     * Build the posts the widget shows, newest first.
     *
     * @return array<int, RecentPostData>
     */
    public function posts(): array
    {
        return collect($this->records())->map(fn (Model $record): RecentPostData => new RecentPostData(
            key: $record->getKey(),
            title: $this->titleFor($record),
            excerpt: Str::of($this->bodyFor($record))->stripTags()->squish()->limit(280)->toString(),
            color: $this->colorFor($record),
            author: $this->authorFor($record),
            date: $this->dateFor($record),
            note: $this->noteFor($record),
        ))->all();
    }

    public function viewAction(): Action
    {
        return Action::make('view')
            ->record(fn (array $arguments): ?Model => $this->findRecord($arguments['record'] ?? null))
            ->hidden(fn (?Model $record): bool => ! $record instanceof Model)
            ->modalHeading(fn (Model $record): string => $this->titleFor($record))
            ->modalDescription(fn (Model $record): string => collect([
                $this->authorFor($record)?->name,
                $this->dateFor($record)?->toDayDateTimeString(),
            ])->filter()->implode(' · '))
            ->modalWidth('2xl')
            ->modalSubmitAction(false)
            ->modalCancelActionLabel('Close')
            ->schema([
                TextEntry::make('body')
                    ->hiddenLabel()
                    ->state(fn (Model $record): string => $this->bodyFor($record))
                    ->html(),
            ]);
    }

    protected function authorFor(Model $record): ?User
    {
        return null;
    }

    protected function dateFor(Model $record): ?CarbonInterface
    {
        $date = $record->getAttribute('created_at');

        return $date instanceof CarbonInterface ? $date : null;
    }

    protected function noteFor(Model $record): ?string
    {
        return null;
    }
}
