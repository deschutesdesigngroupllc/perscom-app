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

#[Name('search_resource')]
#[Title('Search Resource')]
#[Description('Search, filter and sort records of a PERSCOM resource, such as users, ranks or award-records. Results are paginated. Use list_resources to see which fields each resource accepts for filters, sort, search and include.')]
#[IsReadOnly]
#[IsIdempotent]
#[IsOpenWorld(false)]
class SearchResourceTool extends Tool
{
    use RespondsWithApiResults;

    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'resource' => ['required', 'string', 'in:'.implode(',', ApiResourceRegistry::keys())],
            'filters' => ['nullable', 'array'],
            'filters.*.field' => ['required', 'string'],
            'filters.*.operator' => ['nullable', 'string'],
            'filters.*.value' => ['nullable'],
            'filters.*.type' => ['nullable', 'string', 'in:and,or'],
            'search' => ['nullable', 'string'],
            'sort' => ['nullable', 'array'],
            'sort.*.field' => ['required', 'string'],
            'sort.*.direction' => ['nullable', 'string', 'in:asc,desc'],
            'include' => ['nullable', 'array'],
            'include.*' => ['string'],
            'page' => ['nullable', 'integer', 'min:1'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ], [
            'resource.in' => 'Unknown resource. Call list_resources to see the available resources.',
        ]);

        /** @var array{endpoint: string, scope: string} $resource */
        $resource = ApiResourceRegistry::find($validated['resource']);

        $body = array_filter([
            'filters' => collect($validated['filters'] ?? [])
                ->map(fn (array $filter): array => array_filter([
                    'field' => $filter['field'],
                    'operator' => $filter['operator'] ?? '=',
                    'value' => $filter['value'] ?? null,
                    'type' => $filter['type'] ?? null,
                ], fn (mixed $value): bool => ! is_null($value)))
                ->all(),
            'search' => filled($validated['search'] ?? null) ? ['value' => $validated['search']] : null,
            'sort' => collect($validated['sort'] ?? [])
                ->map(fn (array $sort): array => [
                    'field' => $sort['field'],
                    'direction' => $sort['direction'] ?? 'asc',
                ])
                ->all(),
        ], fn (mixed $value): bool => filled($value));

        $query = array_filter([
            'include' => filled($validated['include'] ?? null) ? implode(',', $validated['include']) : null,
            'page' => $validated['page'] ?? null,
            'limit' => $validated['limit'] ?? 25,
        ], fn (mixed $value): bool => filled($value));

        return $this->respondWithApiResult(
            InternalApiService::post($resource['endpoint'].'/search', $body, $query),
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
                ->description('The resource to search.')
                ->required(),
            'filters' => $schema->array()
                ->items($schema->object([
                    'field' => $schema->string()->description('A filterable field, e.g. "name" or "rank.name".')->required(),
                    'operator' => $schema->string()
                        ->enum(['<', '<=', '>', '>=', '=', '!=', 'like', 'not like', 'in', 'not in'])
                        ->description('Defaults to "=". Use "like" with % wildcards for partial matches and "in" with a list of values.'),
                    'value' => $schema->union(['string', 'number', 'boolean', 'array', 'null'])->description('The value to compare against.'),
                    'type' => $schema->string()->enum(['and', 'or'])->description('How this filter combines with the previous one. Defaults to "and".'),
                ]))
                ->description('Conditions every returned record must match.'),
            'search' => $schema->string()->description('Free-text search across the resource\'s searchable fields.'),
            'sort' => $schema->array()
                ->items($schema->object([
                    'field' => $schema->string()->description('A sortable field.')->required(),
                    'direction' => $schema->string()->enum(['asc', 'desc'])->description('Defaults to "asc".'),
                ]))
                ->description('Sort order, applied in sequence.'),
            'include' => $schema->array()
                ->items($schema->string())
                ->description('Related data to expand, e.g. ["rank", "unit"] for users.'),
            'page' => $schema->integer()->min(1)->description('The page of results to return. Defaults to 1.'),
            'limit' => $schema->integer()->min(1)->max(100)->description('Records per page, up to 100. Defaults to 25.'),
        ];
    }
}
