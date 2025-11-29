<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Specialties;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\SpecialtyRequest;
use App\Models\Specialty;
use Orion\Http\Controllers\Controller;

class SpecialtiesController extends Controller
{
    use AuthorizesRequests;

    protected $model = Specialty::class;

    protected $request = SpecialtyRequest::class;

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['assignment_records', 'assignment_records.*', 'users'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'abbreviation', 'description', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'abbreviation', 'description', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'abbreviation', 'description', 'order', 'created_at', 'updated_at'];
    }
}
