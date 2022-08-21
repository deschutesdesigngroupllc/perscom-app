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
     * Available field types
     */
    public static $fieldTypes = [
        'checkbox' => 'Checkbox',
        'email' => 'Email',
        'meta' => 'Meta',
        'multiselect' => 'Multiselect',
        'radio' => 'Radio',
        'radiogroup' => 'Radio Group',
        'select' => 'Select',
        'static' => 'Static',
        'text' => 'Text',
        'textarea' => 'Textarea',
        'toggle' => 'Toggle',
        'password' => 'Password',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function forms()
    {
        return $this->morphedByMany(Form::class, 'model', 'model_has_fields')
            ->withPivot(['order'])
            ->withTimestamps();
    }
}
