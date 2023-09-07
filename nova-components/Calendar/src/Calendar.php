<?php

namespace Perscom\Calendar;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class Calendar extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('calendar', __DIR__.'/../dist/js/tool.js');
        Nova::style('calendar', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     *
     * @return mixed
     */
    public function menu(Request $request)
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
