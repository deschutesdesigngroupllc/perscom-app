<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Concerns\TenantConnection;

class Settings extends \Outl1ne\NovaSettings\Models\Settings
{
    use TenantConnection;
    use HasFactory;

    /**
     * @param $key
     *
     * @return null
     */
    public static function getValueForKey($key)
    {
        if (Request::isCentralRequest()) {
            return null;
        }

        return parent::getValueForKey($key);
    }
}
