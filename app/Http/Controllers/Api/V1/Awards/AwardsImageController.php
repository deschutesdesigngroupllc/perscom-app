<?php

namespace App\Http\Controllers\Api\V1\Awards;

use App\Http\Requests\Api\ImageRequest;
use App\Models\Award;
use App\Policies\ImagePolicy;
use Illuminate\Database\Eloquent\Model;
use Orion\Http\Controllers\RelationController;
use Orion\Http\Requests\Request;

class AwardsImageController extends RelationController
{
    /**
     * @var string
     */
    protected $model = Award::class;

    /**
     * @var string
     */
    protected $request = ImageRequest::class;

    /**
     * @var string
     */
    protected $policy = ImagePolicy::class;

    /**
     * @var string
     */
    protected $relation = 'image';

    /**
     * @return string[]
     */
    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'path', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'path', 'created_at', 'updated_at'];
    }

    /**
     * @return string[]
     */
    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'path', 'created_at', 'updated_at'];
    }

    protected function beforeSave(Request $request, Model $parentEntity, Model $entity): void
    {
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');

            $path = $file->store('/', 's3_public');

            $entity->forceFill([
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
            ]);
        }
    }
}
