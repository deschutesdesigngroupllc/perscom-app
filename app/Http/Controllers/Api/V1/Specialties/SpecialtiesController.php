<?php

namespace App\Http\Controllers\Api\V1\Specialties;

use App\Http\Requests\Api\SpecialtyRequest;
use App\Models\Specialty;
use App\Policies\SpecialtyPolicy;
use Orion\Http\Controllers\Controller;

class SpecialtiesController extends Controller
{
    /**
     * @var string
     */
    protected $model = Specialty::class;

    /**
     * @var string
     */
    protected $request = SpecialtyRequest::class;

    /**
     * @var string
     */
    protected $policy = SpecialtyPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['assignment_records', 'assignment_records.*', 'users'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['name'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'created_at'];
    }
}
