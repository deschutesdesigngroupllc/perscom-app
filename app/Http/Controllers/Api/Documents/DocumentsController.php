<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Documents;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\DocumentRequest;
use App\Models\Document;
use Orion\Http\Controllers\Controller;

class DocumentsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Document::class;

    protected $request = DocumentRequest::class;

    /**
     * @return array<int, string>
     */
    public function exposedScopes(): array
    {
        return ['tags'];
    }

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return ['author'];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'author_id', 'author.*', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'author_id', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'author_id', 'author.*', 'name', 'description', 'content', 'created_at', 'updated_at'];
    }
}
