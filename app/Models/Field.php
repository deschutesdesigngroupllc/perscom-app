<?php

namespace App\Models;

use App\Models\Forms\Form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;

class Field extends Model
{
    use HasFactory;
    use HasTags;

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
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function forms()
    {
        return $this->morphedByMany(Form::class, 'model', 'model_has_fields')->withPivot(['order'])->withTimestamps();
    }
}
