<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Forms;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\SubmissionRequest;
use App\Models\Form;
use Illuminate\Database\Eloquent\Model;
use Orion\Http\Controllers\RelationController;
use Orion\Http\Requests\Request;

class FormsSubmissionsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = Form::class;

    protected $request = SubmissionRequest::class;

    protected $relation = 'submissions';

    public function includes(): array
    {
        return ['form', 'user', 'statuses', 'statuses.record'];
    }

    public function sortableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'data', 'read_at', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'form_id', 'user_id', 'data', 'read_at', 'created_at', 'updated_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'form_id', 'form.*', 'user_id', 'user.*', 'read_at', 'created_at', 'updated_at'];
    }

    public function beforeSave(Request $request, Model $parentEntity, Model $entity): void
    {
        foreach ($request->allFiles() as $key => $file) {
            /** @var Form $parentEntity */
            if (in_array($key, $parentEntity->fields->pluck('key')->toArray())) {
                $path = $request->file($key)->storePublicly();

                $entity->fill([
                    $key => $path,
                ]);
            }
        }
    }
}
