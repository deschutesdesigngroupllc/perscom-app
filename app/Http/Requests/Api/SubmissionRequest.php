<?php

namespace App\Http\Requests\Api;

use App\Models\Field;
use App\Models\Form;
use Orion\Http\Requests\Request;

class SubmissionRequest extends Request
{
    /**
     * @return array|array[]
     */
    protected function getDynamicRules()
    {
        $form = Form::findOrFail($this->route('form'));

        return $form->fields->filter->rules->mapWithKeys(function (Field $field) {
            $rules = explode('|', $field->rules);

            if ($field->required && ! \in_array('required', $rules)) {
                $rules[] = 'required';
            }

            return [$field->key => implode('|', array_unique($rules))];
        })->toArray();
    }

    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return $this->getDynamicRules();
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return $this->getDynamicRules();
    }
}
