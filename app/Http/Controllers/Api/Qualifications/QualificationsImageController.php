<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Qualifications;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Qualification;
use Illuminate\Database\Eloquent\Model;
use Orion\Http\Controllers\RelationController;
use Orion\Http\Requests\Request;

class QualificationsImageController extends RelationController
{
    use AuthorizesRequests;

    protected $model = Qualification::class;

    protected $request = ImageRequest::class;

    protected $relation = 'image';

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'path', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'path', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'path', 'created_at', 'updated_at'];
    }

    protected function beforeSave(Request $request, Model $parentEntity, Model $entity): void
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');

            $path = $file->storePublicly('/');

            $entity->forceFill([
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
            ]);
        }
    }
}
