<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Credentials;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\CredentialRequest;
use App\Models\Credential;
use Orion\Http\Controllers\Controller;

class CredentialsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Credential::class;

    protected $request = CredentialRequest::class;

    public function includes(): array
    {
        return [
            'issuer',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'type', 'issuer_id', 'order', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'type', 'issuer_id', 'order', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'type', 'issuer_id', 'order', 'created_at', 'updated_at'];
    }
}
