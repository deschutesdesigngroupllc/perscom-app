<?php

namespace App\Http\Controllers\Api\V1\Forms;

use App\Http\Requests\Api\FormRequest;
use App\Models\Form;
use App\Policies\FormPolicy;
use Orion\Http\Controllers\Controller;

class FormsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Form::class;

    /**
     * @var string
     */
    protected $request = FormRequest::class;

    /**
     * @var string
     */
    protected $policy = FormPolicy::class;

    /**
     * @return string[]
     */
    public function exposedScopes(): array
    {
        return ['tags'];
    }

    /**
     * @return string[]
     */
    public function includes(): array
    {
        return ['fields', 'submissions', 'submissions.*', 'tags'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['name', 'slug'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'slug', 'created_at'];
    }
}
