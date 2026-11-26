<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Turns generated field, entry, column and filter labels into Title Case so every label reads the same
 * whether it was written by hand or derived from an attribute name.
 */
class Label
{
    /**
     * Words kept lowercase unless they open or close the label.
     */
    public const array MINOR_WORDS = ['a', 'an', 'and', 'as', 'at', 'but', 'by', 'for', 'from', 'in', 'nor', 'of', 'on', 'or', 'per', 'the', 'to', 'via', 'vs', 'with'];

    /**
     * Words that should always be written this way, keyed by their lowercase form.
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
     * Derive a Title Case label from an attribute name such as "user.first_name" or "apiKey".
     */
    public static function fromName(string $name): string
    {
        return static::titleCase((string) Str::of($name)->kebab()->replace(['-', '_'], ' '));
    }

    public static function titleCase(string $label): string
    {
        $words = preg_split('/(\s+)/', trim($label), -1, PREG_SPLIT_DELIM_CAPTURE) ?: [];
        $wordIndexes = array_keys(array_filter($words, fn (string $word): bool => $word !== '' && ! ctype_space($word)));
        $firstIndex = reset($wordIndexes);
        $lastIndex = end($wordIndexes);

        foreach ($wordIndexes as $index) {
            $word = $words[$index];
            $lower = Str::lower($word);

            $words[$index] = match (true) {
                array_key_exists($lower, static::ACRONYMS) => static::ACRONYMS[$lower],
                $index !== $firstIndex && $index !== $lastIndex && in_array($lower, static::MINOR_WORDS, true) => $lower,
                default => Str::ucfirst($word),
            };
        }

        return implode('', $words);
    }
}
