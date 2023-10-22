<?php

namespace App\Models;

use App\Models\Scopes\FieldScope;
use App\Traits\HasHiddenResults;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
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
 * @property AsArrayObject $options
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Form> $forms
 * @property-read int|null $forms_count
 * @property-read string|null $validation_rules
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Form> $users
 * @property-read int|null $users_count
 *
 * @method static \Database\Factories\FieldFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Field hidden()
 * @method static \Illuminate\Database\Eloquent\Builder|Field newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Field newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Field query()
 * @method static \Illuminate\Database\Eloquent\Builder|Field visible()
 *
 * @mixin \Eloquent
 */
class Field extends Model
{
    use HasFactory;
    use HasHiddenResults;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'hidden' => 'boolean',
        'options' => AsArrayObject::class,
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

    public const FIELD_DATETIME = 'datetime-local';

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

    public static function boot(): void
    {
        parent::boot();

        static::saving(function (Field $field) {
            $field->nova_type = Field::$novaFieldTypes[$field->type];
            $field->cast = Field::$fieldCasts[$field->type];
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new FieldScope());
    }

    public function constructNovaField(): mixed
    {
        $field = \call_user_func([$this->nova_type, 'make'], $this->name, $this->key);

        if ($this->placeholder && method_exists($field, 'placeholder')) {
            $field->placeholder($this->placeholder);
        }

        if ($this->help && method_exists($field, 'help')) {
            $field->help($this->help);
        }

        if ($this->validation_rules) {
            $field->rules(explode('|', $this->validation_rules));
        }

        if ($this->readonly && method_exists($field, 'readonly')) {
            $field->readonly();
        }

        if (isset($this->options) && method_exists($field, 'options')) {
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

                if ($field && $field->cast) {
                    return $this->castValue($value);
                }
            }

            return $value;
        });

        return $field;
    }

    public function castValue(mixed $value): mixed
    {
        return match ($this->cast) {
            self::$fieldCasts[self::FIELD_BOOLEAN] => (bool) $value,
            self::$fieldCasts[self::FIELD_DATE], self::$fieldCasts[self::FIELD_DATETIME] => Carbon::parse($value),
            self::$fieldCasts[self::FIELD_NUMBER] => (int) $value,
            default => $value
        };
    }

    public function getHumanReadableFormat(mixed $value): string
    {
        return match ($this->cast) {
            self::$fieldCasts[self::FIELD_BOOLEAN] => $value ? 'True' : 'False',
            self::$fieldCasts[self::FIELD_DATE] => Carbon::parse($value)->toFormattedDateString(),
            self::$fieldCasts[self::FIELD_DATETIME] => Carbon::parse($value)->format('M j, Y H:i:s'),
            self::$fieldCasts[self::FIELD_SELECT] => $this->options[$value] ?? $value,
            default => (string) $value
        };
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
        return $this->morphedByMany(Form::class, 'model', 'model_has_fields')
            ->as('users')
            ->withPivot(['order'])
            ->withTimestamps();
    }

    public function getValidationRulesAttribute(): ?string
    {
        $rules = explode('|', $this->rules);

        if ($this->required && ! \in_array('required', $rules, false)) {
            $rules[] = 'required';
        }

        $rules = array_unique(array_filter($rules));

        if (empty($rules)) {
            return null;
        }

        return implode('|', $rules);
    }
}
