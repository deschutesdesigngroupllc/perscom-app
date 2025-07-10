<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\LaravelSettings\Models\SettingsProperty;

/**
 * @property int $id
 * @property string|null $group
 * @property string $name
 * @property string|null $payload
 * @property bool $locked
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Settings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Settings query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Settings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Settings whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Settings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Settings whereLocked($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Settings whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Settings wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Settings whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Settings extends SettingsProperty
{
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
}
