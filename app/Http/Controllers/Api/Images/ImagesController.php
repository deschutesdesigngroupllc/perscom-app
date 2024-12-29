<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Images;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Image;
use Illuminate\Database\Eloquent\Model;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class ImagesController extends Controller
{
    use AuthorizesRequests;

    protected $model = Image::class;

    protected $request = ImageRequest::class;

    public function includes(): array
    {
        return ['model'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'model_id', 'model_type', 'model.*', 'path', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'model_id', 'model_type', 'path', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'model_id', 'model_type', 'model.*', 'path', 'created_at', 'updated_at'];
    }

    protected function beforeSave(Request $request, Model $entity): void
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
