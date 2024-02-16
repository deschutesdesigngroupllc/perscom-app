<?php

namespace App\Http\Controllers\Api\V1\Documents;

use App\Http\Requests\Api\DocumentRequest;
use App\Models\Document;
use App\Policies\DocumentPolicy;
use Orion\Http\Controllers\Controller;

class DocumentsController extends Controller
{
    /**
     * @var string
     */
    protected $model = Document::class;

    /**
     * @var string
     */
    protected $request = DocumentRequest::class;

    /**
     * @var string
     */
    protected $policy = DocumentPolicy::class;

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
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }
}
