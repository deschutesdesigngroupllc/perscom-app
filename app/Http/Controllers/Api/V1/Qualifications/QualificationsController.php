<?php

namespace App\Http\Controllers\Api\V1\Qualifications;

use App\Http\Requests\Api\QualificationRequest;
use App\Models\Qualification;
use App\Policies\QualificationPolicy;
use Orion\Http\Controllers\Controller;

class QualificationsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Qualification::class;

    /**
     * @var string
     */
    protected $request = QualificationRequest::class;

    /**
     * @var string
     */
    protected $policy = QualificationPolicy::class;

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['image'];
    }

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at'];
    }
}
