<?php

namespace Perscom\Newsfeed;

use App\Models\Permission;
use Illuminate\Support\Facades\Auth;
use Laravel\Nova\Card;

class Newsfeed extends Card
{
    /**
     * @var string
     */
    public $width = '2/3';

    public function __construct($component = null)
    {
        parent::__construct($component);

        $this->withMeta([
            'jwt' => Auth::guard('jwt')->claims([
                'scope' => Auth::guard('web')
                    ->user()
                    ->getAllPermissions()
                    ->map(fn (Permission $permission) => $permission->name)
                    ->toArray(),
            ])->login(Auth::guard('web')->user()),
            'tenant_id' => tenant()->getTenantKey(),
            'widget_url' => env('WIDGET_URL'),
        ]);
    }

    public function component(): string
    {
        return 'newsfeed';
    }
}
