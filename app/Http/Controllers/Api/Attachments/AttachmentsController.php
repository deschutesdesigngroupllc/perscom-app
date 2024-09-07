<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Attachments;

use App\Http\Requests\Api\AttachmentRequest;
use App\Models\Attachment;
use App\Policies\AttachmentPolicy;
use Illuminate\Database\Eloquent\Model;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class AttachmentsController extends Controller
{
    protected $model = Attachment::class;

    protected $request = AttachmentRequest::class;

    protected $policy = AttachmentPolicy::class;

    public function includes(): array
    {
        return ['model'];
    }

    public function sortableBy(): array
    {
        return ['id', 'name', 'filename', 'model_id', 'model_type', 'model.*', 'path', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function searchableBy(): array
    {
        return ['id', 'name', 'filename', 'model_id', 'model_type', 'path', 'created_at', 'updated_at', 'deleted_at'];
    }

    public function filterableBy(): array
    {
        return ['id', 'name', 'filename', 'model_id', 'model_type', 'model.*', 'path', 'created_at', 'updated_at', 'deleted_at'];
    }

    protected function beforeSave(Request $request, Model $entity): void
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');

            $path = $file->storePublicly('/', 's3');

            $entity->forceFill([
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
            ]);

            $request->merge([
                'file' => null,
            ]);
        }
    }
}
