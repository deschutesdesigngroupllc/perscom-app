<?php

declare(strict_types=1);

namespace App\Mcp\Tools;

use App\Mcp\ApiResourceRegistry;
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

#[Name('list_resources')]
#[Title('List Resources')]
#[Description('List the PERSCOM resources that can be read, with the fields each one can be filtered, sorted, searched and expanded (included) by. Call this first to learn what search_resource and get_resource accept.')]
#[IsReadOnly]
#[IsIdempotent]
#[IsOpenWorld(false)]
class ListResourcesTool extends Tool
{
    public function handle(Request $request): Response
    {
        $validated = $request->validate([
            'resource' => ['nullable', 'string', 'in:'.implode(',', ApiResourceRegistry::keys())],
        ]);

        $resources = collect(ApiResourceRegistry::all())
            ->when(filled($validated['resource'] ?? null), fn ($resources) => $resources->only($validated['resource']))
            ->map(fn (array $resource, string $key): array => [
                'resource' => $key,
                'description' => $resource['description'],
                'required_scope' => $resource['scope'],
                ...ApiResourceRegistry::capabilities($key),
            ])
            ->values()
            ->all();

        return Response::json(['resources' => $resources]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'resource' => $schema->string()
                ->enum(ApiResourceRegistry::keys())
                ->description('Only describe this resource. Leave empty to list every resource.'),
        ];
    }
}
