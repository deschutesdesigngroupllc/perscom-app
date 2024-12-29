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

    public function includes(): array
    {
        return ['assignment_records', 'assignment_records.*', 'users'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'abbreviation', 'description', 'order', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'abbreviation', 'description', 'order', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'abbreviation', 'description', 'order', 'created_at', 'updated_at'];
    }
}
