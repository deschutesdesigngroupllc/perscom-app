<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Users;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\UnitRequest;
use App\Models\User;
use Orion\Http\Controllers\RelationController;

class UsersUnitController extends RelationController
{
    use AuthorizesRequests;

    protected $model = User::class;

    protected $request = UnitRequest::class;

    protected $relation = 'unit';

    public function includes(): array
    {
        return ['image'];
    }
}
