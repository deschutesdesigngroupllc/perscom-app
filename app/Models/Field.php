<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Hideable;
use App\Models\Enums\FieldOptionsModel;
use App\Models\Enums\FieldOptionsType;
use App\Models\Enums\FieldType;
use App\Traits\CanBeHidden;
use App\Traits\ClearsApiCache;
use App\Traits\ClearsResponseCache;
use App\Traits\HasResourceLabel;
use App\Traits\HasResourceUrl;
use ArrayObject as ArrayObjectAlias;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\ArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

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
 * @property ArrayObject $options
 * @property FieldOptionsType|null $options_type
 * @property FieldOptionsModel|null $options_model
 * @property string|null $true_value
 * @property string|null $false_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Form> $forms
 * @property-read int|null $forms_count
 * @property-read string $label
 * @property-read string|null $relative_url
 * @property-read string|null $url
 * @property-read Collection<int, User> $users
 * @property-read int|null $users_count
 * @property-read string|null $validation_rules
 *
 * @method static \Database\Factories\FieldFactory factory($count = null, $state = [])
 * @method static Builder<static>|Field hidden()
 * @method static Builder<static>|Field newModelQuery()
 * @method static Builder<static>|Field newQuery()
 * @method static Builder<static>|Field query()
 * @method static Builder<static>|Field visible()
 * @method static Builder<static>|Field whereCast($value)
 * @method static Builder<static>|Field whereCreatedAt($value)
 * @method static Builder<static>|Field whereDefault($value)
 * @method static Builder<static>|Field whereDescription($value)
 * @method static Builder<static>|Field whereFalseValue($value)
 * @method static Builder<static>|Field whereHelp($value)
 * @method static Builder<static>|Field whereHidden($value)
 * @method static Builder<static>|Field whereId($value)
 * @method static Builder<static>|Field whereKey($value)
 * @method static Builder<static>|Field whereName($value)
 * @method static Builder<static>|Field whereNovaType($value)
 * @method static Builder<static>|Field whereOptions($value)
 * @method static Builder<static>|Field whereOptionsModel($value)
 * @method static Builder<static>|Field whereOptionsType($value)
 * @method static Builder<static>|Field wherePlaceholder($value)
 * @method static Builder<static>|Field whereReadonly($value)
 * @method static Builder<static>|Field whereRequired($value)
 * @method static Builder<static>|Field whereRules($value)
 * @method static Builder<static>|Field whereTrueValue($value)
 * @method static Builder<static>|Field whereType($value)
 * @method static Builder<static>|Field whereUpdatedAt($value)
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
        'options_type',
        'options_model',
        'created_at',
        'updated_at',
    ];

    public function options(): Attribute
    {
        return Attribute::get(function ($value, $attributes = null): ArrayObject {
            if (filled($value) && data_get($attributes, 'options_type') === FieldOptionsType::Array->value) {
                return new ArrayObject(json_decode($value), ArrayObjectAlias::ARRAY_AS_PROPS);
            }

            if (filled(data_get($attributes, 'options_model')) && $this->options_model instanceof FieldOptionsModel && data_get($attributes, 'options_type') === FieldOptionsType::Model->value) {
                return new ArrayObject($this->options_model->getOptions(), ArrayObjectAlias::ARRAY_AS_PROPS);
            }

            if (data_get($attributes, 'type') === FieldType::FIELD_TIMEZONE->value) {
                return new ArrayObject(Collection::wrap(timezone_identifiers_list())
                    ->mapWithKeys(fn ($timezone): array => [$timezone => $timezone])
                    ->toArray(), ArrayObjectAlias::ARRAY_AS_PROPS);
            }

            return new ArrayObject([], ArrayObjectAlias::ARRAY_AS_PROPS);
        })->shouldCache();
    }

    public function validationRules(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes): ?string {
                $rules = explode('|', data_get($attributes, 'rules', '') ?? '');

                if (data_get($attributes, 'required') && ! in_array('required', $rules)) {
                    $rules[] = 'required';
                }

                if (data_get($attributes, 'type') === FieldType::FIELD_FILE->value) {
                    $rules[] = 'file';
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

    protected function casts(): array
    {
        return [
            'readonly' => 'boolean',
            'required' => 'boolean',
            'type' => FieldType::class,
            'options_type' => FieldOptionsType::class,
            'options_model' => FieldOptionsModel::class,
        ];
    }
}
