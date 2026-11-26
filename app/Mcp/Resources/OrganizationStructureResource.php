<?php

declare(strict_types=1);

namespace App\Mcp\Resources;

use App\Services\InternalApiService;
use JsonException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\MimeType;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Attributes\Uri;
use Laravel\Mcp\Server\Resource;

#[Name('organization-structure')]
#[Title('Organization Structure')]
#[Uri('perscom://organization/structure')]
#[MimeType('application/json')]
#[Description("The organization's ranks (most senior first), units and positions. Sections the connection is not allowed to view are omitted.")]
class OrganizationStructureResource extends Resource
{
    /**
     * @var array<string, string>
     */
    protected const array SECTIONS = [
        'ranks' => 'ranks',
        'units' => 'units',
        'positions' => 'positions',
    ];

    /**
     * Read the organization's ranks, units and positions from the API.
     *
     * @throws JsonException when an API response body is not valid JSON
     */
    public function handle(Request $request): Response
    {
        $structure = collect(static::SECTIONS)
            ->map(function (string $endpoint): ?array {
                $response = InternalApiService::get($endpoint, ['limit' => 100]);

                if (! $response->isSuccessful()) {
                    return null;
                }

                /** @var array{data?: array<int, array<string, mixed>>} $payload */
                $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

                return collect($payload['data'] ?? [])
                    ->sortBy('order')
                    ->map(fn (array $item): array => collect([
                        'id' => $item['id'] ?? null,
                        'name' => $item['name'] ?? null,
                        'abbreviation' => $item['abbreviation'] ?? null,
                        'paygrade' => $item['paygrade'] ?? null,
                        'description' => $item['description'] ?? null,
                    ])->filter(filled(...))->all())
                    ->values()
                    ->all();
            })
            ->reject(fn (?array $section): bool => is_null($section))
            ->all();

        return Response::json($structure);
    }
}
