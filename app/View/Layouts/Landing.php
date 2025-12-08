<?php

declare(strict_types=1);

namespace App\View\Layouts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Landing extends Component
{
    public function render(): View|Closure|string
    {
        return view('components.layouts.landing');
    }
}
