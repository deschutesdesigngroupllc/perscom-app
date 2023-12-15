<?php

namespace Perscom\Roster;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class Roster extends Tool
{
    public function boot(): void
    {
        Nova::script('roster', __DIR__.'/../dist/js/tool.js');
        Nova::style('roster', __DIR__.'/../dist/css/tool.css');
    }

    public function menu(Request $request): MenuSection
    {
        return MenuSection::make('Roster')->path('/roster')->icon('user-group');
    }

    public static function generateJwt()
    {
        return Auth::guard('jwt')->claims([
            'scope' => [
                'view:assignmentrecord',
                'view:awardrecord',
                'view:combatrecord',
                'view:field',
                'view:group',
                'view:position',
                'view:qualificationrecord',
                'view:rankrecord',
                'view:servicerecord',
                'view:specialty',
                'view:unit',
                'view:user',
            ],
        ])->login(Auth::guard('web')->user());
    }
}
