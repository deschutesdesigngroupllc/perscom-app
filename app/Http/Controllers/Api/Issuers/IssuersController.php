<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Issuers;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\IssuerRequest;
use App\Models\Issuer;
use Orion\Http\Controllers\Controller;

class IssuersController extends Controller
{
    use AuthorizesRequests;

    protected $model = Issuer::class;

    protected $request = IssuerRequest::class;

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'created_at', 'updated_at'];
    }
}
