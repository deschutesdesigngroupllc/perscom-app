<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\TrainingRecords;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\CredentialRequest;
use App\Models\TrainingRecord;
use Orion\Http\Controllers\RelationController;

class TrainingRecordsCredentialsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = TrainingRecord::class;

    protected $request = CredentialRequest::class;

    protected $relation = 'credentials';
}
