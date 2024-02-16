<?php

namespace App\Http\Requests\Api;

use App\Models\Field;
use App\Models\Form;
use App\Models\Submission;
use Orion\Http\Requests\Request;

class SubmissionRequest extends Request
{
    protected function getDynamicRules(): mixed
    {
        $submissionId = $this->route('form') ?? optional($this->route('submission'), function ($submissionId) {
            return optional(Submission::find($submissionId))->form_id;
        });

        $form = optional($submissionId, function ($submissionId) {
            return Form::find($submissionId);
        });

        if ($form) {
            return $form->fields->filter->validation_rules->mapWithKeys(function (Field $field) {
                return [$field->key => $field->validation_rules];
            })->toArray();
        }

        return [];
    }

    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return array_merge([
            'form_id' => 'integer|exists:forms,id',
            'user_id' => 'integer|nullable|exists:users,id',
            'updated_at' => 'date',
            'created_at' => 'date',
        ], $this->getDynamicRules());
    }

    /**
     * @return string[]
     */
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
