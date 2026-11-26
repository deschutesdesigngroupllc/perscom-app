<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Write field, entry, column, and filter labels in Title Case.
 */
class Label
{
    /**
     * The words kept lowercase unless they open or close the label.
     */
    public const array MINOR_WORDS = [
        'a',
        'an',
        'and',
        'as',
        'at',
        'but',
        'by',
        'for',
        'from',
        'in',
        'nor',
        'of',
        'on',
        'or',
        'per',
        'the',
        'to',
        'via',
        'vs',
        'with',
    ];

    /**
     * The words always written this way, keyed by their lowercase form.
     */
    public const array ACRONYMS = [
        'ai' => 'AI',
        'api' => 'API',
        'css' => 'CSS',
        'html' => 'HTML',
        'ics' => 'ICS',
        'id' => 'ID',
        'ids' => 'IDs',
        'ip' => 'IP',
        'json' => 'JSON',
        'mcp' => 'MCP',
        'mfa' => 'MFA',
        'oauth' => 'OAuth',
        'rss' => 'RSS',
        'rsvp' => 'RSVP',
        'sms' => 'SMS',
        'url' => 'URL',
        'urls' => 'URLs',
        'uuid' => 'UUID',
    ];

    /**
     * Derive a Title Case label from an attribute name such as "first_name".
     *
     * A foreign key like "author_id" is labeled after what it points to ("Author").
     */
    public static function fromName(string $name): string
    {
        return static::titleCase((string) Str::of($name)
            ->replaceMatches('/(.)(?:_id|Id)$/', '$1')
            ->kebab()
            ->replace(['-', '_'], ' '));
    }

    /**
     * Write a label in Title Case, keeping acronyms and minor words as listed.
     */
    public static function titleCase(string $label): string
    {
        $words = Str::of($label)->trim()->explode(' ');
        $wordIndexes = $words->filter(fn (string $word): bool => filled($word))->keys();
        $firstIndex = $wordIndexes->first();
        $lastIndex = $wordIndexes->last();

        return $words
            ->map(fn (string $word, int $index): string => static::titleCaseWord(
                $word,
                isEdge: $index === $firstIndex || $index === $lastIndex,
            ))
            ->implode(' ');
    }

    /**
     * Write one word of a label in Title Case.
     */
    protected static function titleCaseWord(string $word, bool $isEdge): string
    {
        $lower = Str::lower($word);

        return match (true) {
            Arr::exists(static::ACRONYMS, $lower) => static::ACRONYMS[$lower],
            ! $isEdge && collect(static::MINOR_WORDS)->contains($lower) => $lower,
            default => Str::ucfirst($word),
        };
    }
}
