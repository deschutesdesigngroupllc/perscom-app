<?php

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class SubmissionRequest extends Request
{
    /**
     * @return string[]
     */
    public function commonRules(): array
    {
        return [
        ];
    }

    /**
     * @return string[]
     */
    public function storeRules(): array
    {
        return [
        ];
    }
}
