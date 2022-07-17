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
        'Boolean',
        'Code',
        'Color',
        'Country',
        'Currency',
        'DateTime',
        'Date',
        'Email',
        'Heading',
        'KeyValue',
        'Line',
        'Markdown',
        'Number',
        'Password',
        'Select',
        'Text',
        'Textarea',
        'Timezone',
        'URL',
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
