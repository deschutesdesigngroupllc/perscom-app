<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\SettingsResource;
use App\Models\Settings;
use Illuminate\Pagination\LengthAwarePaginator;
use JsonException;
use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;

class SettingsController extends Controller
{
    use AuthorizesRequests;

    protected $model = Settings::class;

    protected $resource = SettingsResource::class;

    /**
     * Decode each setting's payload before returning the page.
     *
     * @param  LengthAwarePaginator  $entities
     *
     * @throws JsonException when a setting payload is not valid JSON
     */
    protected function afterIndex(Request $request, $entities)
    {
        $settings = $entities->getCollection()->transform(fn (Settings $setting): Settings => $setting->forceFill([
            'payload' => json_decode((string) $setting->payload, false, 512, JSON_THROW_ON_ERROR),
        ]));

        $paginator = new LengthAwarePaginator($settings, $settings->count(), $this->paginator->resolvePaginationLimit($request));

        return $this->collectionResponse($paginator);
    }
}
