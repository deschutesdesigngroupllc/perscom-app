<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Orion\Http\Requests\Request;

class AttachmentRequest extends Request
{
    public function commonRules(): array
    {
        return [
            'name' => 'string',
            'filename' => 'string',
            'path' => 'string',
            'updated_at' => 'date',
            'created_at' => 'date',
            'deleted_at' => 'date',
        ];
    }

    public function storeRules(): array
    {
        return [
            'name' => 'required|string',
            'file' => 'required|file|min:1|max:10000',
        ];
    }
}
