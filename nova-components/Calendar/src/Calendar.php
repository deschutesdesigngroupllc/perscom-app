<?php

namespace Perscom\Calendar;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class Calendar extends Tool
{
    public function boot(): void
    {
        Nova::script('calendar', __DIR__.'/../dist/js/tool.js');
        Nova::style('calendar', __DIR__.'/../dist/css/tool.css');
    }

    public function menu(Request $request): MenuSection
    {
        return MenuSection::make('Calendar')
            ->path('/calendar')
            ->icon('server');
    }

    public static function generateJwt()
    {
        return Auth::guard('jwt')->claims([
            'scope' => [
                'view:calendar',
                'view:event',
            ],
        ])->login(Auth::guard('web')->user());
    }
}
