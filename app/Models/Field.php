<?php

namespace App\Models;

use App\Models\Forms\Form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    /**
     * Available field types
     */
    public const fieldTypes = [
        'Boolean' => 'boolean',
        'Code' => 'string',
        'Color' => 'string',
        'Country' => 'string',
        'Currency' => 'string',
        'DateTime' => 'datetime',
        'Date' => 'date',
        'Email' => 'string',
        'Heading' => 'string',
        'Line' => 'string',
        'Markdown' => 'string',
        'Number' => 'integer',
        'Password' => 'string',
        'Select' => 'string',
        'Text' => 'string',
        'Textarea' => 'string',
        'Timezone' => 'string',
        'URL' => 'string',
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
