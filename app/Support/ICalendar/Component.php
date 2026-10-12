<?php

declare(strict_types=1);

namespace App\Support\ICalendar;

use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;

/**
 * An iCalendar (RFC 5545) component such as a VEVENT, holding content lines and nested components.
 */
class Component
{
    use Conditionable;

    /**
     * @var array<int, string>
     */
    protected array $lines = [];

    /**
     * @var array<int, Component>
     */
    protected array $components = [];

    final public function __construct(public readonly string $name) {}

    public static function make(string $name): static
    {
        return new static(Str::upper($name));
    }

    /**
     * Escape a TEXT value: backslashes, semicolons, commas and newlines.
     */
    public static function escape(string $value): string
    {
        return str_replace(
            ['\\', ';', ',', "\r\n", "\r", "\n"],
            ['\\\\', '\;', '\\,', '\\n', '\\n', '\\n'],
            $value,
        );
    }

    /**
     * Fold a content line so no physical line exceeds 75 octets. Continuation lines begin with a
     * single space and multibyte characters are never split.
     */
    public static function fold(string $line): string
    {
        if (strlen($line) <= 75) {
            return $line;
        }

        $folded = collect(mb_str_split($line))->reduce(function (array $carry, string $character): array {
            $limit = count($carry) === 1 ? 75 : 74;
            $current = (string) end($carry);

            if (strlen($current) + strlen($character) > $limit) {
                $carry[] = $character;
            } else {
                $carry[array_key_last($carry)] = $current.$character;
            }

            return $carry;
        }, ['']);

        return implode("\r\n ", $folded);
    }

    public static function formatUtcDateTime(CarbonInterface $value): string
    {
        return $value->copy()->utc()->format('Ymd\THis\Z');
    }

    public static function formatLocalDateTime(CarbonInterface $value, string $timezone): string
    {
        return $value->copy()->setTimezone($timezone)->format('Ymd\THis');
    }

    public static function formatDate(CarbonInterface $value): string
    {
        return $value->format('Ymd');
    }

    /**
     * Add a property whose value is already in iCalendar format.
     *
     * @param  array<string, string>  $parameters
     */
    public function property(string $name, string $value, array $parameters = []): static
    {
        $this->lines[] = Str::upper($name).static::formatParameters($parameters).':'.$value;

        return $this;
    }

    /**
     * Add a text property, escaping its value. Blank values are skipped.
     *
     * @param  array<string, string>  $parameters
     */
    public function text(string $name, ?string $value, array $parameters = []): static
    {
        if (blank($value)) {
            return $this;
        }

        return $this->property($name, static::escape($value), $parameters);
    }

    /**
     * Add a date-time property. Without a timezone the value is written in UTC, otherwise as local
     * time in the given IANA timezone with a TZID parameter.
     */
    public function dateTime(string $name, CarbonInterface $value, ?string $timezone = null): static
    {
        return filled($timezone)
            ? $this->property($name, static::formatLocalDateTime($value, $timezone), ['TZID' => $timezone])
            : $this->property($name, static::formatUtcDateTime($value));
    }

    /**
     * Add a date property (VALUE=DATE), as used by all-day events.
     */
    public function date(string $name, CarbonInterface $value): static
    {
        return $this->property($name, static::formatDate($value), ['VALUE' => 'DATE']);
    }

    public function component(Component $component): static
    {
        $this->components[] = $component;

        return $this;
    }

    /**
     * @param  iterable<int, Component>  $components
     */
    public function components(iterable $components): static
    {
        collect($components)->each(fn (Component $component): static => $this->component($component));

        return $this;
    }

    /**
     * The unfolded content lines of the component, including its nested components.
     *
     * @return Collection<int, string>
     */
    public function lines(): Collection
    {
        return collect(['BEGIN:'.$this->name])
            ->merge($this->lines)
            ->merge(collect($this->components)->flatMap(fn (Component $component): Collection => $component->lines()))
            ->push('END:'.$this->name);
    }

    /**
     * Render the component as folded, CRLF-terminated content lines.
     */
    public function render(): string
    {
        return $this->lines()
            ->map(fn (string $line): string => static::fold($line)."\r\n")
            ->implode('');
    }

    /**
     * @param  array<string, string>  $parameters
     */
    protected static function formatParameters(array $parameters): string
    {
        return collect($parameters)
            ->filter(fn (string $value): bool => filled($value))
            ->map(fn (string $value, string $key): string => sprintf(
                ';%s=%s',
                Str::upper($key),
                Str::contains($value, [':', ';', ',']) ? '"'.str_replace('"', '', $value).'"' : $value,
            ))
            ->implode('');
    }
}
