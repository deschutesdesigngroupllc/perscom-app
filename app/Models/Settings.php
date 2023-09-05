<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

/**
 * App\Models\Settings
 *
 * @property string $key
 * @property string|null $value
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings query()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereValue($value)
 *
 * @mixin \Eloquent
 */
class Settings extends \Outl1ne\NovaSettings\Models\Settings
{
    use HasFactory;

    public static function getValueForKey($key)
    {
        if (Request::isCentralRequest()) {
            return null;
        }

        return parent::getValueForKey($key);
    }
}
