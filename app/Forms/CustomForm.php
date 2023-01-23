<?php

namespace App\Forms;

use App\Models\Field;
use App\Models\Forms\Submission;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laraform\Laraform;

class CustomForm extends Laraform
{
    /**
     * @var string
     */
    public $class = 'form';

    /**
     * @return array
     */
    public function schema($fields = null, $formId = null)
    {
        $elements = [
            'form_id' => [
                'type' => 'meta',
                'default' => $formId,
            ],
        ];

        if ($fields) {
            foreach ($fields as $field) {
                $definition = [
                    'type' => $field->type === Field::FIELD_EMAIL ? Field::FIELD_TEXT : $field->type,
                    'label' => $field->name,
                    'placeholder' => $field->placeholder,
                    'readonly' => $field->readonly,
                    'disabled' => $field->disabled,
                    'description' => $field->help,
                ];

                $rules = Arr::wrap(Arr::get($definition, 'rules'));

                if ($field->required) {
                    $rules[] = 'required';
                    Arr::set($definition, 'rules', $rules);
                }

                if ($field->type === Field::FIELD_EMAIL) {
                    $rules[] = 'email';
                    Arr::set($definition, 'rules', $rules);
                }

                if ($field->type === Field::FIELD_SELECT ||
                    $field->type === Field::FIELD_RADIOGROUP ||
                    $field->type === Field::FIELD_MULTISELECT) {
                    Arr::set($definition, 'items', $field->options);
                }

                if ($field->type === Field::FIELD_STATIC) {
                    Arr::pull($definition, 'label');
                    if ($field->text) {
                        Arr::set($definition, 'content', Str::markdown($field->text));
                    }
                }

                if ($field->type === Field::FIELD_CHECKBOX) {
                    Arr::pull($definition, 'label');
                    Arr::set($definition, 'text', $field->name);
                }

                $elements[Field::getSchemaSafeKey($field->key)] = $definition;
            }
        }

        return $elements;
    }

    /**
     * After hook
     */
    public function after()
    {
        $submission = new Submission();
        $submission->forceFill(array_merge($this->data, [
            'user_id' => Auth::user() ? Auth::user()->getAuthIdentifier() : null,
        ]));
        $submission->save();
    }

    /**
     * @return \string[][]
     */
    public function buttons()
    {
        return [
            [
                'label' => 'Submit',
                'class' => 'btn-primary form-button',
            ],
        ];
    }
}
