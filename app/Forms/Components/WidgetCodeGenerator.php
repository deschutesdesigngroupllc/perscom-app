<?php

declare(strict_types=1);

namespace App\Forms\Components;

use Filament\Forms\Components\Field;
use Filament\Support\Concerns\HasColor;

class WidgetCodeGenerator extends Field
{
    use HasColor;

    protected string $view = 'forms.components.widget-code-generator';
}
