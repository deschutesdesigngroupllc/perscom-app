<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Resources\Api\SettingsResource;
use App\Models\Settings;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
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
    public function afterIndex(Request $request, $entities)
    {
        /** @var Collection $settings */
        $settings = $entities->getCollection()->transform(function (Settings $setting) {
            return $setting->forceFill([
                'payload' => json_decode($setting->payload),
            ]);
        });

        $paginator = new LengthAwarePaginator($settings, $settings->count(), $this->paginator->resolvePaginationLimit($request));

        return $this->collectionResponse($paginator);
    }
}
