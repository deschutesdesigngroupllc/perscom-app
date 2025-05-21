<?php

declare(strict_types=1);

namespace App\Facades;

use App\Models\User;
use App\Services\ContentTagParserService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string|null parse(string $content, ?User $user = null, ?Model $attachedModel = null)
 */
class ContentTagParser extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ContentTagParserService::class;
    }
}
