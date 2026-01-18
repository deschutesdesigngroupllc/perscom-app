<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AutomationLogResource\Widgets;

use App\Filament\App\Resources\AutomationResource;
use App\Services\AutomationService;
use Filament\Widgets\Widget;

class AutomationTemplatesWidget extends Widget
{
    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.automation-templates-widget';

    /**
     * @return array<string, array{name: string, description: string, category: string, prerequisites?: list<string>, data: array<string, mixed>}>
     */
    public function getTemplates(): array
    {
        return AutomationService::getTemplates();
    }

    public function getCreateUrl(string $templateKey): string
    {
        return AutomationResource::getUrl('create', ['template' => $templateKey]);
    }
}
