<?php

namespace Perscom\DocumentViewerTool;

use Laravel\Nova\ResourceTool;

class DocumentViewerTool extends ResourceTool
{
    public function name(): string
    {
        return 'Document Viewer Tool';
    }

    public function component(): string
    {
        return 'document-viewer-tool';
    }

    public function withTitle(?string $title = null): DocumentViewerTool
    {
        return $this->withMeta([
            'title' => $title ?? null,
        ]);
    }

    public function withContent(?string $content = null): DocumentViewerTool
    {
        return $this->withMeta([
            'content' => $content ?? null,
        ]);
    }
}
