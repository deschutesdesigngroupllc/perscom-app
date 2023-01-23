<?php

namespace App\Models;

use App\Models\Forms\Form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Email;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Field extends Model
{
    use HasFactory;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
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
    public const FIELD_CHECKBOX = 'checkbox';

    public const FIELD_EMAIL = 'email';

    public const FIELD_META = 'meta';

    public const FIELD_MULTISELECT = 'multiselect';

    public const FIELD_RADIO = 'radio';

    public const FIELD_RADIOGROUP = 'radiogroup';

    public const FIELD_SELECT = 'select';

    public const FIELD_STATIC = 'static';

    public const FIELD_TEXT = 'text';

    public const FIELD_TEXTAREA = 'textarea';

    public const FIELD_PASSWORD = 'password';

    /**
     * Available field types
     */
    public static $fieldTypes = [
        self::FIELD_CHECKBOX => 'Checkbox',
        self::FIELD_EMAIL => 'Email',
        self::FIELD_META => 'Meta',
        self::FIELD_MULTISELECT => 'Multiselect',
        self::FIELD_RADIO => 'Radio',
        self::FIELD_RADIOGROUP => 'Radio Group',
        self::FIELD_SELECT => 'Select',
        self::FIELD_STATIC => 'Static',
        self::FIELD_TEXT => 'Text',
        self::FIELD_TEXTAREA => 'Textarea',
        self::FIELD_PASSWORD => 'Password',
    ];

    /**
     * @var string[]
     */
    public static $novaFieldTypes = [
        self::FIELD_CHECKBOX => Boolean::class,
        self::FIELD_EMAIL => Email::class,
        self::FIELD_META => Code::class,
        self::FIELD_MULTISELECT => Select::class,
        self::FIELD_RADIO => Select::class,
        self::FIELD_RADIOGROUP => 'Radio Group',
        self::FIELD_SELECT => Select::class,
        self::FIELD_STATIC => 'Static',
        self::FIELD_TEXT => Text::class,
        self::FIELD_TEXTAREA => Textarea::class,
        self::FIELD_PASSWORD => Password::class,
    ];

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function (Field $field) {
            $field->nova_type = Field::$novaFieldTypes[$field->type];
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

        if ($this->disabled && method_exists($field, 'disabled')) {
            $field->disabled();
        }

        if ($this->options && method_exists($field, 'options')) {
            $field->options($this->options);
        }

        return $field;
    }

    /**
     * @param $key
     *
     * @return string
     */
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
