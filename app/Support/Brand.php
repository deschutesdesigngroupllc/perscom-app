<?php

declare(strict_types=1);

namespace App\Support;

use Filament\Support\Colors\Color;

class Brand
{
    /**
     * Brand blue, the same as perscom.io's brand-600: used for actions only, never to signal a status.
     */
    public const string PRIMARY_HEX = '#2563EB';

    /**
     * Brass: reserved for insignia moments (logo stars, ranks, awards, promotion eligibility).
     */
    public const string ACCENT_HEX = '#C9A24B';

    /**
     * The neutral used when a status or category has no color of its own.
     */
    public const string NEUTRAL_HEX = '#71717A';

    /**
     * One identity color per record type, shared by Discord embeds, notifications and Copilot.
     *
     * @var array<string, string>
     */
    public const array CATEGORY_COLORS = [
        'rank' => '#C9A24B',
        'award' => '#D97706',
        'assignment' => '#2563EB',
        'qualification' => '#0D9488',
        'training' => '#0284C7',
        'combat' => '#B91C1C',
        'service' => '#4F46E5',
    ];

    /**
     * Semantic color meanings: success = completed or healthy, warning = needs attention or time-boxed,
     * danger = failed or blocked, info = in progress, gray = ended, opted out or none. Warnings share
     * the brass ramp so attention states read as the brand's gold; brass is also used on its own for
     * insignia moments (new, rank, awards, promotion eligibility).
     *
     * @return array<string, array<int, string>>
     */
    public static function colors(): array
    {
        return [
            'primary' => Color::Blue,
            'gray' => Color::Zinc,
            'info' => Color::Sky,
            'success' => Color::Emerald,
            'warning' => Color::hex(self::ACCENT_HEX),
            'danger' => Color::Red,
            'brass' => Color::hex(self::ACCENT_HEX),
        ];
    }

    public static function categoryColor(string $category): string
    {
        return self::CATEGORY_COLORS[$category] ?? self::NEUTRAL_HEX;
    }
}
