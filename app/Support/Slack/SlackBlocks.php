<?php

declare(strict_types=1);

namespace App\Support\Slack;

use Illuminate\Support\Str;

/**
 * Turns the Discord embeds every notification already builds into Slack Block Kit, so both
 * services show the same title, details, image and link.
 */
final class SlackBlocks
{
    /**
     * @param  array<string, mixed>  $embed
     * @return array{text: string, blocks: array<int, array<string, mixed>>}
     */
    public static function fromDiscordEmbed(array $embed, ?string $fallback = null): array
    {
        $title = (string) data_get($embed, 'title', '');
        $description = (string) data_get($embed, 'description', '');
        $author = data_get($embed, 'author.name');

        $blocks = collect();

        if (filled($author)) {
            $blocks->push(['type' => 'context', 'elements' => [['type' => 'mrkdwn', 'text' => SlackBlocks::escape((string) $author)]]]);
        }

        if (filled($title)) {
            $blocks->push(['type' => 'header', 'text' => ['type' => 'plain_text', 'text' => Str::limit($title, 147), 'emoji' => true]]);
        }

        $section = array_filter([
            'type' => 'section',
            'text' => filled($description) ? ['type' => 'mrkdwn', 'text' => Str::limit(SlackBlocks::markdown($description), 2997)] : null,
            'fields' => collect((array) data_get($embed, 'fields', []))
                ->take(10)
                ->map(fn (array $field): array => [
                    'type' => 'mrkdwn',
                    'text' => Str::limit(sprintf("*%s*\n%s", SlackBlocks::escape((string) data_get($field, 'name')), SlackBlocks::markdown((string) data_get($field, 'value'))), 1997),
                ])
                ->values()
                ->all() ?: null,
            'accessory' => filled(data_get($embed, 'thumbnail.url'))
                ? ['type' => 'image', 'image_url' => data_get($embed, 'thumbnail.url'), 'alt_text' => Str::limit($title ?: 'Image', 1997)]
                : null,
        ]);

        if (count($section) > 1) {
            $blocks->push($section);
        }

        if (filled(data_get($embed, 'image.url'))) {
            $blocks->push(['type' => 'image', 'image_url' => data_get($embed, 'image.url'), 'alt_text' => Str::limit($title ?: 'Image', 1997)]);
        }

        if (filled(data_get($embed, 'url'))) {
            $blocks->push(['type' => 'actions', 'elements' => [[
                'type' => 'button',
                'text' => ['type' => 'plain_text', 'text' => 'Open in PERSCOM'],
                'url' => data_get($embed, 'url'),
            ]]]);
        }

        if (filled(data_get($embed, 'footer.text'))) {
            $blocks->push(['type' => 'context', 'elements' => [['type' => 'mrkdwn', 'text' => SlackBlocks::escape((string) data_get($embed, 'footer.text'))]]]);
        }

        return [
            'text' => Str::limit($fallback ?? ($title ?: Str::limit($description, 140)), 3000),
            'blocks' => $blocks->values()->all(),
        ];
    }

    /**
     * Converts Discord's Markdown to Slack's mrkdwn: **bold** becomes *bold* and [text](url) becomes <url|text>.
     */
    public static function markdown(string $text): string
    {
        return Str::of(SlackBlocks::escape($text))
            ->replaceMatches('/\*\*(.+?)\*\*/s', '*$1*')
            ->replaceMatches('/\[([^\]]+)\]\((https?:\/\/[^)\s]+)\)/', '<$2|$1>')
            ->toString();
    }

    public static function escape(string $text): string
    {
        return str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $text);
    }
}
