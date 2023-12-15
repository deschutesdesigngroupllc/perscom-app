<?php

namespace Perscom\DashboardTitle;

use Closure;
use Laravel\Nova\Card;

class DashboardTitle extends Card
{
    /**
     * @var string
     */
    public $width = self::FULL_WIDTH;

    /**
     * @var string
     */
    public $height = self::DYNAMIC_HEIGHT;

    public function component(): string
    {
        return 'dashboard-title';
    }

    public function withTitle(mixed $title): DashboardTitle
    {
        if ($title instanceof Closure) {
            return $this->withMeta([
                'title' => $title(),
            ]);
        }

        return $this->withMeta([
            'title' => $title,
        ]);
    }

    public function withSubtitle(mixed $subtitle): DashboardTitle
    {
        if ($subtitle instanceof Closure) {
            return $this->withMeta([
                'subtitle' => $subtitle(),
            ]);
        }

        return $this->withMeta([
            'subtitle' => $subtitle,
        ]);
    }
}
