<?php

declare(strict_types=1);

namespace App\Pipes;

use App\Facades\ContentTagParser;
use Closure;
use Illuminate\Database\Eloquent\Model;

class ParseTextForContentTags
{
    public function __construct(protected Model $attachedModel) {}

    public function __invoke(string $text, Closure $next): string
    {
        return $next(ContentTagParser::parse(
            content: $text,
            attachedModel: $this->attachedModel
        ));
    }
}
