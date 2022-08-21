<?php

namespace App\Forms;

use App\Models\Forms\Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laraform\Authorization\AuthorizationBuilder;
use Laraform\Database\DatabaseBuilder;
use Laraform\Elements\Factory as ElementFactory;
use Laraform\Event\Event;
use Laraform\Laraform;
use Laraform\User\UserBuilder;
use Laraform\Validation\Validation;

class CustomForm extends Laraform
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * @var string
     */
    public $class = 'form';

    /**
     * @param  Form  $form
     *
     * @return $this
     */
    public function withForm(Form $form)
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return array
     */
    public function schema()
    {
        $fields = [];

        if ($this->form) {
            foreach ($this->form->fields as $field) {
                $definition = [
                    'type' => $field->type === 'email' ? 'text' : $field->type,
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

                if ($field->type === 'email') {
                    $rules[] = 'email';
                    Arr::set($definition, 'rules', $rules);
                }

                if ($field->type === 'select' || $field->type === 'radiogroup') {
                    Arr::set($definition, 'items', $field->options);
                }

                if ($field->type === 'static') {
                    Arr::pull($definition, 'label');
                    Arr::set($definition, 'content', Str::markdown($field->text));
                }

                if ($field->type === 'checkbox') {
                    Arr::pull($definition, 'label');
                    Arr::set($definition, 'text', $field->name);
                }

                $fields[$field->id] = $definition;
            }
        }

        return $fields;
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
