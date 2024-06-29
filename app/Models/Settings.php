<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

/**
 * App\Models\Settings
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property string $group
 * @property string $name
 * @property int $locked
 * @property string $payload
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings query()
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Settings whereValue($value)
 *
 * @mixin \Eloquent
 */
class Settings extends \Outl1ne\NovaSettings\Models\Settings
{
    use ClearsResponseCache;
    use HasFactory;

    // @phpstan-ignore-next-line
    public static function getValueForKey($key)
    {
        if (Request::isCentralRequest()) {
            return null;
        }

        return parent::getValueForKey($key);
    }
}
