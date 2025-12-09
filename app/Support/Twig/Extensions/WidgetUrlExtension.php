<?php

declare(strict_types=1);

namespace App\Support\Twig\Extensions;

use Illuminate\Container\Attributes\Config;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class WidgetUrlExtension extends AbstractExtension
{
    public function __construct(
        #[Config('app.widget_url')]
        private readonly string $widgetUrl,
    ) {
        //
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('widgetUrl', [$this, 'widgetUrl']),
        ];
    }

    public function widgetUrl(): ?string
    {
        return $this->widgetUrl;
    }
}
