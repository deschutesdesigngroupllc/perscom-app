<?php

namespace App\Http\Requests\Api;

use App\Models\Field;
use App\Models\Form;
use App\Models\Submission;
use Orion\Http\Requests\Request;

class SubmissionRequest extends Request
{
    /**
     * @return mixed
     */
    protected function getDynamicRules()
    {
        $submissionId = $this->route('form') ?? optional($this->route('submission'), function ($submissionId) {
            return Submission::find($submissionId)->form_id;
        });
        $form = Form::find($submissionId);

        if ($form) {
            return $form->fields->filter->validation_rules->mapWithKeys(function (Field $field) {
                return [$field->key => $field->validation_rules];
            })->toArray();
        }

        return [];
    }

    public function commonRules(): array
    {
        return array_merge([
            'form_id' => 'integer|exists:forms,id',
            'user_id' => 'integer|nullable|exists:users,id',
        ], $this->getDynamicRules());
    }

    public function storeRules(): array
    {
        $rules = [];
        if (! $this->route('form')) {
            $rules = [
                'form_id' => 'required|integer|exists:forms,id',
            ];
        }

        return array_merge($rules, $this->getDynamicRules());
    }
}
