<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Qualifications;

use App\Http\Requests\Api\QualificationRequest;
use App\Models\Qualification;
use App\Policies\QualificationPolicy;
use Orion\Http\Controllers\Controller;

class QualificationsController extends Controller
{
    protected $model = Qualification::class;

    protected $request = QualificationRequest::class;

    protected $policy = QualificationPolicy::class;

    public function includes(): array
    {
        return ['image'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'order', 'created_at', 'updated_at', 'deleted_at'];
    }
}
