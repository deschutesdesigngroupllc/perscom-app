<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Form;
use App\Models\Submission;
use App\Services\FieldService;
use Illuminate\Support\Traits\Conditionable;
use Orion\Http\Requests\Request;

class SubmissionRequest extends Request
{
    use Conditionable;

    public function commonRules(): array
    {
        /**
         * @return string[]
         */
        return array_merge([
            'form_id' => 'integer|exists:forms,id',
            'user_id' => 'integer|nullable|exists:users,id',
            'updated_at' => 'date',
            'created_at' => 'date',
        ], $this->getFieldRules());
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

        if (! $this->route('user')) {
            $rules['user_id'] = 'required|integer|exists:users,id';
        }

        return array_merge($rules, $this->getFieldRules());
    }

    /**
     * @return string[]
     */
    protected function getFieldRules(): array
    {
        $routeFormId = $this->route('form');
        $routeSubmissionId = $this->route('submission');
        $inputFormId = $this->input('form_id');

        /** @var Form|null $form */
        $form = $routeFormId ? Form::find($routeFormId) : null;
        $form ??= Submission::find($routeSubmissionId)->form ?? null;
        $form ??= $inputFormId ? Form::find($inputFormId) : null;

        if (blank($form)) {
            return [];
        }

        return FieldService::getValidationRules($form->fields)->toArray();
    }
}
