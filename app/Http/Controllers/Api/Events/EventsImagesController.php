<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Events;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;
use Orion\Http\Controllers\RelationController;
use Orion\Http\Requests\Request;

class EventsImagesController extends RelationController
{
    use AuthorizesRequests;

    protected $model = Event::class;

    protected $request = ImageRequest::class;

    protected $relation = 'image';

    public function sortableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'path', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'path', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'description', 'filename', 'path', 'created_at', 'updated_at'];
    }

    protected function beforeSave(Request $request, Model $parentEntity, Model $entity): void
    {
        // TODO: Remove image from saving to DB
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');

            $path = $file->storePublicly('/', 's3');

            $entity->forceFill([
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
            ]);
        }
    }
}
