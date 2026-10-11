<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Discord;

use App\Support\Discord\DiscordEmbed;
use Illuminate\Support\Str;
use Tests\TestCase;

class DiscordEmbedTest extends TestCase
{
    public function test_it_builds_a_discord_embed_object(): void
    {
        $timestamp = now()->startOfSecond();

        $embed = DiscordEmbed::make()
            ->title('New Rank Record')
            ->description('Promoted for outstanding service.')
            ->url('https://example.com/records/1')
            ->color('#f59e0b')
            ->timestamp($timestamp)
            ->thumbnail('https://example.com/rank.png')
            ->author('Jane Doe', 'https://example.com/users/1', 'https://example.com/avatar.png')
            ->footer('PERSCOM')
            ->field('Rank', 'Sergeant')
            ->field('Type', 'Promotion', inline: false)
            ->toArray();

        $this->assertSame([
            'title' => 'New Rank Record',
            'description' => 'Promoted for outstanding service.',
            'url' => 'https://example.com/records/1',
            'color' => 0xF59E0B,
            'timestamp' => $timestamp->toIso8601String(),
            'thumbnail' => ['url' => 'https://example.com/rank.png'],
            'author' => [
                'name' => 'Jane Doe',
                'url' => 'https://example.com/users/1',
                'icon_url' => 'https://example.com/avatar.png',
            ],
            'footer' => ['text' => 'PERSCOM'],
            'fields' => [
                ['name' => 'Rank', 'value' => 'Sergeant', 'inline' => true],
                ['name' => 'Type', 'value' => 'Promotion', 'inline' => false],
            ],
        ], $embed);
    }

    public function test_it_omits_empty_values(): void
    {
        $embed = DiscordEmbed::make()
            ->title('Title')
            ->description('')
            ->url(null)
            ->thumbnail(null)
            ->author(null)
            ->footer('')
            ->field('Unit', null)
            ->field('Position', '')
            ->field(null, 'Value')
            ->toArray();

        $this->assertSame(['title' => 'Title'], $embed);
    }

    public function test_it_accepts_integer_colors(): void
    {
        $this->assertSame(['color' => 255], DiscordEmbed::make()->color(255)->toArray());
    }

    public function test_it_truncates_values_to_discord_limits(): void
    {
        $embed = DiscordEmbed::make()
            ->title(Str::repeat('a', 300))
            ->description(Str::repeat('b', 5000))
            ->field(Str::repeat('c', 300), Str::repeat('d', 2000))
            ->toArray();

        $this->assertLessThanOrEqual(DiscordEmbed::TITLE_LIMIT, mb_strlen($embed['title']));
        $this->assertLessThanOrEqual(DiscordEmbed::DESCRIPTION_LIMIT, mb_strlen($embed['description']));
        $this->assertLessThanOrEqual(DiscordEmbed::FIELD_NAME_LIMIT, mb_strlen($embed['fields'][0]['name']));
        $this->assertLessThanOrEqual(DiscordEmbed::FIELD_VALUE_LIMIT, mb_strlen($embed['fields'][0]['value']));
    }

    public function test_it_limits_the_number_of_fields(): void
    {
        $embed = DiscordEmbed::make();

        collect(range(1, 30))->each(fn (int $index): DiscordEmbed => $embed->field('Field '.$index, 'Value'));

        $this->assertCount(DiscordEmbed::FIELD_COUNT_LIMIT, $embed->toArray()['fields']);
    }
}
