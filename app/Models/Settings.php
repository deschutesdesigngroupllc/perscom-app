<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Spatie\LaravelSettings\Models\SettingsProperty;

/**
 * @property int $id
 * @property string|null $group
 * @property string $name
 * @property string|null $value
 * @property string|null $payload
 * @property bool $locked
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @method static Builder<static>|Settings newModelQuery()
 * @method static Builder<static>|Settings newQuery()
 * @method static Builder<static>|Settings query()
 * @method static Builder<static>|Settings whereCreatedAt($value)
 * @method static Builder<static>|Settings whereGroup($value)
 * @method static Builder<static>|Settings whereId($value)
 * @method static Builder<static>|Settings whereLocked($value)
 * @method static Builder<static>|Settings whereName($value)
 * @method static Builder<static>|Settings wherePayload($value)
 * @method static Builder<static>|Settings whereUpdatedAt($value)
 * @method static Builder<static>|Settings whereValue($value)
 *
 * @mixin \Eloquent
 */
class Settings extends SettingsProperty
{
    use ClearsApiCache;
    use ClearsResponseCache;
}
