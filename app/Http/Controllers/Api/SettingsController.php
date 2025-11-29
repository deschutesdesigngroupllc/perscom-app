<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\SettingsResource;
use App\Models\Settings;
use Illuminate\Pagination\LengthAwarePaginator;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class SettingsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Settings::class;

    protected $resource = SettingsResource::class;

    /**
     * @param  LengthAwarePaginator  $entities
     */
    protected function afterIndex(Request $request, $entities)
    {
        $settings = $entities->getCollection()->transform(fn (Settings $setting) => $setting->forceFill([
            'payload' => json_decode((string) $setting->payload),
        ]));

        $paginator = new LengthAwarePaginator($settings, $settings->count(), $this->paginator->resolvePaginationLimit($request));

        return $this->collectionResponse($paginator);
    }
}
