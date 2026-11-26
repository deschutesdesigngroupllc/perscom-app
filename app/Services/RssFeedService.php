<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Feeds\FeedItemData;
use App\Filament\App\Pages\Dashboard;
use App\Models\Announcement;
use App\Models\Newsfeed;
use App\Models\User;
use App\Settings\OrganizationSettings;
use Carbon\CarbonInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use XMLWriter;

class RssFeedService
{
    public const int LIMIT = 25;

    public static function announcementsFeedUrl(User $user): string
    {
        return route('app.rss-feeds.announcements', ['token' => CalendarFeedService::tokenFor($user)]);
    }

    public static function newsFeedUrl(User $user): string
    {
        return route('app.rss-feeds.news', ['token' => CalendarFeedService::tokenFor($user)]);
    }

    public static function announcements(): string
    {
        $items = Announcement::query()
            ->latest()
            ->limit(static::LIMIT)
            ->get()
            ->map(fn (Announcement $announcement): FeedItemData => new FeedItemData(
                guid: 'announcement-'.$announcement->getKey(),
                title: $announcement->title,
                description: $announcement->content,
                author: null,
                date: $announcement->created_at,
            ));

        return static::render(
            sprintf('%s Announcements', static::organizationName()),
            sprintf('The latest announcements from %s.', static::organizationName()),
            $items,
        );
    }

    public static function news(): string
    {
        $items = Newsfeed::query()
            ->with('causer')
            ->latest()
            ->limit(static::LIMIT)
            ->get()
            ->map(fn (Newsfeed $item): FeedItemData => new FeedItemData(
                guid: 'news-'.$item->getKey(),
                title: (string) $item->headline,
                description: (string) $item->text,
                author: $item->causer instanceof User ? $item->causer->name : null,
                date: $item->created_at,
            ));

        return static::render(
            sprintf('%s News', static::organizationName()),
            sprintf('The latest news from %s.', static::organizationName()),
            $items,
        );
    }

    /**
     * Render an RSS 2.0 document for the given feed items.
     *
     * @param  Collection<int, FeedItemData>  $items
     */
    public static function render(string $title, string $description, Collection $items): string
    {
        $link = Dashboard::getUrl(panel: 'app');

        $xml = new XMLWriter;
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('rss');
        $xml->writeAttribute('version', '2.0');
        $xml->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
        $xml->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');

        $xml->startElement('channel');
        $xml->writeElement('title', $title);
        $xml->writeElement('link', $link);
        $xml->writeElement('description', $description);
        $xml->writeElement('language', Str::replace('_', '-', (string) config('app.locale')));

        $xml->startElement('atom:link');
        $xml->writeAttribute('href', request()->url());
        $xml->writeAttribute('rel', 'self');
        $xml->writeAttribute('type', 'application/rss+xml');
        $xml->endElement();

        $latest = $items->first()?->date;

        if ($latest instanceof CarbonInterface) {
            $xml->writeElement('lastBuildDate', $latest->toRssString());
        }

        $items->each(function (FeedItemData $item) use ($xml, $link): void {
            $xml->startElement('item');
            $xml->writeElement('title', $item->title);
            $xml->writeElement('link', $link);
            $xml->writeElement('description', $item->description);

            $xml->startElement('guid');
            $xml->writeAttribute('isPermaLink', 'false');
            $xml->text($item->guid);
            $xml->endElement();

            if (filled($item->author)) {
                $xml->writeElement('dc:creator', $item->author);
            }

            if ($item->date instanceof CarbonInterface) {
                $xml->writeElement('pubDate', $item->date->toRssString());
            }

            $xml->endElement();
        });

        $xml->endElement();
        $xml->endElement();
        $xml->endDocument();

        return $xml->outputMemory();
    }

    public static function toResponse(string $feed): Response
    {
        return response($feed, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
            'Cache-Control' => 'private, max-age=900',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    protected static function organizationName(): string
    {
        /** @var OrganizationSettings $settings */
        $settings = resolve(OrganizationSettings::class);

        return filled($settings->name) ? $settings->name : (string) config('app.name');
    }
}
