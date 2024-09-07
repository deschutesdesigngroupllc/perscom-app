<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Specialties;

use App\Http\Requests\Api\SpecialtyRequest;
use App\Models\Specialty;
use App\Policies\SpecialtyPolicy;
use Orion\Http\Controllers\Controller;

class SpecialtiesController extends Controller
{
    protected $model = Specialty::class;

    protected $request = SpecialtyRequest::class;

    protected $policy = SpecialtyPolicy::class;

    public function includes(): array
    {
        return ['assignment_records', 'assignment_records.*', 'users'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'abbreviation', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'abbreviation', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'abbreviation', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }
}
