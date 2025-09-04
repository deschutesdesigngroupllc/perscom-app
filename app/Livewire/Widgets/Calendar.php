<?php

declare(strict_types=1);

namespace App\Livewire\Widgets;

use App\Filament\App\Widgets\CalendarWidget;
use Illuminate\Support\HtmlString;

class Calendar extends CalendarWidget
{
    protected string|bool|null|HtmlString $heading = 'Calendar';

    protected bool $eventClickEnabled = false;
}
