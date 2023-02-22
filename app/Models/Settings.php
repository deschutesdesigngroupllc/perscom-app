<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class Settings extends \Outl1ne\NovaSettings\Models\Settings
{
    use HasFactory;

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::updated(function ($setting) {
            Cache::forget($setting->key);
        });

        static::deleted(function ($setting) {
            Cache::forget($setting->key);
        });
    }

    /**
     * @param $key
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
