<?php

declare(strict_types=1);

namespace App\Support\Discord;

use Carbon\CarbonInterface;
use Illuminate\Support\Str;

final class DiscordEmbed
{
    public const int TITLE_LIMIT = 256;

    public const int DESCRIPTION_LIMIT = 4096;

    public const int FIELD_NAME_LIMIT = 256;

    public const int FIELD_VALUE_LIMIT = 1024;

    public const int FIELD_COUNT_LIMIT = 25;

    public const int AUTHOR_NAME_LIMIT = 256;

    public const int FOOTER_TEXT_LIMIT = 2048;

    private ?string $title = null;

    private ?string $description = null;

    private ?string $url = null;

    private ?int $color = null;

    private ?string $timestamp = null;

    private ?string $thumbnail = null;

    /**
     * @var array{name: string, url?: string, icon_url?: string}|null
     */
    private ?array $author = null;

    /**
     * @var array{text: string, icon_url?: string}|null
     */
    private ?array $footer = null;

    /**
     * @var array<int, array{name: string, value: string, inline: bool}>
     */
    private array $fields = [];

    public static function make(): static
    {
        return new self;
    }

    public function title(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function description(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function url(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Accepts either an integer color or a hex string such as "#3b82f6".
     */
    public function color(int|string|null $color): static
    {
        $this->color = is_string($color)
            ? (int) hexdec(ltrim($color, '#'))
            : $color;

        return $this;
    }

    public function timestamp(CarbonInterface|string|null $timestamp): static
    {
        $this->timestamp = $timestamp instanceof CarbonInterface
            ? $timestamp->toIso8601String()
            : $timestamp;

        return $this;
    }

    public function thumbnail(?string $url): static
    {
        $this->thumbnail = $url;

        return $this;
    }

    public function author(?string $name, ?string $url = null, ?string $iconUrl = null): static
    {
        $this->author = blank($name) ? null : array_filter([
            'name' => Str::limit($name, self::AUTHOR_NAME_LIMIT - 3),
            'url' => $url,
            'icon_url' => $iconUrl,
        ], filled(...));

        return $this;
    }

    public function footer(?string $text, ?string $iconUrl = null): static
    {
        $this->footer = blank($text) ? null : array_filter([
            'text' => Str::limit($text, self::FOOTER_TEXT_LIMIT - 3),
            'icon_url' => $iconUrl,
        ], filled(...));

        return $this;
    }

    /**
     * Fields with a blank name or value are skipped, as Discord rejects them.
     */
    public function field(?string $name, mixed $value, bool $inline = true): static
    {
        $value = is_scalar($value) ? (string) $value : null;

        if (blank($name) || blank($value) || count($this->fields) >= self::FIELD_COUNT_LIMIT) {
            return $this;
        }

        $this->fields[] = [
            'name' => Str::limit($name, self::FIELD_NAME_LIMIT - 3),
            'value' => Str::limit($value, self::FIELD_VALUE_LIMIT - 3),
            'inline' => $inline,
        ];

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'title' => filled($this->title) ? Str::limit($this->title, self::TITLE_LIMIT - 3) : null,
            'description' => filled($this->description) ? Str::limit($this->description, self::DESCRIPTION_LIMIT - 3) : null,
            'url' => $this->url,
            'color' => $this->color,
            'timestamp' => $this->timestamp,
            'thumbnail' => filled($this->thumbnail) ? ['url' => $this->thumbnail] : null,
            'author' => $this->author,
            'footer' => $this->footer,
            'fields' => $this->fields,
        ], fn (mixed $value): bool => filled($value));
    }
}
