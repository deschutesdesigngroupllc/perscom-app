<?php

namespace App\Http\Requests\Api;

use App\Models\Field;
use App\Models\Form;
use Orion\Http\Requests\Request;

class SubmissionRequest extends Request
{
    /**
     * @return array<string>
     */
    protected function getDynamicRules(): array
    {
        $form = Form::findOrFail($this->route('form'));

        return $form->fields->filter->validation_rules->mapWithKeys(function (Field $field) {
            return [$field->key => $field->validation_rules];
        })->toArray();
    }

    /**
     * @return array<string>
     */
    public function commonRules(): array
    {
        return $this->getDynamicRules();
    }

    /**
     * @return array<string>
     */
    public function storeRules(): array
    {
        return $this->getDynamicRules();
    }
}
