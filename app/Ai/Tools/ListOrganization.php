<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Award;
use App\Models\Position;
use App\Models\Qualification;
use App\Models\Rank;
use App\Models\Specialty;
use App\Models\Status;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\Database\Eloquent\Model;
use JsonException;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ListOrganization implements Tool
{
    /**
     * @var array<string, class-string<Model>>
     */
    public const array TYPES = [
        'ranks' => Rank::class,
        'units' => Unit::class,
        'positions' => Position::class,
        'specialties' => Specialty::class,
        'awards' => Award::class,
        'qualifications' => Qualification::class,
        'statuses' => Status::class,
    ];

    public function __construct(protected User $actor) {}

    public function description(): Stringable|string
    {
        return "List the organization's ranks, units, positions, specialties, awards, qualifications or statuses with their ids. Use this to find the id of something the user mentions by name.";
    }

    /**
     * Return the records of the requested type as JSON.
     *
     * @throws JsonException when the records cannot be encoded
     */
    public function handle(Request $request): Stringable|string
    {
        $model = static::TYPES[$request->string('type')->toString()] ?? null;

        if ($model === null) {
            return 'Unknown type. Use one of: '.collect(static::TYPES)->keys()->implode(', ').'.';
        }

        if (! $this->actor->can('viewAny', $model)) {
            return 'You are not allowed to view these.';
        }

        return json_encode($model::query()
            ->orderBy('order')
            ->orderBy('name')
            ->get()
            ->map(fn (Model $item): array => collect([
                'id' => $item->getKey(),
                'name' => $item->getAttribute('name'),
                'abbreviation' => $item->getAttribute('abbreviation'),
                'paygrade' => $item->getAttribute('paygrade'),
                'order' => $item->getAttribute('order'),
            ])->filter(filled(...))->all())
            ->all(), JSON_THROW_ON_ERROR);
    }

    /**
     * @return \Illuminate\JsonSchema\Types\StringType[]
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'type' => $schema->string()->enum(array_keys(static::TYPES))->description('What to list.')->required(),
        ];
    }
}
