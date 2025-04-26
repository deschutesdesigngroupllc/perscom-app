<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Forms;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Models\Form;
use Orion\Http\Controllers\RelationController;

class FormsFieldsController extends RelationController
{
    use AuthorizesRequests;

    protected $model = Form::class;

    protected $relation = 'fields';

    protected $pivotFillable = ['order'];
}
