<?php

namespace App\Http\Requests;

use Orion\Http\Requests\Request;

class SubmissionRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
            'data' => 'json',
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
            'form_id' => 'required|exists:forms,id',
            'user_id' => 'required|exists:users,id',
            'data' => 'required|json',
        ];
    }
}
