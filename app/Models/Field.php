<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\Country;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Timezone;

/**
 * App\Models\Field
 *
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Form> $forms
 * @property-read int|null $forms_count
 *
 * @method static \Database\Factories\FieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Field newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Field newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Field query()
 *
 * @mixin \Eloquent
 */
class Field extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
        'readonly' => 'boolean',
        'disabled' => 'boolean',
    ];

    /**
     * Fields
     */
    public const FIELD_BOOLEAN = 'boolean';

    public const FIELD_CODE = 'code';

    public const FIELD_COLOR = 'color';

    public const FIELD_COUNTRY = 'country';

    public const FIELD_DATE = 'date';

    public const FIELD_DATETIME = 'datetime';

    public const FIELD_EMAIL = 'email';

    public const FIELD_FILE = 'file';

    public const FIELD_NUMBER = 'number';

    public const FIELD_PASSWORD = 'password';

    public const FIELD_SELECT = 'select';

    public const FIELD_TEXT = 'text';

    public const FIELD_TEXTAREA = 'textarea';

    public const FIELD_TIMEZONE = 'timezone';

    /**
     * @var string[]
     */
    public static $fieldTypes = [
        self::FIELD_BOOLEAN => 'Boolean',
        self::FIELD_CODE => 'Code',
        self::FIELD_COLOR => 'Color',
        self::FIELD_COUNTRY => 'Country',
        self::FIELD_DATE => 'Date',
        self::FIELD_DATETIME => 'Datetime',
        self::FIELD_EMAIL => 'Email',
        self::FIELD_FILE => 'File',
        self::FIELD_NUMBER => 'Number',
        self::FIELD_PASSWORD => 'Password',
        self::FIELD_SELECT => 'Select',
        self::FIELD_TEXT => 'Text',
        self::FIELD_TEXTAREA => 'Textarea',
        self::FIELD_TIMEZONE => 'Timezone',
    ];

    /**
     * @var string[]
     */
    public static $novaFieldTypes = [
        self::FIELD_BOOLEAN => Boolean::class,
        self::FIELD_CODE => Code::class,
        self::FIELD_COLOR => Color::class,
        self::FIELD_COUNTRY => Country::class,
        self::FIELD_DATE => Date::class,
        self::FIELD_DATETIME => DateTime::class,
        self::FIELD_EMAIL => Email::class,
        self::FIELD_FILE => File::class,
        self::FIELD_NUMBER => Number::class,
        self::FIELD_PASSWORD => Password::class,
        self::FIELD_SELECT => Select::class,
        self::FIELD_TEXT => Text::class,
        self::FIELD_TEXTAREA => Textarea::class,
        self::FIELD_TIMEZONE => Timezone::class,
    ];

    /**
     * @var string[]
     */
    public static $fieldCasts = [
        self::FIELD_BOOLEAN => 'boolean',
        self::FIELD_CODE => 'string',
        self::FIELD_COLOR => 'string',
        self::FIELD_COUNTRY => 'string',
        self::FIELD_DATE => 'date',
        self::FIELD_DATETIME => 'datetime',
        self::FIELD_EMAIL => 'string',
        self::FIELD_FILE => 'string',
        self::FIELD_NUMBER => 'integer',
        self::FIELD_PASSWORD => 'string',
        self::FIELD_SELECT => 'string',
        self::FIELD_TEXT => 'string',
        self::FIELD_TEXTAREA => 'string',
        self::FIELD_TIMEZONE => 'string',
    ];

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (Field $field) {
            $field->nova_type = Field::$novaFieldTypes[$field->type];
            $field->cast = Field::$fieldCasts[$field->type];
        });
    }

    /**
     * @return false|mixed
     */
    public function constructNovaField()
    {
        $field = \call_user_func([$this->nova_type, 'make'], $this->name, $this->key);

        if ($this->placeholder && method_exists($field, 'placeholder')) {
            $field->placeholder($this->placeholder);
        }

        if ($this->help && method_exists($field, 'help')) {
            $field->help($this->help);
        }

        if ($this->required) {
            $field->rules('required');
        }

        if ($this->readonly && method_exists($field, 'readonly')) {
            $field->readonly();
        }

        if ($this->options && method_exists($field, 'options')) {
            $field->options($this->options);
        }

        if ($this->nova_type === File::class) {
            $field->disk('s3_public');
        }

        if ($this->nova_type === Select::class || $this->nova_type === Country::class) {
            $field->displayUsingLabels();
        }

        $field->resolveUsing(function ($value, $resource, $attribute) {
            if ($form = $resource->form) {
                $field = $form->fields->filter(function ($field) use ($attribute) {
                    return $field->key === $attribute;
                })->first();

                if ($field && $cast = $field->cast) {
                    switch ($cast) {
                        case 'boolean':
                            return (bool) $value;
                        case 'date':
                            return Carbon::parse($value);
                        case 'datetime':
                            return Carbon::parse($value);
                        case 'integer':
                            return (int) $value;
                        default:
                            return $value;
                    }
                }
            }

            return $value;
        });

        return $field;
    }

    public static function getSchemaSafeKey($key): string
    {
        return preg_replace('/[\W]/', '_', $key);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function forms()
    {
        return $this->morphedByMany(Form::class, 'model', 'model_has_fields')
            ->as('forms')
            ->withPivot(['order'])
            ->withTimestamps();
    }
}
