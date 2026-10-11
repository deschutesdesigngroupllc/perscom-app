<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Mcp\ApiResourceRegistry;
use App\Mcp\Tools\Concerns\RespondsWithApiResults;
use App\Services\InternalApiService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Title;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsOpenWorld;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[Name('get_resource')]
#[Title('Get Resource')]
#[Description('Get a single PERSCOM record by its ID, optionally expanding related data with include.')]
#[IsReadOnly]
#[IsIdempotent]
#[IsOpenWorld(false)]
class GetResourceTool extends Tool
{
    use RespondsWithApiResults;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'resource' => ['required', 'string', 'in:'.implode(',', ApiResourceRegistry::keys())],
            'id' => ['required', 'integer', 'min:1'],
            'include' => ['nullable', 'array'],
            'include.*' => ['string'],
        ], [
            'resource.in' => 'Unknown resource. Call list_resources to see the available resources.',
        ]);

        /** @var array{endpoint: string, scope: string} $resource */
        $resource = ApiResourceRegistry::find($validated['resource']);

        $query = filled($validated['include'] ?? null)
            ? ['include' => implode(',', $validated['include'])]
            : [];

        return $this->respondWithApiResult(
            InternalApiService::get(sprintf('%s/%d', $resource['endpoint'], $validated['id']), $query),
            $resource['scope'],
        );
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'resource' => $schema->string()
                ->enum(ApiResourceRegistry::keys())
                ->description('The resource the record belongs to.')
                ->required(),
            'id' => $schema->integer()->min(1)->description('The record ID.')->required(),
            'include' => $schema->array()
                ->items($schema->string())
                ->description('Related data to expand, e.g. ["rank", "unit", "award_records.award"] for a user.'),
        ];
    }
}
