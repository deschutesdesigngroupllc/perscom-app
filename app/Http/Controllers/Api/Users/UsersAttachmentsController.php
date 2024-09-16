<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\AttachmentRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Orion\Http\Controllers\RelationController;
use Orion\Http\Requests\Request;

class UsersAttachmentsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = User::class;

    protected $request = AttachmentRequest::class;

    protected $relation = 'attachments';

    protected function beforeSave(Request $request, Model $parentEntity, Model $entity): void
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');

            $path = $file->storePublicly('/', 's3');

            $entity->forceFill([
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
            ]);
        }
    }
}
