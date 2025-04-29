<?php

declare(strict_types=1);

namespace App\View\Widgets;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public function render(): View|Closure|string
    {
        return view('widgets.layout');
    }
}
