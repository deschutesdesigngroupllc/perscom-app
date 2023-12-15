<?php

namespace Perscom\Forms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class Forms extends Tool
{
    public function boot(): void
    {
        Nova::script('forms', __DIR__.'/../dist/js/tool.js');
        Nova::style('forms', __DIR__.'/../dist/css/tool.css');
    }

    public function menu(Request $request): MenuSection
    {
        return MenuSection::make('Forms')
            ->path('/forms')
            ->icon('server');
    }

    public static function generateJwt()
    {
        return Auth::guard('jwt')->claims([
            'scope' => [
                'create:submission',
                'update:field',
                'view:form',
                'view:submission',
            ],
        ])->login(Auth::guard('web')->user());
    }
}
