<?php

declare(strict_types=1);

namespace App\Pipes;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use League\HTMLToMarkdown\HtmlConverter;

class ConvertHtmlToMarkdown
{
    public function __construct(protected HtmlConverter $htmlConverter)
    {
        $this->htmlConverter->setOptions([
            'strip_tags' => true,
            'remove_nodes' => true,
        ]);
    }

    public function __invoke(string $html, Closure $next): string
    {
        $markdown = Str::of($html)
            ->replaceMatches('/<@(\d+)>/', function ($matches) {
                return '{{MENTION_'.$matches[1].'}}';
            })
            ->pipe(fn (Stringable $content) => $this->htmlConverter->convert($content->toString()))
            ->replaceMatches('/\{\{MENTION\\\\_(\d+)\}\}/', '<@$1>')
            ->toString();

        return $next($markdown);
    }
}
