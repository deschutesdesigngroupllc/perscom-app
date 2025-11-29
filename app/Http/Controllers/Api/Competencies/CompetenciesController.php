<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Competencies;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\CompetencyRequest;
use App\Models\Competency;
use Orion\Http\Controllers\Controller;

class CompetenciesController extends Controller
{
    use AuthorizesRequests;

    protected $model = Competency::class;

    protected $request = CompetencyRequest::class;

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'created_at', 'updated_at'];
    }
}
