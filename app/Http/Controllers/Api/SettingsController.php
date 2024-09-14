<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\SettingsResource;
use App\Models\Settings;
use Orion\Http\Controllers\Controller;

class SettingsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Settings::class;

    protected $resource = SettingsResource::class;
}
