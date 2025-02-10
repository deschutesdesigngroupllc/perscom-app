<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Hideable;
use App\Models\Enums\FieldType;
use App\Traits\CanBeHidden;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use ArrayObject;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

use function in_array;

/**
 * @property int $id
 * @property string $name
 * @property string|null $key
 * @property FieldType $type
 * @property string|null $nova_type
 * @property string|null $cast
 * @property string|null $description
 * @property string|null $placeholder
 * @property string|null $default
 * @property string|null $help
 * @property bool $required
 * @property string|null $rules
 * @property bool $readonly
 * @property bool $hidden
 * @property ArrayObject<array-key, mixed>|null $options
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Form> $forms
 * @property-read int|null $forms_count
 * @property-read string $label
 * @property-read string|null $relative_url
 * @property-read string|null $url
 * @property-read \Illuminate\Database\Eloquent\Collection<int, User> $users
 * @property-read int|null $users_count
 * @property-read string|null $validation_rules
 *
 * @method static \Database\Factories\FieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field hidden()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereCast($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereHelp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereHidden($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereNovaType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field wherePlaceholder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereReadonly($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereRequired($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereRules($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Field whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class Field extends Model implements HasLabel, Hideable
{
    use CanBeHidden;
    use ClearsApiCache;
    use ClearsResponseCache;
    use HasFactory;
    use HasResourceLabel;
    use HasResourceUrl;

    protected $fillable = [
        'name',
        'key',
        'type',
        'cast',
        'description',
        'placeholder',
        'default',
        'help',
        'required',
        'rules',
        'readonly',
        'options',
        'created_at',
        'updated_at',
    ];

    /**
     * @return Attribute<?string, never>
     */
    public function validationRules(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes): ?string {
                $rules = explode('|', data_get($attributes, 'rules', '') ?? '');

                if (data_get($attributes, 'required') && ! in_array('required', $rules)) {
                    $rules[] = 'required';
                }

                return filled($rules)
                    ? implode('|', array_unique(array_filter($rules)))
                    : null;
            }
        )->shouldCache();
    }

    public function forms(): MorphToMany
    {
        return $this->morphedByMany(Form::class, 'model', 'model_has_fields')
            ->as('forms')
            ->withPivot(['order'])
            ->withTimestamps();
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_fields')
            ->as('users')
            ->withPivot(['order'])
            ->withTimestamps();
    }

    /**
     * @return string[]
     */
    protected function casts(): array
    {
        return [
            'readonly' => 'boolean',
            'required' => 'boolean',
            'type' => FieldType::class,
            'options' => AsArrayObject::class,
        ];
    }
}
